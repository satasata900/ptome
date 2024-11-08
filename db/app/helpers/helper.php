<?php

$app_server = 'https://ramybadr.com';

function assetURL($name=''){
    return "dashboard/img/" . $name;
}
 
function imgURL($name=''){
    return asset('/') . $name;
}

function providerURL($path){
    $hostname = $_SERVER['SERVER_NAME'];
    if($hostname == '127.0.0.1'){
        return asset('images/' . $path);
    }
    else{
        return 'https://ramybadr.com/pay-to-me/images/' . $path;
    }
}


function uploadImage($image): string
{
    if(!is_null($image)){
        $uniqueName = hexdec(uniqid());
        $extension = strtolower($image->getClientOriginalExtension());
        $imageName = $uniqueName . '.' . $extension;
        $uploadLocation = 'images/users/';
        $finalImage = $uploadLocation.$imageName;
        $image->move($uploadLocation,$finalImage);
        return $finalImage;
    }
}

function uploadAppImage($image='',$dir=''): string
{
    $hostname = $_SERVER['SERVER_NAME'];

    if(!is_null($image)){
        $uniqueName = hexdec(uniqid());
        $extension = strtolower($image->getClientOriginalExtension());
        $imageName = $uniqueName . '.' . $extension;
        if($hostname == '127.0.0.1'){
            $uploadLocation = 'images/' . $dir;
        }
        else{
            $uploadLocation = 'pay-to-me/images/' . $dir;
        }
        $finalImage = $uploadLocation.$imageName;
        $image->move($uploadLocation,$finalImage);
        return $imageName;
    }
}

function unlinkAppImage($image)
{
    $hostname = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
    if(!is_null($image)){
        if($_SERVER['SERVER_NAME'] == '127.0.0.1'){
            if(file_exists('images/' . $image))
                unlink( 'images/' . $image);
        }
        else{
            if(file_exists('pay-to-me/images/' . $image))
                unlink('pay-to-me/images/' . $image);
        }
    }
}

