<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{

    public function index() {
        $users = Admin::orderBy('created_at','DESC')->where('id','!=', 1)->paginate(50);
        $roles = Role::orderBy('created_at','DESC')->get();
        $permissions = Permission::orderBy('created_at','DESC')->get();
        return view('admin.users.index',compact('roles','users','permissions'));
    }

    public function getSuspendedUsers() {
        $users = Admin::where('suspended',1)->orderBy('created_at','DESC')->where('id','!=', 1)->paginate(50);
        $roles = Role::orderBy('created_at','DESC')->get();
        $permissions = Permission::orderBy('created_at','DESC')->get();
        return view('admin.users.index',compact('roles','users','permissions'));
    }

    public function add() {
        return view('admin.users.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'fullName' => ['required'],
            'email' => ['required', 'unique:users'],
            'user_email' => ['unique:users'],
            'password' => ['required'],
            'lang' => ['required'],
            'img' => ['mimes:jpg.jpeg,png','max:5000']
        ],[
            'fullName.required' => 'Full Name is required',
            'email.required' => 'Email Address is required',
            'user_email.unique' => 'App Email already exists for another user',
            'password.required' => 'Password is required',
            'email.unique' => 'Email already exists',
            'lang.required' => 'Language is required',
            'img.max' => 'File size must be <= 5 MB'
        ]);
        $user = new User();
        $user->fullName = $data['fullName'];
        $user->email = $data['email'];
        $user->password = md5($data['password']);
        $user->lang = $data['lang'];

        $image = $request->file('img');
        $finalImage = uploadImage($image);
        $user->avatar = $finalImage;

        if(isset($data['suspended'])){
            $user->suspended = 1;
        }
        if(isset($data['user_email'])){
            $user->user_email = $data['user_email'];
        }

        $user->created_by = Auth::user()->id;

        if($user->save()){
            toast('User added successfully','success');
            return redirect()->route('system_users');
        }
    }

    public function edit($id) {
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        return view('admin.users.edit',compact('user'));
    }

    public function editPrivileges($id) {
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $roles = Role::get();
        $permissions = Permission::get();
        $userRoles = $user->getRoleNames()->toArray();
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        return view('admin.users.permissions',compact('user' , 'roles' , 'permissions' ,'userRoles','userPermissions'));
    }

    public function update($id) {

        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $data = request()->all();
        $validatedData = request()->validate([
            'fullName' => ['required'],
            'email' => ['required', Rule::unique('users')->ignore($user->id)],
            'user_email' => [Rule::unique('users')->ignore($user->id)],
            'lang' => ['required'],
            'img' => ['mimes:jpg.jpeg,png','max:5000']
        ],[
            'fullName.required' => 'Full Name is required',
            'email.required' => 'Email Address is required',
            'email.unique' => 'Email already exists',
            'user_email.unique' => 'App Email already exists',
            'lang.required' => 'Language is required',
            'img.max' => 'File size must be <= 5 MB'
        ]);
        $user->fullName = $data['fullName'];
        $user->email = $data['email'];
        if(isset($data['password'])){
            $user->password = md5($data['password']);
        }

        $user->lang = $data['lang'];

        $image = request()->file('img');

        if(!is_null($image)){
            $finalImage = uploadImage($image);
            $user->avatar = $finalImage;
            $old_img = $data['old_img'];
            if(!is_null($old_img) && file_exists($old_img)){
                unlink($old_img);
            }
        }

        if(isset($data['suspended']) && Auth::user()->id != $id){
            $user->suspended = 1;
        }
        else{
            $user->suspended = 0;
        }

        if(isset($data['user_email'])){
            $user->user_email = $data['user_email'];
        }
        if($user->save()){
            toast('User updated successfully','success');
            return redirect()->route('system_users_edit',$user->id);
        }
    }

    public function updateUserRoles($id) {
        $data = request()->except('_method','_token');
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $selectedRoleIDs = [];
        foreach($data as $input){
            array_push($selectedRoleIDs , $input);
        }
        $targetRoles = Role::whereIn('id',$selectedRoleIDs)->get();
        $excludedRoles = Role::whereNotIn('id',$selectedRoleIDs)->get();
        if(count($excludedRoles) > 0){
            foreach($excludedRoles as $excludedRole){
                $user->removeRole($excludedRole);
            }
        }
        if(count($targetRoles) > 0){
            foreach($targetRoles as $targetRole){
                $user->assignRole($targetRole);
            }
        }
        toast('User Role updated successfully','success');
        return redirect()->back();
    }

    public function updateUserPermissions($id) {
        $data = request()->except('_method','_token');
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        $selectedPermissionsIDs = [];
        foreach($data as $input){
            array_push($selectedPermissionsIDs , $input);
        }
        $targetPermissions = Permission::whereIn('id',$selectedPermissionsIDs)->get();
        $user->syncPermissions($targetPermissions);
        toast('Permissions updated successfully','success');
        return redirect()->back();
    }

    public function delete($id) {
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        if(Auth::user()->id == $id){
            alert()->error('Invalid Operation','You are not allowed to delete yourself');
            return redirect()->route('system_users');
        }
        if(file_exists($user->avatar)){
            unlink($user->avatar);
        }
        $user->update(['avatar'=>null]);
        if($user->delete()){
            toast('User deleted successfully','success');
        }
        return redirect()->route('system_users');
    }

    public function suspend($id) {
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        if(Auth::user()->id == $id){
            alert()->error('Invalid Operation','You are not allowed to suspend yourself');
            return redirect()->route('system_users');
        }
        $user->suspended = 1;
        if($user->save()){
            toast('User suspended successfully','success');
        }
        return redirect()->route('system_users');
    }

    public function suspendSelected(Request $request) {
        $ids = $request->ids;
        $targetUsers = Admin::whereIn('id',$ids)->where('suspended',0)->where('id','!=',Auth::user()->id);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetUsers->update(['suspended'=>1])){
            toast('Users suspended successfully','success');
        }
        return redirect()->route('system_users');
    }

    public function allow($id) {
        $user = Admin::find($id);
        if(is_null($user)){
            return abort(404);
        }
        if(Auth::user()->id == $id){
            alert()->error('Invalid Operation','You are not allowed to allow or suspend yourself');
            return redirect()->route('system_users');
        }
        $user->suspended = 0;
        if($user->save()){
            toast('User allowed successfully','success');
        }
        return redirect()->route('system_users');
    }

    public function deleteSelected() {

    }

    public function search(Request $request){
        $users = Admin::orderBy('created_at','DESC');
        $permissions = Permission::orderBy('created_at','DESC')->get();
        $roles = Role::get();
        if(isset($request->id)){
            $users = $users->where('id', $request->id);
        }
        if(isset($request->name)){
            $users = $users->where('fullName','LIKE','%' .$request->name. '%');
        }
        if(isset($request->email)){
            $users = $users->where('email','LIKE','%' .$request->email. '%');
        }
        $users = $users->paginate(50);
        return view('admin.users.index',compact('roles','users','permissions'));
    }

}
