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
                $stmt = $con->prepare( "SELECT * FROM users WHERE email=? LIMIT 1");
                $stmt->execute(array($email));
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    // login
                    if ($user['verified_email'] == '0') {
                        $stmt=$con->prepare("UPDATE users SET verified_email=? WHERE id=? ");
                        $stmt->execute(array('1',$user['id']));
                        $user['verified_email'] = '1';
                    }
                    if (!empty($details['platform'])  && !empty($details['platform_token'])) {
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
                    if (empty($user['password'])) {
                        $user['password'] = false;
                    } else {
                        $user['password'] = true;
                    }
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
                } else {
                    // register
                    if (empty($details['full_name'])) {
                        $response['message'] = translate('full_name_required');
                        $response['success'] = false;
                    } else {
                        $full_name = $details['full_name'];
                        $ragister_data = array(
                            'full_name'=>$full_name,
                            'email'=>$email,
                            'registration_Time'=>time(),
                            'type'=>'social',
                            'verified_email'=>'1'
                        );
                        $insert_request = $con->prepare(insert('users', $ragister_data))->execute();
                        if ($insert_request) {
                            $conn = ("SELECT LAST_INSERT_ID()");
                            $stmt = $con->prepare($conn);
                            $stmt->execute();
                            $user_id = $stmt->fetchColumn();                  
                            $stmt = $con->prepare( "SELECT * FROM wallets");
                            $stmt->execute();
                            $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            for ($i = 0; $i < count($wallets); $i++) {
                                $wallet_data = array(
                                    'user_id'=>$user_id,
                                    'wallet_id'=>$wallets[$i]['id'],
                                    'price'=>$wallets[$i]['price'],
                                    'test_mode'=>$wallets[$i]['test_wallet']
                                );
                                $insert_request = $con->prepare(insert('users_wallets', $wallet_data))->execute();
                            }                   
                            $stmt = $con->prepare( "SELECT * FROM users WHERE email=? LIMIT 1");
                            $stmt->execute(array($email));
                            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($user) {
                                // login
                                if (!empty($details['platform'])  || !empty($details['platform_token'])) {
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
                                $user['password'] = false;
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
                        } else {
                            $response['message'] = translate('try_again');
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
    