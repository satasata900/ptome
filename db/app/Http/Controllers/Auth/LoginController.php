<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function login(Request $request){

        $credentials = $request->all();
        $adminEmail = Admin::where('email', $credentials['email'])->where('suspended',0)->first();
        $admin = Admin::where('email', $credentials['email'])->where('password',md5($credentials['password']))->where('suspended',0)->first();

        $remember = false;

        if(isset($credentials['remember_token']) && $credentials['remember_token'] == "on"){
            $remember = true;
        }

        $validator = Validator::make($request->all(),
            array(
                'email' => 'required',
                'password' => 'required',
            ));

        if ($validator->fails()) {
            Session::flash('error', "Email and password are required");
            return redirect()->route('login');
        }

        if(!is_null($adminEmail)){
            if(config('app.max_invalid_login_attempts') < $adminEmail->invalid_login_attempts){
                Session::flash('error', "Maximum invalid logins exceeded");
                return redirect()->route('login');
            }
        }

        if(is_null($admin)){
            if(!is_null($adminEmail)){
                $adminEmail->invalid_login_attempts = $adminEmail->invalid_login_attempts + 1;
                $adminEmail->save();
            }
            Session::flash('error', "Email or password is incorrect");
            return redirect()->route('login');
        }
        if(config('app.max_invalid_login_attempts') >= $admin->invalid_login_attempts){
            $admin->invalid_login_attempts = 0;
            $admin->save();
        }
        Auth::login($admin,$remember);
        toast('You\'ve logged in successfully','success');
        return redirect()->route('dashboard');

    }

    public function logout(){
        Auth::logout();
        Session::flash('logout_message','You are logged out successfully');
        return redirect()->route('login');
    }

}
