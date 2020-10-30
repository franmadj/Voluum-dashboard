<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    function dashboard() {//session(['voluum_tokens' => []]);return;
       // try {
            $accounts_data = $this->get_accounts();
            $data = $this->get_dashboard_data($accounts_data, []);
            return view('dashboard', ['data' => $data]);
        //} catch (\Exception $e) {
            var_dump($e->getMessage());
       // }
    }

    function filter_dashboard(Request $request) {
        $accounts_data = $this->get_accounts();
        if ($request->has('date_from') && $request->has('date_to'))
            $dateRange = ['date_from' => $request->date_from, 'date_to' => $request->date_to];
        else
            $dateRange = [];

        $data = $this->get_dashboard_data($accounts_data, $dateRange);
        
        return view('dashboard', ['data' => $data]);
    }

    private function get_accounts() {
        $accounts_data = session('voluum_tokens', []);
        if ($accounts = Account::all()) {

            $now = (new \DateTime(date('c')));
            $tokens_updated = false;
            foreach ($accounts as $acc) {
                if (isset($accounts_data[$acc->id]) && $accounts_data[$acc->id]['expirationTimestamp']) {
                    $expire = (new \DateTime($accounts_data[$acc->id]['expirationTimestamp']));
                    if ($now > $expire) {
                        $accounts_data = $this->make_account_data($acc);
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

    private function get_dashboard_data($accounts_data, $dateRange) {
        //$data_dashboard=$accounts_data;
        foreach ($accounts_data as $key=>$acc) {
            $data = $this->request_report($acc, $dateRange);
            Log::debug('data response .' . print_r($data, true));
            
            $accounts_data[$key]['data']=$data->totals;

            //var_dump($data->totals);
        }
        return $accounts_data;
    }

    private function request_report($acc, $dateRange) {

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
        $dates = '&from=2019-05-20T00:00:00Z';
        if ($dateRange) {
            $dates = '&from=' . $dateRange['date_from'] . '&to' . $dateRange['date_to'];
        }

        $query = $columns . $workspaces . $dates;


        $client = new \GuzzleHttp\Client();

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
        Log::debug('data Account .' . print_r($logs, true));
        return $tokens;
    }

    private function request_auth_token($acc) {
        $client = new \GuzzleHttp\Client();
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
