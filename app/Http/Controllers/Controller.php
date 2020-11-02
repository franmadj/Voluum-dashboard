<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Packages\Voluum\Voluum;
use GuzzleHttp\Exception\ClientException;

\GuzzleHttp\Psr7\Response::class;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    function dashboard() {//session(['voluum_tokens' => []]);return;
        try {
            $voluum = new Voluum();
            $data = []; //$voluum->get_dashboard_data([]);
            return view('dashboard', ['data' => $data, 'daterange' => false, 'date_from' => '', 'date_to' => '']);
        } catch (ClientException $e) {
            echo $e->getResponse()->getReasonPhrase() . ', Something wrong with the request to the API, please check account details like api keys and workspaces';
        } catch (\Exception $e) {
            echo $e->getmessage() . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        }
    }

    function filter_dashboard(Request $request) {
        //try {
        if ($request->has('date_from') && $request->has('date_to'))
            $dateRange = ['date_from' => $request->date_from, 'date_to' => $request->date_to];
        else
            $dateRange = [];

        $voluum = new Voluum();
        $data = $voluum->get_dashboard_data($dateRange);

        return view('dashboard', ['data' => $data, 'daterange' => $request->daterange_format, 'date_from' => $request->date_from, 'date_to' => $request->date_to]);
        // } catch (ClientException $e) {
        echo $e->getResponse()->getReasonPhrase() . ', Something wrong with the request to the API, please check account details like api keys and workspaces' . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        // } catch (\Exception $e) {
        echo $e->getmessage() . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        // }
    }

}
