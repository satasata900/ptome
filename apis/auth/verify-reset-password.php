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
        if (empty($details['email'])) {
            $response['message'] = translate('email_required');
            $response['success'] = false;
        } else {
            $email = $details['email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = translate('valid_email');
                $response['success'] = false; 
            } else {
                if (empty($details['code'])) {
                    $response['message'] = translate('pass_reset_code_required');
                    $response['success'] = false;
                } else {
                    $code = $details['code'];
                    $stmt = $con->prepare( "SELECT * FROM users WHERE email=? LIMIT 1");
                    $stmt->execute(array($email));
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($user) {
                        if ($user['pass_reset_code'] == sha1($code)) {
                            $response['success'] = true; 
                            $response['message'] = translate('verify_reset_password_success');
                        } else {
                            $response['message'] = translate('wrong_pass_reset_code');
                            $response['success'] = false;
                        }
                    } else {
                        $response['message'] = translate('no_user');
                        $response['success'] = false;
                    }
                }
            }   
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response);
    