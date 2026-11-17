<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Tests;

use JeffersonGoncalves\Ccpa\CcpaServiceProvider;
use JeffersonGoncalves\CcpaNotices\CcpaNoticesServiceProvider;
use JeffersonGoncalves\CcpaRiskAssessments\CcpaRiskAssessmentsServiceProvider;
use JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures\Applicant;
use JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures\Customer;
use JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures\Patient;
use JeffersonGoncalves\PestPluginDatabaseMigrations\LoadsPackageMigrations;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use LoadsPackageMigrations;

    protected function getPackageProviders($app): array
    {
        return [
            CcpaServiceProvider::class,
            CcpaNoticesServiceProvider::class,
            CcpaRiskAssessmentsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('ccpa.models', [Customer::class, Patient::class, Applicant::class]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadPackageMigrations(CcpaNoticesServiceProvider::class);
        $this->loadPackageMigrations(CcpaRiskAssessmentsServiceProvider::class);
    }
}
