<?php

namespace App\Repositories;

use App\Models\Ipv6CountryMapping;
use App\Repositories\RepoTraits\Batchable;
use DB;
use Maknz\Slack\Facades\Slack;
use App\Repositories\RepoTraits\IPv6;

class Ipv6CountryMappingRepo {

    use Batchable, IPv6;
    CONST ROOM = "#cmp-hard-start-errors";
    
    private $model;

    public function __construct(Ipv6CountryMapping $model) {
        $this->model = $model;
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();
        return '(' 
            . "conv(hex(inet6_aton('{$row['first_half_from']}')), 16, 10),"
            . "conv(hex(inet6_aton('{$row['second_half_from']}')), 16, 10),"
            . "conv(hex(inet6_aton('{$row['first_half_to']}')), 16, 10),"
            . "conv(hex(inet6_aton('{$row['second_half_to']}')), 16, 10),"
            . $pdo->quote($row['country_code']) . ','
            . $pdo->quote($row['country'])
            . ')';
    }

    private function buildBatchedQuery($batchData) {
        // Just a raw insert - upserts might allow for gaps
        return "INSERT INTO ipv6_country_mappings 
        (first_half_from, second_half_from, first_half_to, second_half_to, country_code, country)

        values

        $batchData";
    }

    public function isFromCanada($ip) {
        $ip = $this->validateIp($ip);

        $words = $this->splitIp($ip);
        $words1 = $words[0];
        $words2 = $words[1];

        $result = $this->model
                    ->whereRaw("conv(hex(inet6_aton($words1))) >= first_half_from")
                    ->whereRaw("conv(hex(inet6_aton($words2))) >= second_half_from")
                    ->whereRaw("conv(hex(inet6_aton($words1))) <= first_half_to")
                    ->whereRaw("conv(hex(inet6_aton($words2))) <= second_half_to")
                    ->get();
        // Ideally we'd validate this beforehand, but due to constraints:
        // number of rows
        // nature of the data (can't be compared within PHP due to integer limits)
        // it's not easy to do that
        // so we can rely on the processing as a sort of de-facto Monte Carlo-style test

        $count = count($result);

        // Incorrect IPv6 ranges are not a problem until they are a problem.

        if (1 === $count) {
            return $result->first()->country_code === 'CA';
        }
        elseif (0 === $count) {
            Slack::to(self::ROOM)->send("$ip does not appear in the IPv6 table");
        }
        if ($count > 1) {
            Slack::to(self::ROOM)->send("$ip appears in more than one row in the IPv6 table");
        }
    }

    public function clearTable() {
        $this->model->truncate();
    }

    public function getLastUpdate() {
        return $this->model->max('updated_at');
    }

}