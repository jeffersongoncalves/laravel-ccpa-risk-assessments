<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Ccpa\Attributes\PersonalData;
use JeffersonGoncalves\Ccpa\Concerns\InteractsWithPersonalData;
use JeffersonGoncalves\Ccpa\Contracts\HasPersonalData;
use JeffersonGoncalves\Ccpa\Enums\DataCategory;

#[PersonalData('name')]
#[PersonalData('diagnosis', category: DataCategory::Sensitive)]
class Patient extends Model implements HasPersonalData
{
    use InteractsWithPersonalData;

    protected $table = 'patients';
}
