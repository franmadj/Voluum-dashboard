<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Account;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    function index() {
        
        return view('dashboard');
        
        if ($accounts = Account::all()) {
            foreach ($accounts as $acc) {

                /* curl -X POST --header 'Content-Type: application/json; charset=utf-8' --header 'Accept: application/json' -d '{"accessId": "c9fe8201-1af5-4899-9ac9-15b9874c337a", \ 
                  "accessKey": "2aKnFAWMht17BMqukPIMoUdCiisYH1jSvLFR"}' 'https://api.voluum.com/auth/access/session' */

                $client = new \GuzzleHttp\Client();
                $body = [];
                $body['accessId'] = $acc->access_key_id;
                $body['accessKey'] = $acc->access_key;
                $url = "https://api.voluum.com/auth/access/session";
                $response = $client->request("POST", $url, [
                    'body' => $body,
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'Accept' => 'application/json',
                     
                ]]);
                $response = $client->send($response);
                return $response;

                $client = new \GuzzleHttp\Client();
                $request = $client->get('goolge.com');
                $response = $request->getBody();
                return $response;
            }
        }



        return view('dashboard');
    }

}
