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

        $firstResult = $this->model
                            ->whereRaw("conv(hex(inet6_aton('$words1')), 16, 10) >= first_half_from")
                            ->whereRaw("conv(hex(inet6_aton('$words1')), 16, 10) <= first_half_to")
                            ->selectRaw("country_code, 
                                (first_half_from = first_half_to) firsts_match, 
                                (conv(hex(inet6_aton('$words2')), 16, 10) BETWEEN second_half_from AND second_half_to) second_between,
                                (conv(hex(inet6_aton('$words1')), 16, 10) = first_half_from) ip_matches_first_from,
                                (conv(hex(inet6_aton('$words1')), 16, 10) = first_half_to) ip_matches_first_to,
                                (conv(hex(inet6_aton('$words2')), 16, 10) >= second_half_from) ip_gte_second_half_from,
                                (conv(hex(inet6_aton('$words2')), 16, 10) <= second_half_to) ip_lte_second_half_to,
                                (conv(hex(inet6_aton('$words1')), 16, 10) BETWEEN (first_half_from+1) AND (first_half_to-1)) second_between
                            ")
                            ->get();

        if (1 === $count) {
            // We've got our result right here
            return $result->first()->country_code === 'CA';
        }
        elseif (0 === $count) {
            Slack::to(self::ROOM)->send("$ip does not appear in the IPv6 table");
            return true; // safety first
        }
        else {
            // appears in a few rows

            // Ideally we'd validate this beforehand, but due to constraints:
            // number of rows
            // nature of the data (can't be compared within PHP due to integer limits)
            // it's not easy to do that
            // so we can rely on the processing as a sort of de-facto Monte Carlo-style test
            // Incorrect IPv6 ranges are not a problem until they are a problem.

            foreach($result as $row) {
                if ((1 === $row->firsts_match) && $row->second_between) {
                    // The leading digits match - as does ours, implicitly. 
                    // We have to be sandwiched between them.
                    return $row->country_code === 'CA';
                }
                elseif ((1 !== $row->firsts_match) && (1 === $row->ip_matches_first_from) && (1 === $row->ip_gte_second_half_from)) {
                    // Our IP shares the same leading hextets as the from in this row
                    // need to check here that our second half >= second half from (the basement)
                    return $row->country_code === 'CA';
                }
                elseif ((1 !== $row->firsts_match) && (1 === $row->ip_matches_first_to) && (1 === $row->ip_lte_second_half_to)) {
                    // Our IP shares the same leading hextets as the to in this row
                    // need to check here that our second half <= second half from (a ceiling here)
                    return $row->country_code === 'CA';
                }
                elseif (1 === $row->ip_exclusive_between_first) {
                    // Last condition - we don't match either the leading hextets of the from or to
                    // (we are already between them)
                }
            }
            Slack::to(self::ROOM)->send("$ip does not appear in the IPv6 table");
            return true; // again, safety first
        }
    }

    public function clearTable() {
        $this->model->truncate();
    }

    public function getLastUpdate() {
        return $this->model->max('updated_at');
    }

}