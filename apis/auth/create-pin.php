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
        if (empty($details['id'])) {
            $response['message'] = translate('no_user');
            $response['success'] = false;
        } else {
            $user_id = $details['id'];
            if (empty($details['pin'])) {
                $response['message'] = translate('pin_required');
                $response['success'] = false;
            } else {
                $pin = $details['pin'];
                if (strlen($pin) != 6) {
                    $response['message'] = translate('pin_is_6');
                    $response['success'] = false;
                } else {
                    $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
                    $stmt->execute(array($user_id));
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($user) {
                        if ($user['verified_email'] == '0') {
                            $response['message'] = translate('email_not_verified');
                            $response['success'] = false;
                        } else {
                            $stmt=$con->prepare("UPDATE users SET pin_code=? WHERE id=? ");
                            $stmt->execute(array(sha1($pin),$user['id']));
                            $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
                            $stmt->execute(array($user['id']));
                            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($user) {
                                if ($user['verified_email'] == '0') {
                                    $response['message'] = translate('email_not_verified');
                                    $response['code'] = 1;
                                    $response['success'] = false; 
                                } else {
                                    unset($user['password']);
                                    if ($user['verified_email'] == '1') {
                                        $user['verified_email'] = true;
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
                                    } else {
                                        $user['verified_email'] = false;
                                    }
                                    if ($user['verified_phone'] == '1') {
                                        $user['verified_phone'] = true;
                                    } else {
                                        $user['verified_phone'] = false;
                                    }
                                    if (empty($user['pin_code'])) {
                                        $user['pin_code'] = false;
                                    } else {
                                        $user['pin_code'] = true;
                                    }
                                    $user['token'] = createToken($user['id']);
                                    $user['picture'] = getFullimageURL($images_url ,$user['picture']);
                                    $user['registration_Time'] = convertDate($user['registration_Time']);
                                    $response['success'] = true; 
                                    $response['message'] = translate('successfull_login');
                                    $response['user'] = $user; 
                                }
                            }
                            $response['success'] = true;
                            $response['message'] = translate('pin_created');
                            $response['user'] = $user;         
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

