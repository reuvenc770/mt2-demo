<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;

class GenderValidator implements IValidate {

    private $gender;
    private $firstName;
    private $nameGenderRepo;

    public function __construct(NameGenderRepo $nameGenderRepo) {
        $this->nameGenderRepo = $nameGenderRepo;
    }

    public function getRequiredData() {
        return ['gender', 'first_name'];
    }

    public function setData(array $data) {
        $this->gender = $data['gender'];
        $this->firstName = $data['first_name'];
    }

    public function validate() {
        $this->gender = strtoupper($this->gender);

        if ('M' !== $this->gender && 'F' !== $this->gender && '' !== $this->gender) {
            if (preg_match('/^MALE$|^HERR$|^SENIOR$|^SR$|^MR$/')) {
                $this->gender = 'M';
            }
            elseif (preg_match('/^FEMALE$|^FRAU$|^SENIORITA$|^SENIORA$|^SRA$|^MRS$|^MS$/', $this->gender)) {
                $this->gender = 'F';
            }
            else {
                // Try to guess based off of first name
                // maybe make this a cached list
                $this->gender = $this->nameGenderRepo->getGender($firstName);
            }
        }
    }

    public function returnData() {
        return ['gender' => $this->gender, 'first_name' => $this->firstName];
    }

}