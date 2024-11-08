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
            if (empty($details['password_verification_code'])) {
                $response['message'] = translate('verification_code_required');
                $response['success'] = false;
            } else {
                $password_verification_code = $details['password_verification_code'];
                $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
                $stmt->execute(array($user_id));
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    if ($user['password_verification_code'] == sha1($password_verification_code)) {
                        $stmt=$con->prepare("UPDATE users SET password_verification_code=? WHERE id=? ");
                        $stmt->execute(array(NULL,$user['id']));
                        $response['success'] = true;
                        $response['message'] = translate('email_verified');       
                    } else {
                        $response['message'] = translate('wrong_verification_code');
                        $response['success'] = false;
                    }
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

