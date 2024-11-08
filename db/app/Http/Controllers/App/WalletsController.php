<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\UserWallet;
use App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use \Carbon\Carbon;

class WalletsController extends Controller
{

    public function index() {
        $wallets = Wallet::paginate(50);
        $live_wallet = Wallet::where('test_wallet', 0)->count();
        $test_wallet = Wallet::where('test_wallet', 1)->count();
        return view('app.wallets.index',compact('wallets','live_wallet','test_wallet'));
    }

    public function add() {
        return view('app.wallets.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'wallet_name' => ['required','unique:mysql2.wallets'],
            'wallet_currency' => ['required', 'unique:mysql2.wallets'],
            'price' => ['required'],
        ],[
            'wallet_name.required' => 'Wallet name is required',
            'wallet_name.unique' => 'Wallet name already exists',
            'wallet_currency.required' => 'Wallet currency is required',
            'wallet_currency.unique' => 'Wallet currency already exists',
            'price.required' => 'Wallet price is required',
        ]);
        $wallet = new Wallet();
        $wallet->wallet_name = $data['wallet_name'];
        $wallet->wallet_currency = $data['wallet_currency'];
        $wallet->default_wallet = isset($data['default_wallet']) ? $data['default_wallet'] : 0;
        $wallet->test_wallet = isset($data['test_wallet']) ? $data['test_wallet'] : 0;

        if(isset($data['test_wallet']))
        {
            $wallet->price = $data['price'];
        }
        else
        {
            $wallet->price = 0;
        }


