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
        if (empty($details['full_name'])) {
            $response['message'] = translate('full_name_required');
            $response['success'] = false;
        } else {
            $full_name = $details['full_name'];
            if (strlen($full_name) < 6) {
                $response['message'] = translate('full_name_length');
                $response['success'] = false;
            } else {
                if (empty($details['email'])) {
                    $response['message'] = translate('email_required');
                    $response['success'] = false;
                } else {
                    $email = $details['email'];
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $response['message'] = translate('valid_email');
                        $response['success'] = false; 
                    } else {
                        $user_name = $details['username'];
                        if (empty($user_name)) {
                            $response['message'] = translate('username_required');
                            $response['success'] = false;
                        } else {
                            if (strlen($user_name) < 6) {
                                $response['message'] = translate('username_invalid');
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
                                            $stmt = $con->prepare( "SELECT * FROM users WHERE email=? LIMIT 1");
                                            $stmt->execute(array($email));
                                            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                                            if ($user) {
                                                $response['message'] = translate('email_registered_before');
                                                $response['success'] = false;
                                            } else {
                                                $register_data = array(
                                                    'full_name'=>$full_name,
                                                    'email'=>$email,
                                                    'user_name'=> $user_name,
                                                    'password'=>sha1($password),
                                                    'type'=>'email',
                                                    'registration_Time'=>time()
                                                );
                                                $insert_request = $con->prepare(insert('users', $register_data))->execute();
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
                                                            'test_mode' => $wallets[$i]['test_wallet']
                                                        );
                                                        $insert_request = $con->prepare(insert('users_wallets', $wallet_data))->execute();
                                                    }
                                                    $response['message'] = translate('successfully_register');
                                                    $response['success'] = true;
                                                } else {
                                                    $response['message'] = translate('try_again');
                                                    $response['success'] = false;
                                                }
                                            }
                                        
                                    }
                                }
                            }
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
    