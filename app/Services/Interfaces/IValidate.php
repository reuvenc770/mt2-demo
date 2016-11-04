<?php

namespace App\Services\Interfaces;

interface IValidate
{
    /**
     *  Get the field names, in the form of an array
     */
    public function getRequiredData();

    /**
     *  Set the data via an array, with the keys being the same as the array returned by getRequiredData()
     */

    public function setData(array $data);

    /**
     *  Validation method with IO Void type - will return nothing on success, throws exception on error. May also handle normalization of data.
     */

    public function validate();

    /**
     *  Return data in the format accepted in setData()
     */

    public function returnData();
}