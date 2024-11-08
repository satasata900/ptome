<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AdminTransaction;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use \Carbon\Carbon;
class AdminTransactionController extends Controller
{

    public function index(Request $request) {

        $today = Carbon::today()->format('Y-m-d'); //today
        $yesterday = Carbon::yesterday()->format('Y-m-d'); //yesterday
        $last_7_days = Carbon::now()->subDays(7)->format('Y-m-d'); //last_7_days
        $last_30_days = Carbon::now()->subDays(30)->format('Y-m-d'); //last_30_days
        $last_month = Carbon::now()->subMonth()->format('m'); //last_month
        $this_month = Carbon::now()->format('m'); //this_month

        $transfer = AdminTransaction::whereSuccess(1)->orderBy('id','DESC');
        $wallets = Wallet::where('test_wallet', 0)->get();


        if(isset($request->filter_by_created_at))
        {
            if($request->filter_by_created_at == 'today')
            {
                $transfer = $transfer->whereDate('created_at', $today);
            }
            elseif($request->filter_by_created_at == 'yesterday')
            {
                $transfer = $transfer->whereDate('created_at', $yesterday);
            }
            elseif($request->filter_by_created_at == 'last_7_days')
            {
                $transfer = $transfer->whereDate('created_at', '>=',$last_7_days)->whereDate('created_at', '<=',$today);
            }
            elseif($request->filter_by_created_at == 'last_30_days')
            {
                $transfer = $transfer->whereDate('created_at', '>=',$last_30_days)->whereDate('created_at', '<=',$today);
            }
            elseif($request->filter_by_created_at == 'last_month')
            {
                $transfer = $transfer->whereMonth('created_at', $last_month);
            }
            elseif($request->filter_by_created_at == 'this_month')
            {
                $transfer = $transfer->whereMonth('created_at', $this_month);
            }
            elseif($request->filter_by_created_at == 'all')
            {
                $transfer = $transfer->whereNotNull('id');
            }
        }

        $transfer = $transfer->paginate(50);
        return view('app.transfer.index',compact('transfer','wallets'));
    }

    public function add() {
        $wallets = Wallet::where('test_wallet', 0)->get();
        return view('app.transfer.add', compact('wallets'));
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'user_name' => ['required','max:255'],
            'wallet' => ['required'],
            'amount' => ['required'],
            'type' => ['required','in:send,draw'],
        ]);

        //check if user_name exists or not
        $user = User::where('user_name',$data['user_name'])->first();
        if(!$user)
        {
            toast('Username not exist before','error');
            return back();
        }

        $transfer = new AdminTransaction();
        $transfer->user_name = $data['user_name'];
        $transfer->wallet    = $data['wallet'];
        $transfer->amount    = $data['amount'];
        $transfer->type      = $data['type'];

        if($transfer->save()){

            $userWallet= UserWallet::where('user_id', $user->id)->where('wallet_id', $transfer->wallet);
            if($userWallet->exists())
            {
                if($transfer->type == 'send')
                {
                    $userWallet->update(['price' => $userWallet->first()->price+$transfer->amount]);
                }
                elseif($transfer->type == 'draw')
                {
                    if($transfer->amount > $userWallet->first()->price)
                    {
                        AdminTransaction::whereId($transfer->id)->update(['success' => 0]);
                        toast('Amount must be equal or smaller than price of user wallet','error');
                        return back();
                    }
                    else
                    {
                        $userWallet->update(['price' => $userWallet->first()->price - $transfer->amount]);
                    }
                }
            }
            else
            {
                if($transfer->type == 'send')
                {
                    $userWallet->create([
                        'user_id' => $user->id,
                        'wallet_id' => $transfer->wallet,
                        'price' => $transfer->amount,
                    ]);
                }
                elseif($transfer->type == 'draw')
                {
                    AdminTransaction::whereId($transfer->id)->update(['success' => 0]);
                    toast('User Wallet Not Founded','error');
                    return back();
                }
                
            }

            toast('Transfer added successfully','success');
            return redirect()->route('admin_transactions');
        }

    }

   

    public function search(Request $request){
        $transfer = AdminTransaction::whereSuccess(1)->orderBy('id','DESC');

        if(isset($request->username)){
            $transfer = $transfer->where('user_name', 'LIKE', '%'.$request->username.'%');
        }

        if(isset($request->wallet)){
            $transfer = $transfer->where('wallet', $request->wallet);
        }

        if(isset($request->type)){
            $transfer = $transfer->where('type',$request->type);
        }
        
        $transfer = $transfer->paginate(50);
        $wallets = Wallet::where('test_wallet', 0)->get();
        return view('app.transfer.index',compact('transfer', 'wallets'));
    }


}
