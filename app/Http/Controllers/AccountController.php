<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller {

    function index() {
        return view('accounts', ['accounts' => Account::all(), 'account' => []]);
    }
    
    /**
     * Store account
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

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
    
    /**
     * Update Account by ID
     * @param Illuminate\Http\Request $request
     * @param INT $id
     * @return \Illuminate\Http\RedirectResponse
     */

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
        $this->reset_session_accounts($id);

        return redirect()->route('accounts');
    }
    
    /**
     * 
     * @param INT $id
     * @return \Illuminate\Http\RedirectResponse
     */

    function delete($id) {
        Account::destroy($id);
        $this->reset_session_accounts($id);
        return redirect()->route('accounts');
    }
    
    /**
     * resets the session with the current account data
     * @param type $id the account id just for the session array
     */
    
    protected function reset_session_accounts($id){
        $accounts_data = session('voluum_tokens', []);
        unset($accounts_data[$id]);
        session(['voluum_tokens' => $accounts_data]);
        
    }

}
