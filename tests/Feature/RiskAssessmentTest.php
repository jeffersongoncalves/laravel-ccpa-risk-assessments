<?php

use Illuminate\Support\Facades\Artisan;
use JeffersonGoncalves\CcpaNotices\Models\ProcessingActivity;
use JeffersonGoncalves\CcpaRiskAssessments\Enums\AssessmentStatus;
use JeffersonGoncalves\CcpaRiskAssessments\Enums\RiskLevel;
use JeffersonGoncalves\CcpaRiskAssessments\Enums\Threat;
use JeffersonGoncalves\CcpaRiskAssessments\Facades\CcpaRiskAssessments;
use JeffersonGoncalves\CcpaRiskAssessments\Models\RiskAssessment;
use JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures\Applicant;
use JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures\Customer;
use JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures\Patient;

function activity(string $name, array $models): ProcessingActivity
{
    return ProcessingActivity::create(['name' => $name, 'purpose' => $name, 'models' => $models]);
}

function assessment(?ProcessingActivity $activity = null): RiskAssessment
{
    return RiskAssessment::create([
        'title' => 'Telemedicine',
        'processing_activity_id' => $activity?->getKey(),
        'risks' => [
            ['title' => 'Diagnosis leak', 'threat' => 'data_disclosure', 'likelihood' => '2', 'impact' => '3', 'measures' => 'Encryption at rest', 'residual_likelihood' => '1', 'residual_impact' => '3'],
            ['title' => 'Re-identification in reports', 'threat' => 'identifying', 'likelihood' => 1, 'impact' => 2],
        ],
    ]);
}

it('scores risks by likelihood and impact, before and after measures', function () {
    $risks = assessment()->scoredRisks();

    expect($risks[0])
        ->threat->toBe(Threat::DataDisclosure)
        ->level->toBe(RiskLevel::High)
        ->residual_level->toBe(RiskLevel::Medium)
        ->and($risks[1]['residual_level'])->toBe(RiskLevel::Low)
        ->and(assessment()->highestRisk(residual: false))->toBe(RiskLevel::High)
        ->and(assessment()->highestRisk())->toBe(RiskLevel::Medium)
        ->and(RiskLevel::score(3, 3))->toBe(RiskLevel::High)
        ->and(fn () => RiskLevel::score(4, 1))->toThrow(InvalidArgumentException::class);
});

it('approves, schedules a review and returns to draft when edited', function () {
    $dpia = assessment()->approve('Privacy lead');

    expect($dpia->status)->toBe(AssessmentStatus::Approved)
        ->and($dpia->next_review_at->toDateString())->toBe(now()->addYears(3)->toDateString());

    $this->travel(37)->months();

    expect($dpia->isReviewDue())->toBeTrue()
        ->and(RiskAssessment::query()->scopes('dueForReview')->count())->toBe(1);

    $dpia->update(['necessity' => 'Only the data needed for the appointment']);

    expect($dpia->fresh())
        ->status->toBe(AssessmentStatus::Draft)
        ->approved_by->toBeNull()
        ->isReviewDue()->toBeFalse();
});

it('needs risks before approval', function () {
    RiskAssessment::create(['title' => 'Empty'])->approve('DPO');
})->throws(LogicException::class, 'at least one risk');

it('lists activities that may need an assessment', function () {
    $health = activity('Telemedicine', [Patient::class]);
    activity('Billing', [Customer::class]);
    activity('Ad targeting', [Applicant::class]);

    expect(collect(CcpaRiskAssessments::candidates())->map(fn (array $c) => [$c['activity']->name, $c['reasons']])->all())->toBe([
        ['Ad targeting', ['selling_sharing']],
        ['Telemedicine', ['sensitive_pi']],
    ]);

    assessment($health);

    expect(CcpaRiskAssessments::candidates())->toHaveCount(1);
});

it('reports assessments and candidates', function () {
    assessment(activity('Telemedicine', [Patient::class]))->approve('DPO');
    activity('Ad targeting', [Applicant::class]);

    Artisan::call('ccpa:risk-assessments');
    expect(Artisan::output())->toContain('Telemedicine')->toContain('high (medium)')->toContain('No risk assessment for [Ad targeting]: selling_sharing');

    Artisan::call('ccpa:risk-assessments', ['--json' => true]);
    expect(json_decode(Artisan::output(), true)[0])
        ->processing_activity->toBe('Telemedicine')
        ->highest_residual_risk->toBe('medium')
        ->risks->{0}->threat->toBe('data_disclosure');
});

it('records the submission to the CPPA', function () {
    $assessment = RiskAssessment::create(['title' => 'Ad targeting', 'risks' => [['title' => 'Profiling', 'likelihood' => 2, 'impact' => 2]]]);

    $assessment->markSubmitted();

    expect($assessment->fresh()->submitted_at)->not->toBeNull()
        ->and(CcpaRiskAssessments::export($assessment->fresh())['submitted_at'])->not->toBeNull();
});
