<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Ccpa\Attributes\PersonalData;
use JeffersonGoncalves\Ccpa\Concerns\InteractsWithPersonalData;
use JeffersonGoncalves\Ccpa\Contracts\HasPersonalData;

#[PersonalData('name')]
#[PersonalData('browsing', sold: true)]
class Applicant extends Model implements HasPersonalData
{
    use InteractsWithPersonalData;

    protected $table = 'applicants';
}
