<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\UserWallet;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class FieldsController extends Controller
{

    public function index() {
        $fields = Field::orderBy('id','DESC')->paginate(50);
        $activeFields = Field::where('active',1)->get();
        $inactiveFields = Field::where('active',0)->get();
        return view('app.fields.index',compact('fields','activeFields','inactiveFields'));
    }

    public function add() {
        return view('app.fields.add');
    }

    public function store(Request $request) {
        $data = $request->all();
        $validatedData = $request->validate([
            'name_ar' => ['required:fields,filed_ar_name','unique:mysql2.fields,filed_ar_name'],
            'name_en' => ['required:fields,filed_en_name','unique:mysql2.fields,filed_en_name'],
        ],[
            'name_ar.required' => "Arabic name is required",
            'name_ar.unique' => "A Field with the same AR name already exists",
            'name_en.required' => "English name is required",
            'name_en.unique' => "A Field with the same EN name already exists",
        ]);


        $field = new Field();
        $field->filed_ar_name = $data['name_ar'];
        $field->filed_en_name = $data['name_en'];


        if(isset($data['active'])){
            $field->active = 1;
        }
        else{
            $field->active = 0;
        }

        if($field->save()){
            toast('Field added successfully','success');
            return redirect()->route('fields');
        }

    }

    public function edit($id) {
        $field = Field::find($id);
        if(is_null($field)){
            return abort(404);
        }
        return view('app.fields.edit',compact('field'));
    }

    public function update(Request $request , $id) {

        $field = Field::find($id);
        if(is_null($field)){
            return abort(404);
        }

        $data = $request->all();
        $validatedData = $request->validate([
            'name_ar' => ['required:mysql2.fields,filed_ar_name',Rule::unique('mysql2.fields','filed_ar_name')->ignore($field->id)],
            'name_en' => ['required:mysql2.fields,filed_en_name',Rule::unique('mysql2.fields','filed_en_name')->ignore($field->id)],
        ],[
            'name_ar.required' => "Arabic name is required",
            'name_ar.unique' => "A Field with the same AR name already exists",
            'name_en.required' => "English name is required",
            'name_en.unique' => "A Field with the same EN name already exists",
        ]);
        $field->filed_ar_name = $data['name_ar'];
        $field->filed_en_name = $data['name_en'];

        if(isset($data['active'])){
            $field->active = 1;
        }
        else{
            $field->active = 0;
        }

        if($field->save()){
            toast('Field updated successfully','success');
            return redirect()->route('fields_edit',$field->id);
        }

    }

    public function delete($id) {
        $field = Field::find($id);
        if(is_null($field)){
            return abort(404);
        }
        if($field->delete()){
            toast('Field deleted successfully','success');
        }
        return redirect()->route('fields');
    }

    public function changeState($id) {
        $field = Field::find($id);
        if(is_null($field)){
            return abort(404);
        }
        if($field->active == 1){
            $field->active = 0;
            if($field->save()){
                toast('Field deactivated successfully','success');
                return redirect()->route('fields');
            }
        }
        else if($field->active == 0){
            $field->active = 1;
            if($field->save()){
                toast('Field activated successfully','success');
                return redirect()->route('fields');
            }
        }
    }

    public function deactivateSelected(Request $request) {
        $ids = $request->ids;
        $targetFields = Field::whereIn('id',$ids)->where('active',1);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetFields->update(['active'=>0])){
            toast('Fields deactivated successfully','success');
        }
        return redirect()->route('fields');
    }

    public function deleteSelected(Request $request) {
        $ids = $request->ids;
        $targetFields = Field::whereIn('id',$ids);
        if(is_null($ids)){
            return abort(404);
        }
        if($targetFields->delete()){
            toast('Fields deleted successfully','success');
        }
        return redirect()->route('fields');
    }

    public function search(Request $request){
        $fields = Field::orderBy('id','DESC');

        if(isset($request->id)){
            $fields = $fields->where('id', $request->id);
        }

        if(isset($request->name_ar)){
            $fields = $fields->where('filed_ar_name' , 'LIKE', '%' . $request->name_ar . '%');
        }

        if(isset($request->name_en)){
            $fields = $fields->where('filed_en_name','LIKE','%' .$request->name_en. '%');
        }

        if(isset($request->active)){
            if($request->active == 'active'){
                $fields = $fields->where('active',1);
            }
            elseif($request->active == 'inactive'){
                $fields = $fields->where('active',0);
            }
        }
        $fields = $fields->paginate(50);
        $inactiveFields = Field::where('active',0)->get();
        $activeFields = Field::where('active',1)->get();
        return view('app.fields.index',compact('fields','activeFields','inactiveFields'));
    }





    //get static pages
    public function edit_page($type)
    {
        $content = Page::whereName($type)->first();
        if($content)
            return view('app.pages.edit', compact('content'));
        else
            return redirect()->back();
    }


    public function update_page(Request $request)
    {
        $page = Page::find($request->id);
        if(is_null($page)){
            return abort(404);
        }

        $data = $request->all();
        $validatedData = $request->validate([
            'ar_content' => ['required'],
            'en_content' => ['required'],
        ],[
            'ar_content.required' => "Arabic Content is required",
            'en_content.required' => "English Content is required",
        ]);
        $page->ar_content = $data['ar_content'];
        $page->en_content = $data['en_content'];

        if($page->save()){
            toast('Page updated successfully','success');
            return redirect()->back();
        }




    }








}
