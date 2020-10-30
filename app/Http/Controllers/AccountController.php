<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller {

    function index() {
        return view('accounts', ['accounts' => Account::all(), 'account' => []]);
    }

    function store(Request $request) {
        $request->validate([
            'name' => 'bail|required|unique:accounts|max:30',
            'access_key_id' => 'bail|required|unique:accounts|max:50',
            'access_key' => 'bail|required|unique:accounts|max:50',
           
        ]);
        $account = Account::create([
                    'name' => $request->name,
                    'access_key_id' => $request->access_key_id,
                    'access_key' => $request->access_key,
                    'workspaces' => $request->workspaces,
        ]);
        return redirect()->route('accounts');
    }

    function edit($id) {
        return view('edit-accounts', ['accounts' => Account::all(), 'account' => Account::findOrFail($id)]);
    }

    function update(Request $request, $id) {
        $request->validate([
            'name' => 'bail|required|unique:accounts,name,'.$id.'|max:30',
            'access_key_id' => 'bail|required|unique:accounts,access_key_id,'.$id.'|max:50',
            'access_key' => 'bail|required|unique:accounts,access_key,'.$id.'|max:50',
            
        ]);

        Account::find($id)->update([
            'name' => $request->name,
            'access_key_id' => $request->access_key_id,
            'access_key' => $request->access_key,
            'workspaces' => $request->workspaces,
        ]);
        
        $accounts_data = session('voluum_tokens', []);
        unset($accounts_data[$id]);
        session(['voluum_tokens' => $accounts_data]);
        
        return redirect()->route('accounts');
    }

    function delete($id) {
        Account::destroy($id);
        return redirect()->route('accounts');
    }

}
