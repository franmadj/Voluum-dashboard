<?php

namespace App\Packages\Voluum;

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;

class Voluum {

    private $auth_token;
    private $accounts_data;
    private $is_daterange_to_past = true;

    public function __construct() {
        $this->accounts_data = $this->get_accounts();
    }

    public function get_dashboard_data($dateRange) {
        $dates = $this->get_date_ranges($dateRange);
        foreach ($this->accounts_data as $key => $acc) {
            $this->auth_token = $acc['token'];
            $data = $this->request_report($acc, $dates);
            $this->accounts_data[$key]['data'] = $data;
        }
        return $this->accounts_data;
    }

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

    private function request_report($acc, $dates) {
        $query = 'include=ALL&groupBy=affiliateNetworkId&conversionTimeMode=CONVERSION';
        $base_url = "https://api.voluum.com/report?";

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
                $report['ws'][$workspace_id] = $this->query_report($url . '&workspaces=' . $workspace_id);
                $report['ws'][$workspace_id]->name = $workspace_name;
                $report['ws'][$workspace_id]->month_profit = $this->get_month_profit($to_month_url . '&workspaces=' . $workspace_id);
            }
        }
        return $report;
    }

    private function get_month_profit($to_month_url) {
        $from = date('Y-m-01') . 'T00:00:00';
        $to = date('Y-m-d\T', strtotime(' +1 days')) . '00:00:00';
        $dates = '&from=' . urlencode($from) . '&to=' . urlencode($to);
        $to_month_url .= $dates . '&tz=CET';
        if ($result = $this->query_report($to_month_url)) {
            return $result->profit;
        }
        return 0;
    }

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
                $result = json_decode((string) $response->getBody())->totals;
                return $result;
            }
        } catch (ClientException $e) {
            Log::debug('query_report .' . $e->getResponse()->getReasonPhrase() . ', Something wrong with the request "'.$url.'" to the API, please check account details like api keys and workspaces');
        }
        return false;
    }

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

    private function request_auth_token($acc) {
        $client = new Client();
        $body = [];
        $body['accessId'] = $acc->access_key_id;
        $body['accessKey'] = $acc->access_key;
        $url = "https://api.voluum.com/auth/access/session";
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
