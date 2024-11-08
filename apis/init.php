<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
    if (empty($html_content)) {
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Content-Type: application/json, text/plain, charset=UTF-8");
    }
    header("Access-Control-Request-Headers: Content-Type");
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, SECRET-KEY, Origin, API-TOKEN, Authorization, Cache-Control, Pragma, lang, TIME-ZONE, token, Accept, Accept-Encoding");


    $database_name = 'u298579295_ptmapp'; // u298579295_ptmdb
    $database_user = 'u298579295_ptmappuser'; // u298579295_ptmdbuser
    $database_pass = '5$sD4ZAN+u3L'; // 5$sD4ZAN+u3L

    $api_token = 'LRD2LF2uJLc8thaxZCA6nxLTByyuUBrcjmX6cGcBgvXdMFs7QEdXZu65x2MAP3UJDhHZvWmFjv3A2Bt9CZd6CxAEaqbtjzENkUxnFUQTb7HKCLveaR7esUPxzzWZbWs4';
    $server_url = 'https://paytome.net/apis/';
    $images_url = $server_url . 'images/';
    $items_per_page = 15;
    $default_page = 0;
    $imgs_dir = 'images/';
    $server_email = 'support@paytome.com';
    $default_language = 'en';
    $app_name = 'Pay To Me';

    define('SECRET_KEY',$api_token);  // secret key can be a random string and keep in secret from anyone
    define('ALGORITHM','HS256');   // Algorithm used to sign the token
    require_once('vendor/autoload.php');
    include('sql.php');
    include('user_func.php');
    

    
