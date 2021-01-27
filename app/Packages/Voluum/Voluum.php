<?php

namespace App\Packages\Voluum;

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;

class Voluum {

    /**
     *
     * @var String Token for the current account the call API is for
     */
    private $auth_token;

    /**
     *
     * @var Array Holds Accounts data 
     */
    private $accounts_data;

    /**
     *
     * @var Boolean Determines if the required API data does not reach the present time.
     */
    private $is_daterange_to_past = true;

    /**
     * Voluum domain API
     * @var String 
     */
    private $domain_api = "https://api.voluum.com";

    /**
     * type of data to retreive
     * @var String 
     */
    private $data_type = 'dashboard';

    /**
     * sets the accounts data
     */
    public function __construct() {
        $this->accounts_data = $this->get_accounts();
    }

    private function is_dashboard() {
        return $this->data_type == 'dashboard';
    }

    private function is_network() {
        return $this->data_type == 'network';
    }

    private function is_traffic_soruce() {
        return $this->data_type == 'traffic-source';
    }

    /**
     * First layer to Get data from Voluum API
     * @param Array $dateRange array with date_from and date_to to get the data from
     * @return Array
     */
    public function get_dashboard_data($dateRange, $account_id = NULL, $type) {
        $this->data_type = $type;
        $dates = $this->get_date_ranges($dateRange);
        foreach ($this->accounts_data as $key => $acc) {
            if ($account_id && $account_id != $key)
                continue;
            $this->auth_token = $acc['token'];
            $data = $this->request_report($acc, $dates);
            $this->accounts_data[$key]['data'] = $data;
        }
        return $this->accounts_data;
    }

    /**
     * Converts dates into Voluum get query string parameters
     * @param Array $dateRange array with date_from and date_to to get the data from
     * @return String
     */
    private function get_date_ranges($dateRange) {
        $dates = '';
        $dates = '&from=' . date('Y-m-d') . 'T00:00:00Z';
        if ($dateRange) {
            $this->set_daterange_to_today($dateRange['date_to']);
            $from = date('Y-m-d\T', strtotime($dateRange['date_from'])) . '00:00:00Z';
            $to = date('Y-m-d\T', strtotime($dateRange['date_to'] . ' +1 days')) . '00:00:00Z';
            $dates = '&from=' . urlencode($from) . '&to=' . urlencode($to);
        }
        return $dates .= '&tz=CET';
    }

    /**
     * For each account Builds data returned from Voluum
     * @param Array $acc the single account data
     * @param String $dates the query string parameters for dates
     * @return Array
     */
    private function request_report($acc, $dates) {
        $groupBy = $this->is_traffic_soruce() ? 'trafficSourceId' : 'affiliateNetworkId';
        $query = 'include=ALL&groupBy='.$groupBy.'&conversionTimeMode=CONVERSION&sort=revenue&direction=DESC';
        $base_url = $this->domain_api . "/report?";
        $url = $base_url . $query . $dates;
        $to_month_url = $base_url . $query;
        $report = [];
        if (!empty($acc['workspaces'])) {
            $report['ws'] = [];
            $workspaces = array_map('trim', explode(',', $acc['workspaces']));
            foreach ($workspaces as $workspace) {
                $workspace = array_map('trim', explode(':', $workspace));
                $workspace_name = $workspace[0];
                $workspace_id = isset($workspace[1]) ? $workspace[1] : $workspace[0];
                $result = $this->query_report($url . '&workspaces=' . $workspace_id);

                if ($this->is_dashboard()) {

                    $report['ws'][$workspace_id] = $result->totals;
                    //var_dump($report['ws'][$workspace_id]);
                    if (!$report['ws'][$workspace_id])
                        continue;

                    $report['ws'][$workspace_id]->month_profit = $this->get_month_profit($to_month_url . '&workspaces=' . $workspace_id);
                } else if ($this->is_network()) {

                    $data = new \stdClass();
                    //$data->networks =$result->rows;
                    $data->networks = $this->get_data_month_profit($to_month_url . '&workspaces=' . $workspace_id, $result->rows);

                    $report['ws'][$workspace_id] = $data;
                    //$data->profit = $this->set_network_profit($data->networks);
                } else if ($this->is_traffic_soruce()) {

                    //var_dump($result->rows);

                    $data = new \stdClass();
                    //$data->networks =$result->rows;

                    $data->traffic = $this->get_data_month_profit($to_month_url . '&workspaces=' . $workspace_id, $result->rows);

                    $report['ws'][$workspace_id] = $data;

                    //$data->profit = $this->set_network_profit($data->networks);
                }
                $report['ws'][$workspace_id]->name = $workspace_name;
                $report['ws'][$workspace_id]->id = substr(md5($workspace_name), 0, 4);
            }
        }
        return $report;
    }

