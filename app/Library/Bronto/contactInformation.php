<?php

class contactInformation
{

    /**
     * @var string $organization
     */
    protected $organization = null;

    /**
     * @var string $firstName
     */
    protected $firstName = null;

    /**
     * @var string $lastName
     */
    protected $lastName = null;

    /**
     * @var string $email
     */
    protected $email = null;

    /**
     * @var string $phone
     */
    protected $phone = null;

    /**
     * @var string $address
     */
    protected $address = null;

    /**
     * @var string $address2
     */
    protected $address2 = null;

    /**
     * @var string $city
     */
    protected $city = null;

    /**
     * @var string $state
     */
    protected $state = null;

    /**
     * @var string $zip
     */
    protected $zip = null;

    /**
     * @var string $country
     */
    protected $country = null;

    /**
     * @var string $notes
     */
    protected $notes = null;

    /**
     * @param string $organization
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     * @param string $address
     * @param string $address2
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     * @param string $notes
     */
    public function __construct($organization, $firstName, $lastName, $email, $phone, $address, $address2, $city, $state, $zip, $country, $notes)
    {
      $this->organization = $organization;
      $this->firstName = $firstName;
      $this->lastName = $lastName;
      $this->email = $email;
      $this->phone = $phone;
      $this->address = $address;
      $this->address2 = $address2;
      $this->city = $city;
      $this->state = $state;
      $this->zip = $zip;
      $this->country = $country;
      $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
      return $this->organization;
    }

    /**
     * @param string $organization
     * @return contactInformation
     */
    public function setOrganization($organization)
    {
      $this->organization = $organization;
      return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
      return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return contactInformation
     */
    public function setFirstName($firstName)
    {
      $this->firstName = $firstName;
      return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
      return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return contactInformation
     */
    public function setLastName($lastName)
    {
      $this->lastName = $lastName;
      return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * @param string $email
     * @return contactInformation
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
      return $this->phone;
    }

    /**
     * @param string $phone
     * @return contactInformation
     */
    public function setPhone($phone)
    {
      $this->phone = $phone;
      return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
      return $this->address;
    }

    /**
     * @param string $address
     * @return contactInformation
     */
    public function setAddress($address)
    {
      $this->address = $address;
      return $this;
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
      return $this->address2;
    }

    /**
     * @param string $address2
     * @return contactInformation
     */
    public function setAddress2($address2)
    {
      $this->address2 = $address2;
      return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
      return $this->city;
    }

    /**
     * @param string $city
     * @return contactInformation
     */
    public function setCity($city)
    {
      $this->city = $city;
      return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
      return $this->state;
    }

    /**
     * @param string $state
     * @return contactInformation
     */
    public function setState($state)
    {
      $this->state = $state;
      return $this;
    }

    /**
     * @return string
     */
    public function getZip()
    {
      return $this->zip;
    }

    /**
     * @param string $zip
     * @return contactInformation
     */
    public function setZip($zip)
    {
      $this->zip = $zip;
      return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
      return $this->country;
    }

    /**
     * @param string $country
     * @return contactInformation
     */
    public function setCountry($country)
    {
      $this->country = $country;
      return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
      return $this->notes;
    }

    /**
     * @param string $notes
     * @return contactInformation
     */
    public function setNotes($notes)
    {
      $this->notes = $notes;
      return $this;
    }

}
