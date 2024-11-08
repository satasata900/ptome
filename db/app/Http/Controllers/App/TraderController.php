<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Trader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TraderController extends Controller
{

    public function index() {
        $trader = Trader::paginate(10);
        return view('app.trader.index',compact('trader'));
    }

   

    public function view($id) {
        $trader = Trader::find($id);
        $trader_services_count = \DB::connection('mysql2')->table('traders_services')->where('trader_id', $trader->id)->count();
        if(is_null($trader)){
            return abort(404);
        }
        return view('app.trader.view',compact('trader','trader_services_count'));
    }

 
    public function delete($id) {
        $trader = Trader::find($id);
        if(is_null($trader)){
            return abort(404);
        }
        if($trader->delete()){
            toast('trader deleted successfully','success');
        }
        return redirect()->route('trader');
    }


    public function search(Request $request){
        $trader = Trader::orderBy('id','ASC');

        if(isset($request->id)){
            $trader = $trader->where('id', $request->id);
        }
        if(isset($request->trader_name)){
            $trader = $trader->where('trader_name','LIKE','%' .$request->trader_name. '%');
        }
        $trader = $trader->paginate(10);
        return view('app.trader.index',compact('trader'));
    }



    //send notification for all traders
    public function send_notification_for_all_traders() {
        return view('app.trader.send_notification_for_all_traders');
    }

    //send_notification_for_all_traders_post
    public function send_notification_for_all_traders_post(Request $request)
    {
        $data = $request->all();
        $validatedData = $request->validate([
            'title' => ['required'],
            'message' => ['required'],
        ]);

         $traders= Trader::pluck('user_id');

        $tokens = \DB::connection('mysql2')->table('tokens')->whereIn('user_id',$traders)->pluck('platform_token');
        $this->sendWebNotification($data['title'], $data['message'], $tokens);
        toast('Notifications Sent successfully','success');
        return redirect()->route('trader');

    }




}
