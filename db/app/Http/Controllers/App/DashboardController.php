<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Transaction;
use \Carbon\Carbon;
use App\Models\Trader;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Trader_Service;
use App\Models\City;
use App\Models\Field;




class DashboardController extends Controller
{

    public function dashboard(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d'); //today
        $yesterday = Carbon::yesterday()->format('Y-m-d'); //yesterday
        $last_7_days = Carbon::now()->subDays(7)->format('Y-m-d'); //last_7_days
        $last_30_days = Carbon::now()->subDays(30)->format('Y-m-d'); //last_30_days
        $last_month = Carbon::now()->subMonth()->format('m'); //last_month
        $this_month = Carbon::now()->format('m'); //this_month
        
        //Count of All Transactions Based on Filter Date
        $AllTransactions = Transaction::get();
        $count_of_transactions = 0;
       // $array=[];
        foreach($AllTransactions as $data)
        {
            $date = date('Y-m-d', strtotime($data->creationTime));
            $dateMonth = date('m', strtotime($data->creationTime));
           // array_push($array, $date);
            if($request->filter_by_date == 'today')
            {
                if($date == $today)
                {
                    $count_of_transactions +=1;
                }
            }
            elseif($request->filter_by_date == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $count_of_transactions +=1;
                }
            }
            elseif($request->filter_by_date == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $count_of_transactions +=1;
                }
            }
            elseif($request->filter_by_date == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $count_of_transactions +=1;
                }
            }
            elseif($request->filter_by_date == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $count_of_transactions +=1;
                }
            }
            elseif($request->filter_by_date == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $count_of_transactions +=1;
                }
            }
            else
            {
                if($date == $today)
                {
                    $count_of_transactions +=1;
                }
            }
        }
        //===============================================================

        //Count of All Users Based on Filter Date
        $users = User::get();
        $count_of_users = 0;
       // $array=[];
        foreach($users as $data)
        {
            $date = date('Y-m-d', strtotime($data->registration_Time));
            $dateMonth = date('m', strtotime($data->registration_Time));
           // array_push($array, $date);
            if($request->filter_by_date == 'today')
            {
                if($date == $today)
                {
                    $count_of_users +=1;
                }
            }
            elseif($request->filter_by_date == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $count_of_users +=1;
                }
            }
            elseif($request->filter_by_date == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $count_of_users +=1;
                }
            }
            elseif($request->filter_by_date == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $count_of_users +=1;
                }
            }
            elseif($request->filter_by_date == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $count_of_users +=1;
                }
            }
            elseif($request->filter_by_date == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $count_of_users +=1;
                }
            }
            else
            {
                if($date == $today)
                {
                    $count_of_users +=1;
                }
            }
        }
        //================================================================
        $traders = Trader::get();
        $count_of_traders = 0;
       // $array=[];
        foreach($traders as $data)
        {
            $date = $data->registeredAt->format('Y-m-d');
            $dateMonth = date('m', strtotime($data->registeredAt));
           // array_push($array, $date);
            if($request->filter_by_date == 'today')
            {
                if($date == $today)
                {
                    $count_of_traders +=1;
                }
            }
            elseif($request->filter_by_date == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $count_of_traders +=1;
                }
            }
            elseif($request->filter_by_date == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $count_of_traders +=1;
                }
            }
            elseif($request->filter_by_date == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $count_of_traders +=1;
                }
            }
            elseif($request->filter_by_date == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $count_of_traders +=1;
                }
            }
            elseif($request->filter_by_date == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $count_of_traders +=1;
                }
            }
            else
            {
                if($date == $today)
                {
                    $count_of_traders +=1;
                }
            }
        }
        // ==============================================================
        $providers = Provider::get();
        $count_of_providers = 0;
       // $array=[];
        foreach($providers as $data)
        {
            $date = date('Y-m-d', strtotime($data->registeredAt));
            $dateMonth = date('m', strtotime($data->registeredAt));
           // array_push($array, $date);
            if($request->filter_by_date == 'today')
            {
                if($date == $today)
                {
                    $count_of_providers +=1;
                }
            }
            elseif($request->filter_by_date == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $count_of_providers +=1;
                }
            }
            elseif($request->filter_by_date == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $count_of_providers +=1;
                }
            }
            elseif($request->filter_by_date == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $count_of_providers +=1;
                }
            }
            elseif($request->filter_by_date == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $count_of_providers +=1;
                }
            }
            elseif($request->filter_by_date == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $count_of_providers +=1;
                }
            }
            else
            {
                if($date == $today)
                {
                    $count_of_providers +=1;
                }
            }
        }
        // ==============================================================
        $services = Service::get();
        $count_of_services = 0;
       // $array=[];
        foreach($services as $data)
        {
            $date = date('Y-m-d', strtotime($data->creationTime));
            $dateMonth = date('m', strtotime($data->creationTime));
           // array_push($array, $date);
            if($request->filter_by_date == 'today')
            {
                if($date == $today)
                {
                    $count_of_services +=1;
                }
            }
            elseif($request->filter_by_date == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $count_of_services +=1;
                }
            }
            elseif($request->filter_by_date == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $count_of_services +=1;
                }
            }
            elseif($request->filter_by_date == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $count_of_services +=1;
                }
            }
            elseif($request->filter_by_date == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $count_of_services +=1;
                }
            }
            elseif($request->filter_by_date == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $count_of_services +=1;
                }
            }
            else
            {
                if($date == $today)
                {
                    $count_of_services +=1;
                }
            }
        }

        // ==============================================================
        $traders_services = Trader_Service::get();
        $count_of_traders_services = 0;
       // $array=[];
        foreach($traders_services as $data)
        {
            $date = date('Y-m-d', strtotime($data->creationTime));
            $dateMonth = date('m', strtotime($data->creationTime));
           // array_push($array, $date);
            if($request->filter_by_date == 'today')
            {
                if($date == $today)
                {
                    $count_of_traders_services +=1;
                }
            }
            elseif($request->filter_by_date == 'yesterday')
            {
                if($date == $yesterday)
                {
                    $count_of_traders_services +=1;
                }
            }
            elseif($request->filter_by_date == 'last_7_days')
            {
                if($date >= $last_7_days && $date <= $today)
                {
                    $count_of_traders_services +=1;
                }
            }
            elseif($request->filter_by_date == 'last_30_days')
            {
                if($date >= $last_30_days && $date <= $today)
                {
                    $count_of_traders_services +=1;
                }
            }
            elseif($request->filter_by_date == 'last_month')
            {
                if($dateMonth == $last_month)
                {
                    $count_of_traders_services +=1;
                }
            }
            elseif($request->filter_by_date == 'this_month')
            {
                if($dateMonth == $this_month)
                {
                    $count_of_traders_services +=1;
                }
            }
            else
            {
                if($date == $today)
                {
                    $count_of_traders_services +=1;
                }
            }
        }
        // ==============================================================
        $unactive_cities = City::whereActive(0)->count();
        $unactive_fields = Field::whereActive(0)->count();
        $disapproved_services = Service::whereApproved(0)->count();
        //================================================================

        //Transactions of wallet
        $wallets = Wallet::where('test_wallet', 0)->get();


        return view('index', compact('count_of_transactions', 'count_of_users', 'count_of_traders','count_of_providers','count_of_services','count_of_traders_services', 'unactive_cities', 'unactive_fields', 'disapproved_services', 'wallets'));
        
    }
    
}
