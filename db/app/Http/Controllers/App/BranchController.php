<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Branch;

class BranchController extends Controller
{

    public function index() {
        $branches = Branch::orderBy('id','DESC')->paginate(50);
        return view('app.branches.index',compact('branches'));
    }

    public function add() {
        return view('app.branches.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'branch_name' => ['required','max:255'],
            'address' => ['required','max:255'],
            'lat' => ['required'],
            'lon' => ['required'],
        ]);


        $branch = new Branch();
        $branch->branch_name = $data['branch_name'];
        $branch->address = $data['address'];
        $branch->lat = $data['lat'];
        $branch->lon = $data['lon'];

        if($branch->save()){
            toast('Branch added successfully','success');
            return redirect()->route('branches');
        }

    }

    public function edit($id) {
        $branch = Branch::find($id);
        if(is_null($branch)){
            return abort(404);
        }
        return view('app.branches.edit',compact('branch'));
    }

    public function update(Request $request , $id) {

        $branch = Branch::find($id);
        if(is_null($branch)){
            return abort(404);
        }

        $data = $request->all();
        $validatedData = $request->validate([
            'branch_name' => ['required','max:255'],
            'address' => ['required','max:255'],
            'lat' => ['required'],
            'lon' => ['required'],
        ]);
        $branch->branch_name = $data['branch_name'];
        $branch->address = $data['address'];
        $branch->lat = $data['lat'];
        $branch->lon = $data['lon'];
        if($branch->save()){
            toast('Branch updated successfully','success');
            return redirect()->route('branches_edit',$branch->id);
        }

    }

    public function delete($id) {
        $branch = Branch::find($id);
        if(is_null($branch)){
            return abort(404);
        }
        if($branch->delete()){
            toast('Branch deleted successfully','success');
        }
        return redirect()->route('branches');
    }

   

    public function search(Request $request){
        $branches = Branch::orderBy('id','DESC');

        if(isset($request->branch_name)){
            $branches = $branches->where('branch_name' , 'LIKE', '%' . $request->branch_name . '%');
        }

        if(isset($request->address)){
            $branches = $branches->where('address','LIKE','%' .$request->address. '%');
        }

        $branches = $branches->paginate(50);
        return view('app.branches.index',compact('branches'));
    }

}
