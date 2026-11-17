<?php

namespace JeffersonGoncalves\CcpaRiskAssessments;

use JeffersonGoncalves\CcpaRiskAssessments\Commands\RiskAssessmentsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CcpaRiskAssessmentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ccpa-risk-assessments')
            ->hasConfigFile()
            ->hasMigrations(['create_ccpa_risk_assessments_table'])
            ->hasCommand(RiskAssessmentsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(RiskAssessmentManager::class);
    }
}
