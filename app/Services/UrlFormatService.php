<?php

namespace App\Services;

class UrlFormatService {
    const USE_NUMBERS_TRUE = true;
    const USE_NUMBERS_FALSE = false;
    const USE_LOWERCASE_TRUE = true;
    const USE_LOWERCASE_FALSE = false;

    public function construct() {}


    public function formatNewUrl($type, $contentDomain, $emailIdField, $linkId) {
        $suffix = 'REDIRECT' === $type ? 'R' : 'A';

        $redirRandomString = $this->randomString(6, 9, self::USE_NUMBERS_TRUE, self::USE_LOWERCASE_TRUE);
        $redirRandomString = strtolower($redirRandomString);
        return "http://$contentDomain/z/$redirRandomString/$emailIdField|1|$linkId|$suffix";
    }


    public function formatGmailUrl($type, $contentDomain, $emailIdField, $linkId) {
        // generate a random number and random letter string
        // e.g. "http://yourcontentdomain.com/7U4R/jqbjlsfx/%%cf_EID%%|cjztnj|29541380|933736"

        $randString1 = $this->randomString(4, 0, self::USE_NUMBERS_TRUE, self::USE_LOWERCASE_TRUE);
        $randString1 = ucfirst($randString1);

        $randString2 = $this->randomString(6, 9, self::USE_NUMBERS_TRUE, self::USE_LOWERCASE_TRUE);
        $randString2 = strtolower($randString2);

        $randString3 = $this->randomString(6, 9, self::USE_NUMBERS_TRUE, self::USE_LOWERCASE_TRUE);
        $randString3 = strtolower($randString3);

        if ('REDIRECT' === $type) {
            $endingString = $this->randomDigits(0, 999999);
        }
        else {
            $endingString = $this->randomString(4, 4, self::USE_NUMBERS_FALSE, self::USE_LOWERCASE_TRUE);
        }
        
        return "http://$contentDomain/$randString1/$randString2/$emailIdField|$randString3|$linkId|$endingString";
    }

    public function formatOpenUrl ( $contentDomain , $esp , $espAccount , $deployId , $emailIdField , $emailAddressField ) {
        $endingString = $this->randomDigits(0, 999999);

        $espAccountId = ( isset( $espAccount->custom_id ) ? $espAccount->custom_id : $espAccount->id );

        return "http://{$contentDomain}/{$esp->nickname}/{$espAccountId}/{$deployId}/{$emailIdField}/spacer{$endingString}.png?em={$emailAddressField}";
    }

    public function getDefinedRandomString() {
        return $this->randomString(6, 9, self::USE_NUMBERS_TRUE, self::USE_LOWERCASE_TRUE);
    }


    private function randomDigits($min, $range) {
        return mt_rand($min, $min + $range);
    }


    private function randomString($minLength, $range, $useNumbers = false, $useLowercase = false) {
        $choices = [];
        $length = $this->randomDigits($minLength, $range); // even the length is randomized
        $return = '';

        // This works by adding the ordinal values for these characters

        // adding upper case letters by default
        for ($i = 65; $i <= 90; $i++) {
            $choices[] = $i;
        }

        if ($useNumbers) {
            for ($i = 48; $i <= 57; $i++) {
                $choices[] = $i;
            }
        }

        if ($useLowercase) {
            for ($i = 97; $i <= 122; $i++) {
                $choices[] = $i;
            }
        }

        $choicesLength = sizeof($choices);

        // pick $length random values from the choices array
        for ($i = 0; $i < $length; $i++) {
            $key = mt_rand(0, $choicesLength - 1);
            $return .= chr($choices[$key]); // return from ordinals back to characters
        }

        return $return;
    }
}
