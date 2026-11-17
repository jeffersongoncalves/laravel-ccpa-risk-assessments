<?php

namespace JeffersonGoncalves\CcpaRiskAssessments;

use JeffersonGoncalves\Ccpa\Ccpa;
use JeffersonGoncalves\Ccpa\Enums\DataCategory;
use JeffersonGoncalves\CcpaNotices\Models\ProcessingActivity;
use JeffersonGoncalves\CcpaRiskAssessments\Models\RiskAssessment;

class RiskAssessmentManager
{
    public function __construct(protected Ccpa $ccpa) {}

    /**
     * Processing activities with no assessment that present a significant risk to consumers' privacy
     * (CCPA Regulations §7150(b)): selling or sharing personal information, and processing sensitive
     * personal information. Automated decision-making for significant decisions, profiling of workers or
     * students, and training such technology still need your judgment.
     *
     * @return list<array{activity: ProcessingActivity, reasons: list<string>}>
     */
    public function candidates(): array
    {
        $inventory = collect($this->ccpa->inventory());
        $sellingModels = $inventory->filter(fn (array $row): bool => $row['sold'] || $row['shared'])->pluck('model')->unique()->all();
        $sensitiveModels = $inventory->where('category', DataCategory::Sensitive->value)->pluck('model')->unique()->all();

        $assessed = RiskAssessment::query()->whereNotNull('processing_activity_id')->pluck('processing_activity_id')->all();

        $candidates = [];

        foreach (ProcessingActivity::query()->whereNotIn('id', $assessed)->orderBy('name')->get() as $activity) {
            $reasons = [];

            if (array_intersect($activity->models ?? [], $sellingModels) !== []) {
                $reasons[] = 'selling_sharing';
            }

            if (array_intersect($activity->models ?? [], $sensitiveModels) !== []) {
                $reasons[] = 'sensitive_pi';
            }

            if ($reasons !== []) {
                $candidates[] = ['activity' => $activity, 'reasons' => $reasons];
            }
        }

        return $candidates;
    }

    /**
     * @return array<string, mixed>
     */
    public function export(RiskAssessment $assessment): array
    {
        return [
            'title' => $assessment->title,
            'processing_activity' => $assessment->processingActivity?->name,
            'description' => $assessment->description,
            'necessity' => $assessment->necessity,
            'risks' => array_map(fn (array $risk): array => [
                ...$risk,
                'threat' => $risk['threat']?->value,
                'level' => $risk['level']->value,
                'residual_level' => $risk['residual_level']->value,
            ], $assessment->scoredRisks()),
            'highest_residual_risk' => $assessment->highestRisk()?->value,
            'reviewer_opinion' => $assessment->reviewer_opinion,
            'status' => $assessment->status->value,
            'approved_at' => $assessment->approved_at?->toIso8601String(),
            'approved_by' => $assessment->approved_by,
            'next_review_at' => $assessment->next_review_at?->toIso8601String(),
            'submitted_at' => $assessment->submitted_at?->toIso8601String(),
        ];
    }
}
