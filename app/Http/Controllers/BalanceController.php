<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;

class BalanceController extends Controller
{
    public function show(Request $request){

        $accountId = $request->input('account_id');

        $account = Account::findOrFail($accountId);

        return $account->balance;
    }


}
