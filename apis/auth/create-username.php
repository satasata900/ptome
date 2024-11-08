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
            if (empty($details['username'])) {
                $response['message'] = translate('username_required');
                $response['success'] = false;
            } else {
                $username = $details['username'];
                $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
                $stmt->execute(array($user_id));
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    if ($user['verified_email'] == '0') {
                        $response['message'] = translate('email_not_verified');
                        $response['success'] = false;
                    } else {
                        if (!empty($user['user_name'])) {
                            $response['message'] = translate('usernmae_already_created');
                            $response['success'] = false;
                        } else {
                            $stmt = $con->prepare( "SELECT * FROM users WHERE user_name=? LIMIT 1");
                            $stmt->execute(array($username));
                            $user_name_found = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($user_name_found) {
                                $response['message'] = translate('username_used');
                                $response['success'] = false;
                            } else {
                                $stmt=$con->prepare("UPDATE users SET user_name=? WHERE id=? ");
                                $stmt->execute(array($username,$user['id']));
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
                                $response['message'] = translate('username_created');
                                $response['user'] = $user;
                            } 
                        }
                    }
                } else {
                    $response['message'] = translate('no_user');
                    $response['success'] = false;
                }
            }
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 

