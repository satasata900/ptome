<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{

    public function index() {
        $users = Admin::all();
        $roles = Role::orderBy('created_at','DESC')->get();
        $permissions = Permission::orderBy('created_at','DESC')->paginate(10);
        return view('admin.permissions.index',compact('permissions','users','roles'));
    }

    public function add() {
        return view('admin.permissions.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'name' => ['required', 'unique:permissions'],
            'guard_name' => ['required'],
        ],[
            'name.required' => 'Permission name is required',
            'name.unique' => 'A Permission with the same name already exists',
            'guard_name.required' => 'Guard name is required',
        ]);
        $permission = new Permission;
        $permission->name = $data['name'];
        $permission->guard_name = $data['guard_name'];
        if($permission->save()){
            toast('Permission added successfully','success');
            return redirect()->route('permissions');
        }
    }

    public function edit($id) {
        $permission = Permission::find($id);
        if(is_null($permission)){
            return abort(404);
        }
        return view('admin.permissions.edit',compact('permission'));
    }

    public function update($id) {
        $data = request()->all();
        $permission = Permission::find($id);
        if(is_null($permission)){
            return abort(404);
        }
        $validatedData = request()->validate([
            'name' => ['required', Rule::unique('permissions')->ignore($permission->id)],
            'guard_name' => ['required'],
        ],[
            'name.required' => 'Permission name is required',
            'name.unique' => 'A Permission with the same name already exists',
            'guard_name.required' => 'Guard name is required',
        ]);
        $permission->name = $data['name'];
        $permission->guard_name = $data['guard_name'];
        if($permission->update()){
            toast('Role updated successfully','success');
            return redirect()->route('permissions');
        }
    }

    public function delete($id) {
        $permission = Permission::find($id);
        if(is_null($permission)){
            return abort(404);
        }
        if($permission->delete()){
            toast('Permission deleted successfully','success');
        }
        return redirect()->route('permissions');
    }

    public function search(Request $request){
        $users = Admin::get();
        $roles = Role::orderBy('created_at','DESC')->get();
        $permissions = Permission::orderBy('created_at','DESC');
        if(isset($request->id)){
            $permissions = $permissions->where('id', $request->id);
        }
        if(isset($request->name)){
            $permissions = $permissions->where('name','LIKE','%' .$request->name. '%');
        }
        $permissions = $permissions->paginate(10);
        return view('admin.permissions.index',compact('permissions','users','roles'));
    }

}
