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
            if (empty($details['notifications_on_off']) && $details['notifications_on_off'] != false) {
                $response['message'] = translate('try_again');
                $response['success'] = false;
            } else {
                $notifications_on_off = $details['notifications_on_off'];
                $notifications_status;
                if ($notifications_on_off) {
                    $notifications_status = '1';
                } else {
                    $notifications_status = '0';
                }
                $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
                $stmt->execute(array($user_id));
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    if (!empty($details['platform'])  && !empty($details['platform_token'])) {
                        $stmt=$con->prepare("DELETE FROM tokens WHERE platform_token=? ");
                        $stmt->execute(array($details['platform_token']));
                        $stmt = $con->prepare( "SELECT * FROM tokens WHERE user_id=? AND platform_token=? LIMIT 1");
                        $stmt->execute(array($user['id'],$details['platform_token']));
                        $token = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if (!$token) {
                            $token_data = array(
                                'user_id'=>$user['id'],
                                'platform'=>$details['platform'],
                                'platform_token'=>$details['platform_token']
                            );
                            $insert_request = $con->prepare(insert('tokens', $token_data))->execute();
                        }
                    }
                    $stmt=$con->prepare("UPDATE users SET notifications_on_off=? WHERE id=? ");
                    $stmt->execute(array($notifications_status,$user['id']));    
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