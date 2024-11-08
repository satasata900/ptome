<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use \Carbon\Carbon;
class TransactionController extends Controller
{

    public function index(Request $request) {


        $today = Carbon::today()->format('Y-m-d'); //today
        $yesterday = Carbon::yesterday()->format('Y-m-d'); //yesterday
        $last_7_days = Carbon::now()->subDays(7)->format('Y-m-d'); //last_7_days
        $last_30_days = Carbon::now()->subDays(30)->format('Y-m-d'); //last_30_days
        $last_month = Carbon::now()->subMonth()->format('m'); //last_month
        $this_month = Carbon::now()->format('m'); //this_month


        
        $transaction_username = 0;
        $transaction_group = 0;
        $transaction_transaction_code = 0;
        $transaction_service = 0;
        $transaction_trade = 0;
        $transaction_invoice = 0;

        $transaction = Transaction::orderBy('id', 'desc');

        if(isset($request->transaction_number)){
            $transaction = $transaction->where('transaction_number','LIKE','%' .$request->transaction_number. '%');
        }
        if(isset($request->transaction_type)){
            $transaction = $transaction->where('transaction_type','LIKE','%' .$request->transaction_type. '%');
        }
        if(isset($request->wallet)){
            $transaction = $transaction->where('wallet','=',$request->wallet);
        }
        $transaction = $transaction->paginate(50);
        foreach ($transaction as $data) 
        {
            if($data->transaction_type == 'username')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $transaction_username +=1;
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $transaction_username +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $transaction_username +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $transaction_username +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $transaction_username +=1;
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $transaction_username +=1;
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $transaction_username +=1;
                    }
                }
            }
            if($data->transaction_type == 'group')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $transaction_group +=1;
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $transaction_group +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $transaction_group +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $transaction_group +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $transaction_group +=1;
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $transaction_group +=1;
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $transaction_group +=1;
                    }
                }
            }
            if($data->transaction_type == 'transaction_code')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $transaction_transaction_code +=1;
                    }
                }
            }
            if($data->transaction_type == 'service')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $transaction_service +=1;
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $transaction_service +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $transaction_service +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $transaction_service +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $transaction_service +=1;
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $transaction_service +=1;
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $transaction_service +=1;
                    }
                }
            }
            if($data->transaction_type == 'trade')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $transaction_trade +=1;
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $transaction_trade +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $transaction_trade +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $transaction_trade +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $transaction_trade +=1;
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $transaction_trade +=1;
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $transaction_trade +=1;
                    }
                }
            }
            if($data->transaction_type == 'invoice')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $transaction_invoice +=1;
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $transaction_invoice +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $transaction_invoice +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $transaction_invoice +=1;
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $transaction_invoice +=1;
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $transaction_invoice +=1;
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $transaction_invoice +=1;
                    }
                }
            }
           
        }


        $wallets = Wallet::where('test_wallet',0)->get();

        return view('app.transaction.index',compact(
            'transaction',
            'transaction_username',
            'transaction_group',
            'transaction_transaction_code',
            'transaction_service',
            'transaction_trade',
            'transaction_invoice',
            'wallets'
            
        ));
    }

   

    public function view($id) {
        $transaction = Transaction::find($id);
        if(is_null($transaction)){
            return abort(404);
        }
        return view('app.transaction.view',compact('transaction'));
    }

 
    // public function delete($id) {
    //     $transaction = Transaction::find($id);
    //     if(is_null($transaction)){
    //         return abort(404);
    //     }
    //     if($transaction->delete()){
    //         toast('Transaction deleted successfully','success');
    //     }
    //     return redirect()->route('transaction');
    // }


    

}
