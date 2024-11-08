<?php
    include('../connect_database.php');
    $response = array();
    if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'GET') {
        // to get data in post
        if(!empty($_POST)) {
            $details = $_POST;
        } else {
            $post = json_decode(file_get_contents('php://input'), true);
            if(json_last_error() == JSON_ERROR_NONE){
                $details = $post;
            } else {
                $details = array();
            }
        }   
        // to get data in get
        if(!empty($_GET)) {
            $details += $_GET;
        }
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            $response['data']['wallets'] = getWallets($con,$user_id);
            // get fields
            if ($lang == 'ar') {
                $query = 'SELECT id, filed_ar_name AS field_name FROM fields WHERE active="1" ORDER BY filed_ar_name';
            } else if ($lang == 'en') {
                $query = 'SELECT id, filed_en_name AS field_name FROM fields WHERE active="1" ORDER BY filed_en_name';
            }
            $stmt = $con->prepare($query);
            $stmt->execute();
            $fields = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $response['data']['fields'] = $fields;
            // get cities
            if ($lang == 'ar') {
                $query = 'SELECT id, city_ar_name AS city_name FROM cities WHERE active="1" ORDER BY city_ar_name';
            } else if ($lang == 'en') {
                $query = 'SELECT id, city_en_name AS city_name FROM cities WHERE active="1" ORDER BY city_en_name';
            }
            $stmt = $con->prepare($query);
            $stmt->execute();
            $cities = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $response['data']['cities'] = $cities;
            $response['success'] = true;
        } else {
            $response['message'] = translate('not_authentication');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 