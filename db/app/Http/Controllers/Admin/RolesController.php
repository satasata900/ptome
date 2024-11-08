<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{

    public function index() {
        $users = Admin::all();
        $roles = Role::orderBy('created_at','DESC')->paginate(10);
        $permissions = Permission::orderBy('created_at','DESC')->get();
        return view('admin.roles.index',compact('roles','users','permissions'));
    }

    public function add() {
        return view('admin.roles.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'name' => ['required', 'unique:roles'],
            'guard_name' => ['required'],
        ],[
            'name.required' => 'Role name is required',
            'name.unique' => 'A role with the same name already exists',
            'guard_name.required' => 'Guard name is required',
        ]);
        $role = new Role;
        $role->name = $data['name'];
        $role->guard_name = $data['guard_name'];
        if($role->save()){
            toast('Role added successfully','success');
            return redirect()->route('roles');
        }
    }

    public function edit($id) {
        $role = Role::find($id);
        if(is_null($role)){
            return abort(404);
        }
        return view('admin.roles.edit',compact('role'));
    }

    public function editPermissions($id) {
        $role = Role::find($id);
        $permissions = Permission::get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        if(is_null($role)){
            return abort(404);
        }
        return view('admin.roles.permissions',compact('role','permissions','rolePermissions'));
    }

    public function update($id) {
        $data = request()->all();
        $role = Role::find($id);
        if(is_null($role)){
            return abort(404);
        }
        $validatedData = request()->validate([
            'name' => ['required', Rule::unique('roles')->ignore($role->id)],
            'guard_name' => ['required'],
        ],[
            'name.required' => 'Role name is required',
            'name.unique' => 'A role with the same name already exists',
            'guard_name.required' => 'Guard name is required',
        ]);
        $role->name = $data['name'];
        $role->guard_name = $data['guard_name'];
        if($role->update()){
            toast('Role updated successfully','success');
            return redirect()->route('roles');
        }
    }

    public function updatePermissions($id) {
        $data = request()->except('_method','_token');
        $role = Role::find($id);
        if(is_null($role)){
            return abort(404);
        }
        $selectedPermissionsIDs = [];
        foreach($data as $input){
            array_push($selectedPermissionsIDs , $input);
        }
        $targetPermissions = Permission::whereIn('id',$selectedPermissionsIDs)->get();
        $excludedPermissions = Permission::whereNotIn('id',$selectedPermissionsIDs)->get();
        $role->syncPermissions($targetPermissions);
        toast('Role permissions updated successfully','success');
        return redirect()->back();
    }

    public function delete($id) {
        $role = Role::find($id);
        if(is_null($role)){
            return abort(404);
        }
        if($role->delete()){
            toast('Role deleted successfully','success');
        }
        return redirect()->route('roles');
    }

    public function deleteSelected() {

    }

    public function search(Request $request){
        $users = Admin::all();
        $permissions = Permission::orderBy('created_at','DESC')->get();
        $roles = Role::orderBy('created_at','DESC');
        if(isset($request->id)){
            $roles = $roles->where('id', $request->id);
        }
        if(isset($request->name)){
            $roles = $roles->where('name','LIKE','%' .$request->name. '%');
        }
        $roles = $roles->paginate(10);
        return view('admin.roles.index',compact('roles','users','permissions'));
    }

}
