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
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            if (empty($details['test_mode']) && $details['test_mode'] != false) {
                $response['message'] = translate('try_again');
                $response['success'] = false;
            } else {
                $test_mode = $details['test_mode'];
                $test_mode_status;
                if ($test_mode) {
                    $test_mode_status = '1';
                } else {
                    $test_mode_status = '0';
                }
                $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
                $stmt->execute(array($user_id));
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    $stmt=$con->prepare("UPDATE users SET test_mode=? WHERE id=? ");
                    $stmt->execute(array($test_mode_status,$user['id']));    
                    $response['success'] = true;            
                } else {
                    $response['message'] = translate('no_user');
                    $response['success'] = false;
                }
            }
        } else {
            $response['message'] = translate('not_authentication');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 