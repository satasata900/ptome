<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\City;

class CitiesController extends Controller
{

    public function index() {
        $cities = City::orderBy('id','DESC')->paginate(50);
        $activeCities = City::where('active',1)->get();
        $inactiveCities = City::where('active',0)->get();
        return view('app.cities.index',compact('cities','activeCities','inactiveCities'));
    }

    public function add() {
        return view('app.cities.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'name_ar' => ['required:cities,city_ar_name','unique:mysql2.cities,city_ar_name'],
            'name_en' => ['required:cities,city_en_name','unique:mysql2.cities,city_en_name'],
        ],[
            'name_ar.required' => "Arabic name is required",
            'name_ar.unique' => "A city with the same AR name already exists",
            'name_en.required' => "English name is required",
            'name_en.unique' => "A city with the same EN name already exists",
        ]);


        $city = new City();
        $city->city_ar_name = $data['name_ar'];
        $city->city_en_name = $data['name_en'];


        if(isset($data['active'])){
            $city->active = 1;
        }
        else{
            $city->active = 0;
        }

        if($city->save()){
            toast('City added successfully','success');
            return redirect()->route('cities');
        }

    }

    public function edit($id) {
        $city = City::find($id);
        if(is_null($city)){
            return abort(404);
        }
        return view('app.cities.edit',compact('city'));
    }

    public function update(Request $request , $id) {

        $city = City::find($id);
        if(is_null($city)){
            return abort(404);
        }

        $data = $request->all();
        $validatedData = $request->validate([
            'name_ar' => ['required:mysql2.cities,city_ar_name',Rule::unique('mysql2.cities','city_ar_name')->ignore($city->id)],
            'name_en' => ['required:mysql2.cities,city_en_name',Rule::unique('mysql2.cities','city_en_name')->ignore($city->id)],
        ],[
            'name_ar.required' => "Arabic name is required",
            'name_ar.unique' => "A city with the same AR name already exists",
            'name_en.required' => "English name is required",
            'name_en.unique' => "A city with the same EN name already exists",
        ]);
        $city->city_ar_name = $data['name_ar'];
        $city->city_en_name = $data['name_en'];

        if(isset($data['active'])){
            $city->active = 1;
        }
        else{
            $city->active = 0;
        }

        if($city->save()){
            toast('City updated successfully','success');
            return redirect()->route('cities_edit',$city->id);
        }

    }

    public function delete($id) {
        $city = City::find($id);
        if(is_null($city)){
            return abort(404);
        }
        if($city->delete()){
            toast('City deleted successfully','success');
        }
        return redirect()->route('cities');
    }

    public function changeState($id) {
        $city = City::find($id);
        if(is_null($city)){
            return abort(404);
        }
        if($city->active == 1){
            $city->active = 0;
            if($city->save()){
                toast('City deactivated successfully','success');
                return redirect()->route('cities');
            }
        }
        else if($city->active == 0){
            $city->active = 1;
            if($city->save()){
                toast('City activated successfully','success');
                return redirect()->route('cities');
            }
        }
    }

    public function deactivateSelected(Request $request) {
        $ids = $request->ids;
        $targetCities = City::whereIn('id',$ids)->where('active',1);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetCities->update(['active'=>0])){
            toast('Cities deactivated successfully','success');
        }
        return redirect()->route('cities');
    }

    public function deleteSelected(Request $request) {
        $ids = $request->ids;
        $targetCities = City::whereIn('id',$ids);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetCities->delete()){
            toast('Cities deleted successfully','success');
        }
        return redirect()->route('cities');
    }

    public function search(Request $request){
        $cities = City::orderBy('id','DESC');

        if(isset($request->id)){
            $cities = $cities->where('id', $request->id);
        }

        if(isset($request->name_ar)){
            $cities = $cities->where('city_ar_name' , 'LIKE', '%' . $request->name_ar . '%');
        }

        if(isset($request->name_en)){
            $cities = $cities->where('city_en_name','LIKE','%' .$request->name_en. '%');
        }

        if(isset($request->active)){
            if($request->active == 'active'){
                $cities = $cities->where('active',1);
            }
            elseif($request->active == 'inactive'){
                $cities = $cities->where('active',0);
            }
        }
        $cities = $cities->paginate(50);
        $inactiveCities = City::where('active',0)->get();
        $activeCities = City::where('active',1)->get();
        return view('app.cities.index',compact('cities','activeCities','inactiveCities'));
    }

}
