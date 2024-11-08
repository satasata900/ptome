<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{

    public function index() {
        $organization = Organization::paginate(50);
        return view('app.organization.index',compact('organization'));
    }

   

    public function view($id) {
        $organization = Organization::find($id);
        $organization_invoices_count = \DB::connection('mysql2')->table('organizations_invoices')->where('organization_id', $organization->id)->count();
        if(is_null($organization)){
            return abort(404);
        }
        return view('app.organization.view',compact('organization','organization_invoices_count'));
    }

 
    public function delete($id) {
        $organization = Organization::find($id);
        if(is_null($organization)){
            return abort(404);
        }
        if($organization->delete()){
            toast('organization deleted successfully','success');
        }
        return redirect()->route('organization');
    }


    public function search(Request $request){
        $organization = Organization::orderBy('id','ASC');

        if(isset($request->id)){
            $organization = $organization->where('id', $request->id);
        }
        if(isset($request->organization_name)){
            $organization = $organization->where('organization_name','LIKE','%' .$request->organization_name. '%');
        }
        $organization = $organization->paginate(50);
        return view('app.organization.index',compact('organization'));
    }

}