        if($wallet->save()){
            if($wallet->default_wallet == 1)
            {
                Wallet::where('test_wallet', $wallet->test_wallet)->where('id', '!=', $wallet->id)->update(['default_wallet'=>0]);
            }
            
            $users = User::get();
            foreach($users as $user)
            {
                $data = [
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'price' => $wallet->price,
                    'test_mode' => $wallet->test_wallet,
                ];
                if(!UserWallet::where('user_id', $user->id)->where('wallet_id', $wallet->id)->exists())
                {
                    UserWallet::create($data);
                }
            }


            toast('Wallet added successfully','success');
            return redirect()->route('wallets');
        }
    }

    public function edit($id) {
        $wallet = Wallet::find($id);
        if(is_null($wallet)){
            return abort(404);
        }
        return view('app.wallets.edit',compact('wallet'));
    }

    public function update(Request $request , $id) {

        $wallet = Wallet::find($id);
        if(is_null($wallet)){
            return abort(404);
        }
        $data = $request->all();
        $validatedData = $request->validate([
            'wallet_name' => ['required',Rule::unique('mysql2.wallets')->ignore($wallet->id)],
            'wallet_currency' => ['required', Rule::unique('mysql2.wallets')->ignore($wallet->id)],
        ],[
            'wallet_name.required' => 'Wallet name is required',
            'wallet_name.unique' => 'Wallet name already exists',
            'wallet_currency.required' => 'Wallet currency is required',
            'wallet_currency.unique' => 'Wallet currency already exists',
        ]);
        $wallet->wallet_name = $data['wallet_name'];
        $wallet->wallet_currency = $data['wallet_currency'];
        $wallet->default_wallet = isset($data['default_wallet']) ? $data['default_wallet'] : 0;
       // $wallet->test_wallet = isset($data['test_wallet']) ? $data['test_wallet'] : 0;

        if(isset($data['price']))
        {
            $wallet->price = $data['price'];
        }
        else
        {
            $wallet->price = 0;
        }

        if($wallet->save()){
            if($wallet->default_wallet == 1)
            {
                Wallet::where('test_wallet', $wallet->test_wallet)->where('id', '!=', $wallet->id)->update(['default_wallet'=>0]);
            }
            toast('Wallet updated successfully','success');
            return redirect()->route('wallets_edit',$wallet->id);
        }
    }

    // public function delete($id) {
    //     $wallets = Wallet::find($id);
    //     if(is_null($wallets)){
    //         return abort(404);
    //     }
    //     if($wallets->delete()){
    //         toast('Wallet deleted successfully','success');
    //     }
    //     return redirect()->route('wallets');
    // }

    public function deactivate($id) {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $user->active = 0;
        if($user->save()){
            toast('User deactivated successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function activate($id) {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $user->active = 1;
        if($user->save()){
            toast('User activated successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function deactivateSelected(Request $request) {
        $ids = $request->ids;
        $targetUsers = User::whereIn('id',$ids)->where('active',1);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetUsers->update(['active'=>0])){
            toast('Users deactivated successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function deleteSelected(Request $request) {
        $ids = $request->ids;
        $targetUsers = User::whereIn('id',$ids);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetUsers->delete()){
            toast('Users deleted successfully','success');
        }
        return redirect()->route('app_users');
    }

    public function search(Request $request){
        $wallets = Wallet::orderBy('id','ASC');
        if(isset($request->id)){
            $wallets = $wallets->where('id', $request->id);
        }
        if(isset($request->name)){
            $wallets = $wallets->where('wallet_name','LIKE','%' .$request->name. '%');
        }
        if(isset($request->currency)){
            $wallets = $wallets->where('wallet_currency','LIKE','%' .$request->currency. '%');
        }
        $wallets = $wallets->paginate(50);
        return view('app.wallets.index',compact('wallets'));
    }




    public function show_wallet_transactions(Request $request,$id)
    {

        $today = Carbon::today()->format('Y-m-d'); //today
        $yesterday = Carbon::yesterday()->format('Y-m-d'); //yesterday
        $last_7_days = Carbon::now()->subDays(7)->format('Y-m-d'); //last_7_days
        $last_30_days = Carbon::now()->subDays(30)->format('Y-m-d'); //last_30_days
        $last_month = Carbon::now()->subMonth()->format('m'); //last_month
        $this_month = Carbon::now()->format('m'); //this_month



        $wallet = Wallet::find($id);
       $wallet_transactions = Transaction::where('wallet', $wallet->id)->get();
       // $totalAmountUsername = Transaction::where('wallet', $wallet->id)->where('transaction_type', 'username')->sum('amount');
       // $totalAmountGroup = Transaction::where('wallet', $wallet->id)->where('transaction_type', 'group')->sum('amount');
       // $totalAmountTransCode = Transaction::where('wallet', $wallet->id)->where('transaction_type', 'transaction_code')->sum('amount');
       // $totalAmountService = Transaction::where('wallet', $wallet->id)->where('transaction_type', 'service')->sum('amount');
       // $totalAmountTrade = Transaction::where('wallet', $wallet->id)->where('transaction_type', 'trade')->sum('amount');
       // $totalAmountInvoice = Transaction::where('wallet', $wallet->id)->where('transaction_type', 'invoice')->sum('amount');
       // $totalAmount = Transaction::where('wallet', $wallet->id)->sum('amount');

       $transArray= [];
       $totalAmountUsername = 0;
       $totalAmountGroup = 0;
       $totalAmountTransCode = 0;
       $totalAmountService = 0;
       $totalAmountTrade = 0;
       $totalAmountInvoice = 0;

       foreach ($wallet_transactions as $data) 
        {
            if($data->transaction_type == 'username')
            {
                $date = date('Y-m-d', strtotime($data->creationTime));
                $dateMonth = date('m', strtotime($data->creationTime));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $totalAmountUsername +=$data->amount;
                        array_push($transArray, $data);
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
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $totalAmountGroup +=$data->amount;
                        array_push($transArray, $data);
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
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                else
                {
                    if($date == $today)
                    {
                       $totalAmountTransCode +=$data->amount;
                        array_push($transArray, $data);
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
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $totalAmountService +=$data->amount;
                        array_push($transArray, $data);
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
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $totalAmountTrade +=$data->amount;
                        array_push($transArray, $data);
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
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
                else
                {
                    if($date == $today)
                    {
                        $totalAmountInvoice +=$data->amount;
                        array_push($transArray, $data);
                    }
                }
            }
           
            
        }



       return view('app.wallets.transactions', 
        compact(
            'transArray',
            'wallet', 
            'totalAmountUsername',
            'totalAmountGroup',
            'totalAmountTransCode',
            'totalAmountService',
            'totalAmountTrade',
            'totalAmountInvoice'
        ));
    }

}
