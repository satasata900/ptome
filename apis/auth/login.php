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
                if (empty($details['password'])) {
                    $response['message'] = translate('password_required');
                    $response['success'] = false;
                } else {
                    $password = $details['password'];
                    $uppercase = preg_match('@[A-Z]@', $password);
                    $lowercase = preg_match('@[a-z]@', $password);
                    $number    = preg_match('@[0-9]@', $password);
                    $specialChars = preg_match('@[^\w]@', $password);
                    if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
                        $response['message'] = translate('password_valid');
                        $response['success'] = false;
                    } else {
                        $stmt = $con->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
                        $stmt->execute(array($email));
                        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($user) {
                            if ($user['password'] != sha1($password)) {
                                $response['message'] = translate('password_is_incorrect');
                                $response['success'] = false; 
                            } else {
                                if ($user['verified_email'] == '0' || empty($user['verified_email'])) {
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
                        } else {
                            $response['message'] = translate('email_not_registered');
                            $response['success'] = false; 
                        }
                    }
                }
            }          
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response);
    