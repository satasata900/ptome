<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use \Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UsersController extends Controller
{

    public function index(Request $request) {

        $today = Carbon::today()->format('Y-m-d'); //today
        $yesterday = Carbon::yesterday()->format('Y-m-d'); //yesterday
        $last_7_days = Carbon::now()->subDays(7)->format('Y-m-d'); //last_7_days
        $last_30_days = Carbon::now()->subDays(30)->format('Y-m-d'); //last_30_days
        $last_month = Carbon::now()->subMonth()->format('m'); //last_month
        $this_month = Carbon::now()->format('m'); //this_month


        $activeUsers = $users = User::where('active',1)->orderBy('id','DESC')->paginate(100);
        $inActiveUsers = $users = User::where('active',0)->orderBy('id','DESC')->paginate(100);
        $userss = User::orderBy('id','DESC')->get();


        if(isset($request->filter_by_date))
        {
            $users = [];
            foreach ($userss as $data) 
            {
                $date = date('Y-m-d', strtotime($data->registration_Time));
                $dateMonth = date('m', strtotime($data->registration_Time));
                if($request->filter_by_date == 'today')
                {
                    if($date == $today)
                    {
                        array_push($users, $data);
                    }
                }
                elseif($request->filter_by_date == 'yesterday')
                {
                    if($date == $yesterday)
                    {
                        array_push($users, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_7_days')
                {
                    if($date >= $last_7_days && $date <= $today)
                    {
                        array_push($users, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_30_days')
                {
                    if($date >= $last_30_days && $date <= $today)
                    {
                        array_push($users, $data);
                    }
                }
                elseif($request->filter_by_date == 'last_month')
                {
                    if($dateMonth == $last_month)
                    {
                        array_push($users, $data);
                    }
                }
                elseif($request->filter_by_date == 'this_month')
                {
                    if($dateMonth == $this_month)
                    {
                        array_push($users, $data);
                    }
                }
                elseif($request->filter_by_date == 'all')
                {
                 
                    array_push($users, $data);
                    
                }
                
            }
            
            $users = $this->custom_paginate($users);
        }
        else
        {
            $users = User::orderBy('id','DESC')->paginate(100);
        }



        return view('app.users.index',compact('users','inActiveUsers','activeUsers'));
    }

    function custom_paginate($data)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $collection = new Collection($data);
        $per_page = 1000;
        $currentPageResults = $collection->slice(($currentPage - 1) * $per_page, $per_page)->values();
        $data['results'] = new LengthAwarePaginator($currentPageResults, count($collection), $per_page);
        return $data['results']->setPath(request()->url());
    }

    public function getSuspendedUsers() {
        $users = User::where('suspended',1)->orderBy('id','DESC')->paginate(50);
        $roles = Role::orderBy('id','DESC')->get();
        $permissions = Permission::orderBy('id','DESC')->get();
        return view('Admin.users.index',compact('roles','users','permissions'));
    }

    // public function add() {
    //     return view('app.users.add');
    // }

    // public function store(Request $request) {
    //     $data = $request->all();
    //     $validatedData = $request->validate([
    //         'id' => ['unique:mysql2.users'],
    //         'full_name' => ['required'],
    //         'email' => ['required', 'unique:mysql2.users'],
    //         'user_name' => ['required','unique:mysql2.users'],
    //         'phone' => ['required', 'unique:mysql2.users'],
    //         'password' => ['required'],
    //         'birthdate' => ['required'],
    //         'isoCode' => ['required','max:4'],
    //         'pin_code' => ['required'],
    //     ],[
    //         'id.unique' => 'This ID already exists',
    //         'full_name.required' => 'Full Name is required',
    //         'email.required' => 'Email Address is required',
    //         'email.unique' => 'Email already exists',
    //         'user_name.required' => 'Username is required',
    //         'user_name.unique' => 'Username already exists for another user',
    //         'password.required' => 'Password is required',
    //         'isoCode.required' => 'Iso code is required',
    //         'isoCode.max' => 'Maximum iso code length is 4 characters',
    //         'pin_code.required' => 'Pin code is required',
    //     ]);
    //     $user = new User();

    //     if(isset($data['id']) && !is_null($data['id'])){
    //         $user->id = $data['id'];
    //     }

    //     $user->full_name = $data['full_name'];
    //     $user->email = $data['email'];
    //     $user->password = md5($data['password']);
    //     $user->isoCode = $data['isoCode'];
    //     $user->pin_code = $data['pin_code'];
    //     $user->phone = $data['phone'];
    //     $user->user_name = $data['user_name'];
    //     $user->birthdate = $data['birthdate'];
    //     $user->registration_Time = time();

    //     if(isset($data['active'])){
    //         $user->active = 1;
    //     }
    //     else{
    //         $user->active = 0;
    //     }

    //     if(isset($data['notifications'])){
    //         $user->notifications_on_off = 1;
    //     }
    //     else{
    //         $user->notifications_on_off = 0;
    //     }

    //     if(isset($data['verified_email'])){
    //         $user->verified_email = 1;
    //     }
    //     else{
    //         $user->verified_email = 0;
    //     }

    //     if(isset($data['verified_phone'])){
    //         $user->verified_phone = 1;
    //     }
    //     else{
    //         $user->verified_phone = 0;
    //     }

    //     if(isset($data['pincode_require'])){
    //         $user->pincode_require = 1;
    //     }
    //     else{
    //         $user->pincode_require = 0;
    //     }

    //     if(isset($data['test_mode'])){
    //         $user->test_mode = 1;
    //     }
    //     else{
    //         $user->test_mode = 0;
    //     }

    //     if($user->save()){
    //         toast('User added successfully','success');
    //         return redirect()->route('app_users');
    //     }
    // }

    // public function edit($id) {
    //     $user = User::find($id);
    //     if(is_null($user)){
    //         return abort(404);
    //     }
    //     return view('app.users.edit',compact('user'));
    // }


    public function view($id) {
        $user = User::find($id);
        $users_wallets = UserWallet::where('user_id', $user->id)->orderBy('id','desc')->get();
        $user_transactions = Transaction::where('sender_id', $user->id)->orWhere('recipient_id', $user->id)->orderBy('id','desc')->get();
        if(is_null($user)){
            return abort(404);
        }
        return view('app.users.view',compact('user', 'users_wallets', 'user_transactions'));
    }




    // public function update(Request $request , $id) {

    //     $user = User::find($id);
    //     if(is_null($user)){
    //         return abort(404);
    //     }
    //     $data = $request->all();
    //     $validatedData = $request->validate([
    //         'id' => [Rule::unique('mysql2.users')->ignore($user->id)],
    //         'full_name' => ['required'],
    //         'email' => ['required',  Rule::unique('mysql2.users')->ignore($user->id)],
    //         'user_name' => ['required', Rule::unique('mysql2.users')->ignore($user->id)],
    //         'phone' => ['required',  Rule::unique('mysql2.users')->ignore($user->id)],
    //         'birthdate' => ['required'],
    //         'isoCode' => ['required','max:4'],
    //         'pin_code' => ['required'],
    //     ],[
    //         'id.unique' => 'This ID already exists',
    //         'full_name.required' => 'Full Name is required',
    //         'email.required' => 'Email Address is required',
    //         'email.unique' => 'Email already exists',
    //         'user_name.required' => 'Username is required',
    //         'user_name.unique' => 'Username already exists for another user',
    //         'isoCode.required' => 'Iso code is required',
    //         'isoCode.max' => 'Maximum iso code length is 4 characters',
    //         'pin_code.required' => 'Pin code is required',
    //     ]);

    //     if(!is_null($data['id'])){
    //         $user->id = $data['id'];
    //     }

    //     $user->full_name = $data['full_name'];
    //     $user->email = $data['email'];
    //     $user->isoCode = $data['isoCode'];
    //     $user->pin_code = $data['pin_code'];
    //     $user->phone = $data['phone'];
    //     $user->user_name = $data['user_name'];
    //     $user->birthdate = $data['birthdate'];
    //     $user->registration_Time = time();

    //     if(isset($data['password']) && !is_null($data['password'])){
    //         $user->password = md5($data['password']);
    //     }

    //     if(isset($data['active'])){
    //         $user->active = 1;
    //     }
    //     else{
    //         $user->active = 0;
    //     }

    //     if(isset($data['notifications'])){
    //         $user->notifications_on_off = 1;
    //     }
    //     else{
    //         $user->notifications_on_off = 0;
    //     }

    //     if(isset($data['verified_email'])){
    //         $user->verified_email = 1;
    //     }
    //     else{
    //         $user->verified_email = 0;
    //     }

    //     if(isset($data['verified_phone'])){
    //         $user->verified_phone = 1;
    //     }
    //     else{
    //         $user->verified_phone = 0;
    //     }

    //     if(isset($data['pincode_require'])){
    //         $user->pincode_require = 1;
    //     }
    //     else{
    //         $user->pincode_require = 0;
    //     }

    //     if(isset($data['test_mode'])){
    //         $user->test_mode = 1;
    //     }
    //     else{
    //         $user->test_mode = 0;
    //     }

    //     if($user->save()){
    //         toast('User updated successfully','success');
    //         return redirect()->route('app_users_edit',$user->id);
    //     }
    // }

    public function delete($id) {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        if(Transaction::where('sender_id', $user->id)->orWhere('recipient_id', $user->id)->exists())
        {
            toast('Sorry, User can"t deleted because have some transactions ','error');
        }
        else
        {
            if($user->delete()){
                toast('User deleted successfully','success');
            }
        }
        
        return redirect()->route('app_users');
    }

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
        $activeUsers = $users = User::where('active',1)->orderBy('id','DESC')->paginate(100);
        $inActiveUsers = $users = User::where('active',0)->orderBy('id','DESC')->paginate(100);
        $users = User::orderBy('id','DESC');
        if(isset($request->fullname)){
            $users = $users->where('full_name','LIKE','%' .$request->fullname. '%');
        }
        if(isset($request->name)){
            $users = $users->where('user_name','LIKE','%' .$request->name. '%');
        }
        if(isset($request->email)){
            $users = $users->where('email','LIKE','%' .$request->email. '%');
        }

        if(isset($request->state) && !is_null($request->state)){
            if($request->state == "active"){
                $users = $users->where('active',1);
            }
            else if($request->state == "inactive"){
                $users = $users->where('active',0);
            }
        }
        $users = $users->paginate(100);
        return view('app.users.index',compact('activeUsers','users','inActiveUsers'));
    }





    //send notification
    public function send_notification($id) {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        return view('app.users.send_notification',compact('user'));
    }

    public function send_notification_post(Request $request, $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $data = $request->all();
        $validatedData = $request->validate([
            'title' => ['required'],
            'message' => ['required'],
        ]);

        $tokens = \DB::connection('mysql2')->table('tokens')->where('user_id', $user->id)->pluck('platform_token');
        $this->sendWebNotification($data['title'], $data['message'], $tokens);

        toast('Notification Sent successfully','success');
        return redirect()->route('app_users');

    }


    //send notification for all users
    public function send_notification_for_all_users() {
        return view('app.users.send_notification_for_all_users');
    }

    //send_notification_for_all_users_post
    public function send_notification_for_all_users_post(Request $request)
    {
        $data = $request->all();
        $validatedData = $request->validate([
            'title' => ['required'],
            'message' => ['required'],
        ]);

        $tokens = \DB::connection('mysql2')->table('tokens')->pluck('platform_token');
        $this->sendWebNotification($data['title'], $data['message'], $tokens);
        toast('Notifications Sent successfully','success');
        return redirect()->route('app_users');

    }



}
