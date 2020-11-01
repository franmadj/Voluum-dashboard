<?php

namespace App\Packages\Voluum;

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class Voluum {

    private $auth_token;
    private $accounts_data;

    public function __construct() {
        $this->accounts_data = $this->get_accounts();
    }

    public function get_dashboard_data($dateRange) {
        //$data_dashboard=$accounts_data;
        foreach ($this->accounts_data as $key => $acc) {
            $this->auth_token = $acc['token'];
            $data = $this->request_report($acc, $dateRange);
            //Log::debug('data response .' . print_r($data, true));
//            $clicks_and_visits = $this->get_clicks_and_visits($data->rows);
//            var_dump($clicks_and_visits);
            //exit;


            $this->accounts_data[$key]['data'] = $data;
//            $accounts_data[$key]['clicks'] = count($data->rows); //$clicks_and_visits['clicks'];
//            $accounts_data[$key]['visits'] = count($data->rows); //$clicks_and_visits['visits'];
            //var_dump($data->totals);
        }
        //var_dump($this->accounts_data);
        return $this->accounts_data;
    }

    private function request_report($acc, $dateRange) {
        //https://api.voluum.com/report?include=ALL&groupBy=affiliateNetworkId&from=2017-05-20T00%3A00%3A00Z&workspaces=21fbb9c8-9c57-4bff-9da8-ab1fa5c932a3
        $query = 'include=ALL&groupBy=affiliateNetworkId';

        $dates = '';
        $report = [];

        $dates = '&from=' . date('Y-m-d', strtotime('-28 day')) . 'T00:00:00Z';
        if ($dateRange) {
            $to = date('Y-m-d', strtotime($dateRange['date_to'] . ' +1 day')) . 'T00:00:00Z';

            $dates = '&from=' . urlencode($dateRange['date_from']) . '&to=' . urlencode($to);
        }
        $query .= $dates;

        $url = "https://api.voluum.com/report?" . $query;
        //$url='https://api.voluum.com/report?include=ALL&groupBy=affiliateNetworkId&from=2017-05-20T00%3A00%3A00Z&to=2020-10-30T00%3A00%3A00Z';
        //var_dump($url);exit;
        $report['acc'] = $this->query_report($url);

        if (!empty($acc['workspaces'])) {
            $report['ws'] = [];
            $workspaces = array_map('trim', explode(',', $acc['workspaces']));
            foreach ($workspaces as $workspace) {
                $ws_query = $query . '&workspaces=' . $workspace;
                $url = "https://api.voluum.com/report?" . $ws_query;
                $report['ws'][$workspace] = $this->query_report($url);
            }
        }
        return $report;
    }

    private function query_report($url) {
        $client = new Client();
        $response = $client->request("GET", $url, [
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
                'CWAUTH-TOKEN' => $this->auth_token,
        ]]);
        $code = $response->getStatusCode();
        if (200 == $code) {
            return json_decode((string) $response->getBody())->totals;
        }
        return false;
    }

    private function get_accounts() {
        $accounts_data = session('voluum_tokens', []);
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

    /* private function get_clicks_and_visits($rows) {
      if (!$rows)
      return;
      $campaigns = [];
      $clicks = $visits = $i = $n = 0;
      foreach ($rows as $row) {
      if (!isset($campaigns[$row->campaignId])) {
      $i++;


      $campaigns[$row->campaignId] = $row->campaignId;
      $clicks += $this->request_clicks_and_visits($row->campaignId, 'clicks');
      $visits += $this->request_clicks_and_visits($row->campaignId, 'visits');
      if ($i >= 5) {
      sleep(2);
      $i = 0;
      }
      }
      }
      return['clicks' => $clicks, 'visits' => $visits];
      }

      private function request_clicks_and_visits($campaign, $type = 'clicks') {

      //var_dump($campaign, $type);return 1;

      $client = new Client();
      $url = "https://api.voluum.com/report/live/$type/" . $campaign;
      $response = $client->request("GET", $url, [
      'headers' => [
      'Accept' => 'application/json',
      'CWAUTH-TOKEN' => $this->auth_token,
      ]]);
      $code = $response->getStatusCode();
      if (200 == $code) {
      $data = json_decode((string) $response->getBody());
      return $data->totalRows;
      }
      return false;
      }

      private function request_report_old($acc, $dateRange) {
      $columns = 'column=visits&column=clicks&column=conversions&column=revenue&column=cost&column=profit';
      $workspaces = '';
      $dates = '';
      if (!empty($acc['workspaces'])) {
      $ws = array_map('trim', explode(',', $acc['workspaces']));
      $workspaces = '';
      foreach ($ws as $_ws) {
      $workspaces .= '&workspaces=' . $_ws;
      }
      }
      $dates = '&from=2020-10-30T00:00:00Z';
      if ($dateRange) {
      $dates = '&from=' . $dateRange['date_from'] . '&to' . $dateRange['date_to'];
      }
      $query = $columns . $workspaces . $dates;
      $client = new Client();
      $url = "https://api.voluum.com/report/conversions?" . $query;
      $response = $client->request("GET", $url, [
      'headers' => [
      'Content-Type' => 'application/json; charset=utf-8',
      'Accept' => 'application/json',
      'CWAUTH-TOKEN' => $acc['token'],
      ]]);
      $code = $response->getStatusCode();
      if (200 == $code) {
      return json_decode((string) $response->getBody());
      }
      return false;
      } */
}
