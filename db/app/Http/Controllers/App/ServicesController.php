<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServicesController extends Controller
{

    public function index() {
        $services = DB::connection('mysql2')->table('services')
              ->join('providers','services.provider_id','=','providers.id')
              ->join('users','providers.user_id','=','users.id')
             ->join('wallets','services.wallet_id','=','wallets.id')
             ->join('cities','services.city_id','=','cities.id')
             ->join('fields','services.field_id','=','fields.id')
            ->select('services.*', 'providers.provider_name', 'users.user_name', 'wallets.wallet_name', 'cities.city_en_name', 'fields.filed_en_name')
            ->paginate(50);

        $allServices = DB::connection('mysql2')->table('services')->count();
        $activeServices = DB::connection('mysql2')->table('services')->whereActive(1)->count();
        $unActiveServices = DB::connection('mysql2')->table('services')->whereActive(0)->count();
        $approvedServices = DB::connection('mysql2')->table('services')->whereApproved(1)->count();
        $disApprovedServices = DB::connection('mysql2')->table('services')->whereApproved(0)->count();

        return view('app.services.index',compact('services', 'allServices','activeServices','unActiveServices','approvedServices','disApprovedServices'));
    }

   

    public function edit($id) {
        
        $service = DB::connection('mysql2')->table('services')
              ->join('providers','services.provider_id','=','providers.id')
              ->join('users','providers.user_id','=','users.id')
             ->join('wallets','services.wallet_id','=','wallets.id')
             ->join('cities','services.city_id','=','cities.id')
             ->join('fields','services.field_id','=','fields.id')
            ->select('services.*', 'providers.provider_name', 'providers.user_id', 'users.user_name','wallets.wallet_name', 'cities.city_en_name', 'fields.filed_en_name')
            ->where('services.id',$id)->first();

        $service_member_count = \DB::connection('mysql2')->table('service_members')->where('service_id', $id)->count();

        if(is_null($service)){
            return abort(404);
        }
        return view('app.services.edit',compact('service','service_member_count'));
    }

   

    public function delete($id) {
        $service_details = DB::connection('mysql2')->table('services')->whereId($id);
        $service = $service_details->first();
        if(is_null($service)){
            return abort(404);
        }
        if($service_details->delete()){
            toast('Service deleted successfully','success');
        }
        return redirect()->route('services');
    }

    public function deactivate($id) {
        $service = Service::find($id);
        if(is_null($service)){
            return abort(404);
        }
        $service->active = 0;
        if($service->save()){
            toast('Service deactivated successfully','success');
        }
        return redirect()->back();
    }

    public function activate($id) {
        $service = Service::find($id);
        if(is_null($service)){
            return abort(404);
        }
        $service->active = 1;
        if($service->save()){
            toast('Service activated successfully','success');
        }
        return redirect()->back();
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
        $userWallets = DB::connection('mysql2')->table('users_wallets')
            ->join('users','users_wallets.user_id','=','users.id')
            ->join('wallets','users_wallets.wallet_id','=','wallets.id')
            ->select('users_wallets.id', 'users.user_name','users.email', 'wallets.wallet_name','wallets.price');
        if(isset($request->id)){
            $userWallets = $userWallets->where('users_wallets.id', $request->id);
        }
        if(isset($request->email)){
            $userWallets = $userWallets->where('users.email' , 'LIKE', '%' . $request->email . '%');
        }
        if(isset($request->name)){
            $userWallets = $userWallets->where('users.user_name','LIKE','%' .$request->name. '%');
        }
        if(isset($request->wallet_name)){
            $userWallets = $userWallets->where('wallets.wallet_name','LIKE','%' .$request->wallet_name. '%');
        }
        $userWallets = $userWallets->paginate(50);
        return view('app.users_wallets.index',compact('userWallets'));
    }




    public function disapproved($id) {
        $service = Service::find($id);
        if(is_null($service)){
            return abort(404);
        }
        $service->approved = 0;
        if($service->save()){
            toast('Service Disapproved successfully','success');
        }
        return redirect()->back();
    }

    public function approved($id) {
        $service = Service::find($id);
        if(is_null($service)){
            return abort(404);
        }
        $service->approved = 1;
        if($service->save()){
            toast('Service Approved successfully','success');
        }
        return redirect()->back();
    }



    //send notification for all users
    public function send_notification_for_all_members_in_service($id) {
        $service = Service::find($id);
        if(is_null($service)){
            return abort(404);
        }

        $service_members= \DB::connection('mysql2')->table('service_members')->where('service_id', $service->id)->get();

        return view('app.services.send_notification_for_all_members_in_service', compact('service', 'service_members'));
    }

    //send_notification_for_all_members_in_service_post
    public function send_notification_for_all_members_in_service_post(Request $request,$id)
    {
        $service = Service::find($id);
        if(is_null($service)){
            return abort(404);
        }
        $data = $request->all();
        $validatedData = $request->validate([
            'title' => ['required'],
            'message' => ['required'],
        ]);

        $service_members= \DB::connection('mysql2')->table('service_members')->where('service_id', $service->id)->pluck('member_id');

        $tokens = \DB::connection('mysql2')->table('tokens')->whereIn('user_id',$service_members)->pluck('platform_token');

        $this->sendWebNotification($data['title'], $data['message'], $tokens);
        toast('Notifications Sent successfully','success');
        return redirect()->route('services');

    }



}
