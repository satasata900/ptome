<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\City;

class ProvidersController extends Controller
{

    public function index() {
        $providers = Provider::orderBy('id','DESC')->paginate(50);
        $confirmedProviders = Provider::where('confirmed_provider',1)->get();
        $approvedProviders = Provider::where('approved_provider',1)->get();
        return view('app.providers.index',compact('providers','confirmedProviders','approvedProviders'));
    }

    public function add() {
        $users = User::get();
        return view('app.providers.add',compact('users'));
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'providerName' => ['required:mysql2.providers,provider_name'],
            'username' => ['required:mysql2.providers'],
            'mobile' => ['required:mysql2.providers'],
            'img' => ['mimes:jpg.jpeg,png','max:5000']
        ],[
            'providerName.required' => "Provider name is required",
            'username.required' => "Username is required",
            'mobile.required' => "Mobile number is required",
            'img.max' => 'File size must be <= 5 MB'
        ]);


        $provider = new Provider();
        $provider->provider_name = $data['providerName'];
        $provider->provider_phone = $data['mobile'];
        $provider->user_id = $data['username'];
        $provider->registeredAt = date('Y-m-d H:i:s');

        $image = $request->file('img');
        $imageName = uploadAppImage($image,'providers/');
        $provider->provider_image = 'providers/' . $imageName;

        if(isset($data['approved'])){
            $provider->approved_provider = 1;
        }
        else{
            $provider->approved_provider = 0;
        }
        if(isset($data['confirmed'])){
            $provider->confirmed_provider = 1;
            $provider->approved_provider = 1;
        }
        else{
            $provider->confirmed_provider = 0;
        }

        if($provider->save()){
            toast('Provider added successfully','success');
            return redirect()->route('providers');
        }

    }

    public function edit($id) {
        $provider = Provider::find($id);
        if(is_null($provider)){
            return abort(404);
        }
        $users = User::get();
        return view('app.providers.edit',compact('provider','users'));
    }

    public function update(Request $request , $id) {

        $provider = Provider::find($id);
        if(is_null($provider)){
            return abort(404);
        }

        $data = $request->all();
        $validatedData = $request->validate([
            'providerName' => ['required:mysql2.providers,provider_name'],
            'username' => ['required:mysql2.providers'],
            'mobile' => ['required:mysql2.providers'],
            'img' => ['mimes:jpg.jpeg,png','max:5000']
        ],[
            'providerName.required' => "Provider name is required",
            'username.required' => "Username is required",
            'mobile.required' => "Mobile number is required",
            'img.max' => 'File size must be <= 5 MB'
        ]);

        $provider->provider_name = $data['providerName'];
        $provider->provider_phone = $data['mobile'];
        $provider->user_id = $data['username'];

        $image = $request->file('img');

        if(!is_null($image)){
            $imageName = uploadAppImage($image,'providers/');
            $provider->provider_image = 'providers/' . $imageName;
            $old_img = $data['old_img'];
            if(!is_null($old_img)){
                unlinkAppImage($old_img);
            }
        }

        if(isset($data['approved'])){
            $provider->approved_provider = 1;
        }
        else{
            $provider->approved_provider = 0;
        }
        if(isset($data['confirmed'])){
            $provider->confirmed_provider = 1;
            $provider->approved_provider = 1;
        }
        else{
            $provider->confirmed_provider = 0;
        }

        if($provider->save()){
            toast('Provider added successfully','success');
            return redirect()->route('providers');
        }

    }

    public function delete($id) {
        $provider = Provider::find($id);
        if(is_null($provider)){
            return abort(404);
        }
        if($provider->delete()){
            toast('Provider deleted successfully','success');
        }
        return redirect()->route('providers');
    }

    public function approveState($id) {
        $provider = Provider::find($id);
        if(is_null($provider)){
            return abort(404);
        }
        if($provider->approved_provider == 1){
            $provider->approved_provider = 0;
            if($provider->save()){
                toast('Provider disapproved successfully','success');
                return redirect()->route('providers');
            }
        }
        else if($provider->approved_provider == 0){
            $provider->approved_provider = 1;
            if($provider->save()){
                toast('Provider approved successfully','success');
                return redirect()->route('providers');
            }
        }
    }

    public function confirmState($id) {
        $provider = Provider::find($id);
        if(is_null($provider)){
            return abort(404);
        }
        if($provider->confirmed_provider == 1){
            $provider->confirmed_provider = 0;
            if($provider->save()){
                toast('Provider disconfirmed successfully','success');
                return redirect()->route('providers');
            }
        }
        else if($provider->confirmed_provider == 0){
            $provider->confirmed_provider = 1;
            $provider->approved_provider = 1;
            if($provider->save()){
                toast('Provider confirmed successfully','success');
                return redirect()->route('providers');
            }
        }
    }

    public function search(Request $request){
        $providers = Provider::orderBy('id','DESC');
        if(isset($request->username)){
            $user = User::where('user_name',$request->username)->first();
            if(!is_null($user)){
                $providers = $providers->where('user_id' , $user->id);
            }
        }

        if(isset($request->name)){
            $providers = $providers->where('provider_name' , 'LIKE', '%' . $request->name . '%');
        }

        if(isset($request->mobile)){
            $providers = $providers->where('provider_phone','LIKE','%' .$request->mobile. '%');
        }

        if(isset($request->approved)){
            if($request->approved == 'approved'){
                $providers = $providers->where('approved_provider',1);
            }
            elseif($request->approved == 'unapproved'){
                $providers = $providers->where('approved_provider',0);
            }
        }
        if(isset($request->confirmed)){
            if($request->confirmed == 'confirmed'){
                $providers = $providers->where('confirmed_provider',1);
            }
            elseif($request->confirmed == 'unconfirmed'){
                $providers = $providers->where('confirmed_provider',0);
            }
        }
        $providers = $providers->paginate(50);
        $confirmedProviders = Provider::where('confirmed_provider',1)->get();
        $approvedProviders = Provider::where('approved_provider',1)->get();
        return view('app.providers.index',compact('providers','approvedProviders','confirmedProviders'));
    }



    //send notification for all providers
    public function send_notification_for_all_providers() {
        return view('app.providers.send_notification_for_all_providers');
    }

    //send_notification_for_all_providers_post
    public function send_notification_for_all_providers_post(Request $request)
    {
        $data = $request->all();
        $validatedData = $request->validate([
            'title' => ['required'],
            'message' => ['required'],
        ]);

         $providers= Provider::pluck('user_id');

        $tokens = \DB::connection('mysql2')->table('tokens')->whereIn('user_id',$providers)->pluck('platform_token');
        $this->sendWebNotification($data['title'], $data['message'], $tokens);
        toast('Notifications Sent successfully','success');
        return redirect()->route('providers');

    }

}
