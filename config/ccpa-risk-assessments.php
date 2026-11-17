<?php

return [

    'table' => 'ccpa_risk_assessments',

    /*
     * Review and update the assessment at least once every three years, and within 45 days of a
     * material change in the processing (CCPA Regulations §7155). CarbonInterval string.
     */
    'review_every' => '3 years',

];
