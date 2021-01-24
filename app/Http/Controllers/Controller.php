<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Packages\Voluum\Voluum;
use App\Models\Account;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    /**
     * Returns data from Voluum to dashboard
     * @return \Illuminate\Contracts\View\View
     */
    function dashboard() {
        try {
            $accounts = Account::all();
            $accs = [];
            foreach ($accounts as $account) {
                $accs[] = $account->id;
            }
            return view('dashboard', ['accounts' => implode(',', $accs)]);
        } catch (\Exception $e) {
            echo $e->getmessage() . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        }
    }

    function networks() {
        try {
            $accounts = Account::all();
            $accs = [];
            foreach ($accounts as $account) {
                $accs[] = $account->id;
            }
            return view('network', ['accounts' => implode(',', $accs)]);
        } catch (\Exception $e) {
            echo $e->getmessage() . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        }
    }

    function get_data(Request $request, $id, $type) {
        try {
            if ($request->has('date_from') && $request->has('date_to'))
                $dateRange = ['date_from' => $request->date_from, 'date_to' => $request->date_to];
            else
                $dateRange = [];
            $voluum = new Voluum();
            $data = $voluum->get_dashboard_data($dateRange, $id, $type)[$id];
            return $data;
        } catch (\Exception $e) {
            echo $e->getmessage() . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        }
    }

    /**
     * Returns filtered data from Voluum to dashboard
     * @return \Illuminate\Contracts\View\View
     */
    function filter_dashboard(Request $request) {
        try {
            if ($request->has('date_from') && $request->has('date_to'))
                $dateRange = ['date_from' => $request->date_from, 'date_to' => $request->date_to];
            else
                $dateRange = [];

            $voluum = new Voluum();
            $data = $voluum->get_dashboard_data($dateRange);
            return view('dashboard', ['data' => $data, 'daterange' => $request->daterange_format, 'date_from' => $request->date_from, 'date_to' => $request->date_to]);
        } catch (\Exception $e) {
            echo $e->getmessage() . ' At file: ' . $e->getFile() . ' At line: ' . $e->getLine();
        }
    }

}
