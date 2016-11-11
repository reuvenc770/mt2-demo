<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;

class EmailValidator implements IValidate {
    
    private $emailAddress;
    private $invalidTlds;
    private $invalidSecondaryTlds;

    const BAD_ALIASES = [
        'bulk','admin','editor','feedback','general','info','mail','marketing',
        'mailerdaemon','news','noreply','online','president','press',
        'pressrelease','privacy','promo','public','publisher','reply','reservations',
        'sales','support','update','updates','support','abuse','attorney','blacklist',
        'blighty','blockme','contact','cyberfraud','devnull','dns','administrator',
        'netadmin','postmaster','webmaster','customerservice','info','sales',
        'contact','customer_service','customer_support'
    ];

    const INVALID_TLDS = [ 'ca', 'gov', 'org', 'edu', 'mil', 'us'];

    private $domainMap = [];
    private $invalidDomains = [];


    public function __construct(array $domainMap, array $invalidDomains) {
        $this->domainMap = $domainMap;
        $this->invalidDomains = $invalidDomains;
    }


    public function getRequiredData() {
        return ['email_address'];
    }


    public function setData(array $data) {
        $this->emailAddress = $data['email_address'];
    }


    public function validate() {

        $this->emailAddress = $this->normalize($this->emailAddress);

        if (strlen($this->emailAddress) > 50) {
            throw new ValidationException("Email is invalid - length {$this->emailAddress}");
        }

        if (!filter_var($this->emailAddress, FILTER_VALIDATE_EMAIL)) {
            // Basic built-in email format validation
            throw new ValidationException("Email address invalid - incorrect format {$this->emailAddress}");
        }

        $address = $this->getAddress($this->emailAddress);
        $domain = $this->getDomain($this->emailAddress);

        if ($this->isDomainInvalid($domain)) {
             throw new ValidationException("Email address invalid - invalid domain {$this->emailAddress}");
        }

        if ($this->isTldInvalid($domain)) {
            throw new ValidationException("Email address invalid - suppressed TLD in domain {$this->emailAddress}");
        }

        if ($this->isBadAlias($address)) {
            throw new ValidationException("Email address invalid - banned alias {$this->emailAddress}");
        }

        if ($this->isSuppressedDomain($domain)) {
            throw new ValidationException("Email address invalid - suppressed domain {$this->emailAddress}");
        }

        if ($this->hasBannedWords($this->emailAddress)) {
            throw new ValidationException("Email address invalid - contains obscene language {$this->emailAddress}");
        }

        // Per-email provider validation rules
        if (!$this->isValidForEmailProviderRules($address, $domain)) {
            throw new ValidationException("Email address invalid - does not pass ISP validation {$this->emailAddress}");
        }

    }

    public function returnData() {
        return ['email_address' => $this->emailAddress];
    }


    private function normalize($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return strtolower($email);
    }


    private function getDomain($email) {
        $parts = explode('@', $email);
        return $parts[1];
    }


    private function getAddress($email) {
        $parts = explode('@', $email);
        return $parts[0];
    }


    private function isTldInvalid($domain) {
        foreach(self::INVALID_TLDS as $tld) {
            if (preg_match("/\.{$tld}$/", $domain)) {
                return true;
            }
        }

        $domainArray = explode('.', $domain);

        if (sizeof($domainArray) > 2) {
            // Example, @site.org.uk
            $secondaryTld = $domainArray[1];
            foreach (self::INVALID_TLDS as $tld) {
                if ($tld === $secondaryTld) {
                    return true;
                }
            }
        }

        return false;
    }


    private function isBadAlias($address) {
        return in_array($address, self::BAD_ALIASES);
    }


    private function hasBannedWords($address) {
        if (
            preg_match('/www\./', $address)
            || preg_match('/fag/', $address)
            || preg_match('/crap/', $address)
            || preg_match('/bitch/', $address)
            || preg_match('/shit/', $address)
            || preg_match('/seed/', $address)
            || preg_match('/spam/', $address)
            || preg_match('/jizz/', $address)
            || preg_match('/nigga/', $address)
            || preg_match('/slut/', $address)
            || preg_match('/lawyer/', $address)
            || preg_match('/piss/', $address)
            || preg_match('/pussy/', $address)
            || preg_match('/fuck/', $address)
            || preg_match('/balsa/', $address)
            || preg_match('/penis/', $address)
            || preg_match('/attorney/', $address)
            || preg_match('/whore/', $address)
            || preg_match('/bastard/', $address)
            || preg_match('/douche/', $address)
            || preg_match('/cracka/', $address)
            || preg_match('/wanker/', $address)
            || preg_match('/vagina/', $address)
            || preg_match('/titties/', $address)
            || preg_match('/cunt/', $address)
            || preg_match('/nigger/', $address)
            || preg_match('/goddamn/', $address)
            || preg_match('/jerkoff/', $address)

        ) {
            return true;
        }
        elseif (preg_match('/dick/', $address)) {
            if (preg_match('/suck/', $address)
                || preg_match('/my/', $address)
                || preg_match('/eat/', $address)
                || preg_match('/gay/', $address)
                || preg_match('/big/', $address)
                || preg_match('/tiny/', $address)
            ) {
                return true;
            }
        }
        elseif (preg_match('/cock/', $address)) {
            if (preg_match('/suck/', $address)
                || preg_match('/my/', $address)
                || preg_match('/eat/', $address)
                || preg_match('/gay/', $address)
                || preg_match('/big/', $address)
                || preg_match('/tiny/', $address)
            ) {
                return true;
            }
        }
        else {
            return false;
        }
    }


    private function isValidForEmailProviderRules($address, $domain) {
        // pick correct set of rules based off of domain:
        $domainClass = $this->domainMap[$domain];

        if ('AOL' === $domainClass) {
            return $this->isValidAolEmail($address);
        }
        elseif ('Yahoo' === $domainClass) {
            return $this->isValidYahooEmail($address);
        }
        elseif ('Hotmail' === $domainClass) {
            return $this->isValidHotmailEmail($address);
        }
        elseif ('Gmail' === $domainClass) {
            return $this->isValidGMailEmail($address);
        }

        return true;
    }


    private function isValidAolEmail($address) {
        $size = strlen($address);

        if ($size < 3 || $size > 32) {
            return false;
        }

        // already lowercase
        if (!preg_match('/^[a-z0-9\.\_]+$/', $address)) {
            return false;
        }

        return true;
    }


    private function isValidYahooEmail($address) {
        $size = strlen($address);

        if ($size < 3 || $size > 32) {
            return false;
        }

        // maximum of one period allowed
        if (!preg_match('/^[a-z0-9\_]+$/', $address) || (preg_match('/^[a-z0-9\_]+$/', $address) && substr_count($address, '.') > 1)) {
            return false;
        }

        return true;
    }


    private function isValidHotmailEmail($address) {
        $size = strlen($address);

        if ($size < 2 || $size > 64) {
            return false;
        }

        if (!preg_match('/^[a-z0-9\.\_\-]+$/', $address)) {
            return false;
        }

        return true;
    }


    private function isValidGMailEmail($address) {
        $size = strlen($address);

        if ($size < 6 || $size > 30) {
            return false;
        }

        // GMail allows +, but we don't want addresses with it
        if (!preg_match('/^[a-z0-9\.]+$/', $address)) {
            return false;
        }

        return true;
    }


    private function isSuppressedDomain($domain) {
        return in_array($domain, $this->invalidDomains);
    }

}
