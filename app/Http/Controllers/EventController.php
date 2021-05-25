<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function store(Request $request){

        if($request->input('type') === 'deposit'){
           return $this->deposit($request->input('destination'),$request->input('amount'));
        }
        elseif($request->input('type') === 'withdraw'){
                return $this->withdraw($request->input('origin'),$request->input('amount'));
        }
        elseif($request->input('type') === 'transfer'){
                return $this->transfer($request->input('origin'),$request->input('amount'),$request->input('destination'));
        }
    }

    private function deposit($destination, $amount){
        $account = Account::firstOrCreate(['id' => $destination] ); //condition search

        $account->balance += $amount;
        
        $account->save();   //update

        return response()->json([
            'destination' => [
                'id' => $account->id,
                'balance' => $account->balance,
            ],

        ], 201);
    }

    private function withdraw($origin, $amount){
        $account = Account::findOrFail($origin);

        $account->balance -= $amount;
        
        $account->save(); 

        return response()->json([
            'origin' => [
                'id' => $account->id,
                'balance' => $account->balance,
            ],

        ], 201);
    }

    public function transfer($origin, $amount, $destination){
        $accountOrigin = Account::findOrFail($origin);
        $accountDestination = Account::firstOrCreate([
            "id" => $destination
        ]);

       DB::transaction(function() use ($accountOrigin, $amount, $accountDestination){
       
        $accountOrigin->balance -=$amount;
        $accountDestination->balance +=$amount;

        $accountOrigin->save();
        $accountDestination->save();
       });
        
        return response()->json([
            'origin' => [
                'id' => $accountOrigin->id,
                'balance' => $accountOrigin->balance,
            ],
            'destination' => [
                'id' => $accountDestination->id,
                'balance' => $accountDestination->balance,
            ]

        ], 201);
    }
}
