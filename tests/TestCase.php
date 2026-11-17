<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Tests;

use JeffersonGoncalves\CcpaRiskAssessments\CcpaRiskAssessmentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CcpaRiskAssessmentsServiceProvider::class,
        ];
    }
}
