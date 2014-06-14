<?php
/**
 * Class Insurance
 *
 */

class Insurance extends Eloquent
{
    protected $table = 'insurances';

    public function insuranceLog()
    {
        return $this->hasOne('InsuranceLog');
    }

    public function serviceInsurance()
    {
        return $this->hasMany('ServiceInsurance');
    }
}
