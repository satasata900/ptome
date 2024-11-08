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
        if (empty($details['search'])) {
            $search = '';
        } else {
            $search = $details['search'];
        }
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            // get wallets
           $stmt = $con->prepare( "SELECT a.*, b.wallet_name, b.wallet_currency FROM users_wallets a INNER JOIN wallets b ON a.wallet_id = b.id WHERE a.user_id=? AND a.test_mode=?");
           $stmt->execute(array($user_id, getUserTestStatus($con, $user_id)));
           $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           for ($i = 0; $i < count($wallets); $i++) {
               unset($wallets[$i]['user_id']);
               if ($wallets[$i]['hidden'] == '0') {
                $wallets[$i]['hidden'] = false;
               } else {
                $wallets[$i]['hidden'] = true;
               }
           }
           $response['success'] = true;
           $response['data'] = $wallets;
        } else {
            $response['message'] = translate('not_authentication');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 