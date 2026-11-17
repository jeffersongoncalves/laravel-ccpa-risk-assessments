<?php

namespace JeffersonGoncalves\CcpaRiskAssessments\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Ccpa\Attributes\PersonalData;
use JeffersonGoncalves\Ccpa\Concerns\InteractsWithPersonalData;
use JeffersonGoncalves\Ccpa\Contracts\HasPersonalData;

#[PersonalData('email', retention: '5 years')]
#[PersonalData('tax_id')]
class Customer extends Model implements HasPersonalData
{
    use InteractsWithPersonalData;

    protected $table = 'customers';
}
