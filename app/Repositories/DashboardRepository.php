<?php

namespace App\Repository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DashboardRepository
{
    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public static function data()
    {
        $client = new Client();
        $uri = env('HINTIFY_API') . '/dashboard/data?public_key=' . env('HINTIFY_API_PUBLIC_KEY') . '&payload={"lastMonths":6}';

        try {
            $result = $client->get($uri);
            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);

                return $result;
            } else {
                return false;
            }
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * Get last months labels
     *
     * @param $last
     * @return array
     */
    public static function lastMonthsLabels($last)
    {
        try {
            $last_months = [];

            $date = strtotime(date('Y-m-01'));
            for ($i = 0; $i < $last; $i++) {
                $month = date("M", strtotime(" -$i month", $date));

                array_unshift($last_months, strtoupper($month));
            }

            return $last_months;
        } catch (RequestException $e) {
            return [];
        }
    }
}