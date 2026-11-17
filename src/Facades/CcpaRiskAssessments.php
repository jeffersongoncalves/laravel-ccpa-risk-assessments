<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\CcpaRiskAssessments\RiskAssessmentManager;

/**
 * @method static list<array{activity: \JeffersonGoncalves\CcpaNotices\Models\ProcessingActivity, reasons: list<string>}> candidates()
 * @method static array<string, mixed> export(\JeffersonGoncalves\CcpaRiskAssessments\Models\RiskAssessment $assessment)
 *
 * @see RiskAssessmentManager
 */
class CcpaRiskAssessments extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RiskAssessmentManager::class;
    }
}