    /**
     * Gets Voluum profit value for the current month
     * @param string $to_month_url base API query url
     * @return int Profit value
     */
    private function get_month_profit($to_month_url) {
        $from = date('Y-m-01') . 'T00:00:00';
        $to = date('Y-m-d\T', strtotime(' +1 days')) . '00:00:00';
        $dates = '&from=' . urlencode($from) . '&to=' . urlencode($to);
        $to_month_url .= $dates . '&tz=CET';
        if ($result = $this->query_report($to_month_url)->totals) {
            return $result->profit;
        }
        return 0;
    }

    /**
     * Query the Voluum API to get the report data
     * @param string $url the url to call the API
     * @return boolean false if not data is found | Object with totals if data is found
     */
    private function query_report($url) {
        try {
            $client = new Client();
            $response = $client->request("GET", $url, [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                    'CWAUTH-TOKEN' => $this->auth_token,
            ]]);
            $code = $response->getStatusCode();
            if (200 == $code) {
                $result = json_decode((string) $response->getBody());
                return $result;
            }
        } catch (ClientException $e) {
            Log::debug('query_report .' . $e->getResponse()->getReasonPhrase() . ', Something wrong with the request "' . $url . '" to the API, please check account details like api keys and workspaces');
        }
        return false;
    }

    private function get_data_month_profit($to_month_url, $rows) {


        $from = date('Y-m-01') . 'T00:00:00';
        $to = date('Y-m-d\T', strtotime(' +1 days')) . '00:00:00';
        $dates = '&from=' . urlencode($from) . '&to=' . urlencode($to);
        $to_month_url .= $dates . '&tz=CET';
        if ($data = $this->query_report($to_month_url)->rows) {
            foreach ($data as $key => $item) {
                $rows[$key]->month_profit = $item->profit;
            }
        }
        return $rows;
    }

    /**
     * Determines if the required data does not reach the present time.
     * @param String $date_to
     */
    private function set_daterange_to_today($date_to) {
        $this->is_daterange_to_past = date('Y-m-d') > date('Y-m-d', strtotime($date_to));
    }

    private function set_cache($url, $new_data) {
        if ($this->is_daterange_to_past) {
            $id = md5($url);
            $data = Storage::exists('dashboard_cache.json') ? json_decode(Storage::get('dashboard_cache.json'), true) : [];
            if (!isset($data[$id])) {
                $data[$id] = $new_data;
                Storage::put('dashboard_cache.json', json_encode($data));
                return true;
            }
        }
        return false;
    }

    private function get_cache($url) {
        if ($this->is_daterange_to_past && Storage::exists('dashboard_cache.json')) {
            $id = md5($url);
            $data = json_decode(Storage::get('dashboard_cache.json', []), true);
            if (isset($data[$id])) {
                return $data[$id];
            }
        }
        return false;
    }

    /**
     * Set session and returns data with the current accounts added in the database along with the token generated from the API to make future queries
     * @return Array with all accounts
     */
    private function get_accounts() {
        $accounts_data = session('voluum_tokens', []);
        try {
            $accounts = Account::all();
            if ($accounts->count()) {

                $now = (new \DateTime(date('c')));
                $tokens_updated = false;
                foreach ($accounts as $acc) {
                    if (isset($accounts_data[$acc->id]) && $accounts_data[$acc->id]['expirationTimestamp']) {
                        $expire = (new \DateTime($accounts_data[$acc->id]['expirationTimestamp']));
                        if ($now > $expire) {
                            $accounts_data[$acc->id] = $this->make_account_data($acc);
                            $tokens_updated = true;
                        }
                    } else {
                        $accounts_data[$acc->id] = $this->make_account_data($acc);
                        $tokens_updated = true;
                    }
                }

                if ($tokens_updated)
                    session(['voluum_tokens' => $accounts_data]);
            }
        } catch (ClientException $e) {
            Log::debug('get_accounts .' . $e->getResponse()->getReasonPhrase());
        }
        return $accounts_data;
    }

    /**
     * Builds data for each account
     * @param Array $acc account Model
     * @return Array new account data from the API
     */
    private function make_account_data($acc) {
        $response = $this->request_auth_token($acc);
        $tokens = [];
        $tokens['name'] = $acc->name;
        $tokens['expirationTimestamp'] = $response->expirationTimestamp;
        $tokens['token'] = $response->token;
        $tokens['workspaces'] = $acc->workspaces;
        //LOGS
        $logs = $tokens;
        $logs['accessId'] = $acc->access_key_id;
        $logs['accessKey'] = $acc->access_key;
        //Log::debug('data Account .' . print_r($logs, true));
        return $tokens;
    }

    /**
     * Query the Voluum API to get the account token
     * @param Array $acc Account Model with access keys
     * @return boolean false when not data is returned | Object when data return from the API
     */
    private function request_auth_token($acc) {
        $client = new Client();
        $body = [];
        $body['accessId'] = $acc->access_key_id;
        $body['accessKey'] = $acc->access_key;
        $url = $this->domain_api . "/auth/access/session";
        $response = $client->request("POST", $url, [
            'json' => $body,
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
        ]]);

        $code = $response->getStatusCode();

        if (200 == $code) {
            return json_decode((string) $response->getBody());
        }
        return false;
    }

}
