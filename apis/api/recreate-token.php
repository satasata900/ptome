<?php
    include('../connect_database.php');
    $response = array();
    if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $user_id;
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $user_id = getToken($_SERVER['HTTP_TOKEN']);
        } 
        if (empty($user_id)) {
            $response['message'] = translate('no_user');
            $response['success'] = false;
        } else {
            //get user name
            $stmt = $con->prepare( "SELECT * FROM organizations WHERE user_id=?");
            $stmt->execute(array($user_id));
            $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($organization) {
                // update
                $secret_key = $user_id . createSecretKey();
                $stmt=$con->prepare("UPDATE organizations SET secret_key=? WHERE id=? ");
                $stmt->execute(array($secret_key,$organization['id']));
                $organization['secret_key'] = $secret_key;
                $organization['creationTime'] = date('d-m-Y H:i', $organization['creationTime']);
                if ($lang == 'ar') {
                    $query = "SELECT id, country_ar As country FROM countries WHERE id=?";
                } else if ($lang == 'en') {
                    $query = "SELECT id, country_en As country FROM countries WHERE id=?";
                }
                $stmt = $con->prepare($query);
                $stmt->execute(array($organization['country']));
                $country = $stmt->fetch(\PDO::FETCH_ASSOC);
                $organization['country'] = $country;
                $response['organization'] = $organization;
                $response['success'] = true; 
                $response['message'] = translate('renew_secretkey');
            } else {
                $response['message'] = translate('no_organization');
                $response['success'] = false;  
            }
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 