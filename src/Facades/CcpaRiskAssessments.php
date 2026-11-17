<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\CcpaRiskAssessments\CcpaRiskAssessments
 */
class CcpaRiskAssessments extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-ccpa-risk-assessments';
    }
}
