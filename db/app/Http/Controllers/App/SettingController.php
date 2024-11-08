<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{

    public function edit() {
        $settings = Setting::whereIn('id', [7,8,9,10])->get();
        $firebase_key = Setting::whereName('firebase_key')->first();
        $version = Setting::whereName('version')->first();
        $app_name = Setting::whereName('app_name')->first();
        $app_email = Setting::whereName('app_email')->first();
        return view('app.settings.edit',compact('settings', 'firebase_key','version','app_name','app_email'));
    }

    public function update(Request $request) {
        $data = $request->all();
        
        if(isset($request['active']))
        {

            $array=[];
            foreach($request['active'] as $key => $val)
            {
                array_push($array,$key);

                if(!in_array(7, $array))
                {
                    Setting::whereId(7)->update(['body' => 0]);
                }
                if(!in_array(8, $array))
                {
                    Setting::whereId(8)->update(['body' => 0]);
                }
                if(!in_array(9, $array))
                {
                    Setting::whereId(9)->update(['body' => 0]);
                }
                if(!in_array(10, $array))
                {
                    Setting::whereId(10)->update(['body' => 0]);
                }
                Setting::whereId($key)->update(['body' => 1]);
            }
        }
        else
        {
            Setting::whereIn('id', [7,8,9,10])->update(['body' => 0]);
        }

        Setting::whereName('firebase_key')->update(['body' => $data['firebase_key']]);
        Setting::whereName('version')->update(['body' => $data['version']]);
        Setting::whereName('app_name')->update(['body' => $data['app_name']]);
        Setting::whereName('app_email')->update(['body' => $data['app_email']]);

        toast('Settings updated successfully','success');
        return redirect()->route('settings_edit');
    }

   

}
