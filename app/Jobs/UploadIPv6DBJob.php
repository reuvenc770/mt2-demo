<?php

namespace App\Jobs;

use Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\Ipv6CountryMappingRepo;
use App\Repositories\RepoTraits\IPv6;

class UploadIPv6DBJob extends Job implements ShouldQueue {

    use InteractsWithQueue, SerializesModels, IPv6;

    private $fileName;

    public function __construct($fileName) {
        $this->fileName = $fileName;
    }

    public function handle(Ipv6CountryMappingRepo $repo) {
        // Table to be truncated. Make sure that record processing is stopped while this is running.
        $repo->clearTable();

        $contents = Storage::get($this->fileName);
        // each line has the following format:
        // "2801:80:1b90::", "2801:80:1b90:ffff:ffff:ffff:ffff:ffff", "53174322277990152152816848337895424000", "53174322277991361078636462967070130175", "BR", "Brazil"
        // or
        // from, to, (likely base 10 from), (likely base 10 to), country code, country name
        // Unfortunately we can't use the base 10 representation - 
        // mysql stops at the maximum unsigned bigint size when converting, which is well below the size of these numbers
        // but we can split the number physically in half and use an unsigned bigint for each half
        // each half is, at most, ffff:ffff:ffff:ffff which exactly equals the maximum size of an unsigned bigint

        $lines = explode(PHP_EOL, $contents);

        foreach ($lines as $line) {
            $contents = explode(",", $line);
            if (count($contents) > 1) {
                $processedContents = $this->processContents($contents);
                $repo->batchInsert($processedContents);
            }
        }

        $repo->insertStored();
    }

    private function processContents(array $contents) {
        /*
            0 is from address
            1 is to address
            2 is from decimal
            3 is to decimal
            4 is country code
            5 is full country name 
        */        

        $firstIp = trim($this->stripQuotes($contents[0]));
        $secondIp = trim($this->stripQuotes($contents[1]));

        $firstIp = $this->validateIp($firstIp);
        $secondIp = $this->validateIp($secondIp);

        $firstIp = $this->splitIp($firstIp);
        $secondIp = $this->splitIp($secondIp);

        return [
            'first_half_from' => $firstIp[0],
            'second_half_from' => $firstIp[1],
            'first_half_to' => $secondIp[0],
            'second_half_to' => $secondIp[1],
            'country_code' => trim($this->stripQuotes($contents[4])),
            'country' => trim($this->stripQuotes($contents[5]))
        ];
    }

    private function stripQuotes($item) {
        return str_replace('"', '', $item);
    }

}
