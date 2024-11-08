<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;




    //Send Notify to User
    public function sendWebNotification($title,$body,$tokens=[])
    {
        $data = '{"notification":{"title":"' . $title . '", "body": "' . $body . '"' . ', "click_action": "FCM_PLUGIN_ACTIVITY"' . '}';
        $data .= ',"data":{"title":"' . $title . '", "body": "' . $body . '",}';
            $data .= ',"registration_ids":' . $tokens;
            $data .= '}';
            $curl = curl_init();

            $key = \App\Models\Setting::whereId(6)->first();
            if(isset($key))
            {
                $firebase_key = $key->body;
            }
            else
            {
                $firebase_key = '';
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: ' . $firebase_key
                ),
            ));
            $response = curl_exec($curl);
            
            curl_close($curl);
    }


}
