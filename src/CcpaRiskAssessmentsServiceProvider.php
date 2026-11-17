<?php

namespace JeffersonGoncalves\CcpaRiskAssessments;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CcpaRiskAssessmentsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ccpa-risk-assessments')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
