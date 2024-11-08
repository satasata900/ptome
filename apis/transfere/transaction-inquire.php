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
        if (!empty($details['admin'])) {
            $admin = true;
        } else {
            $admin = false;
        }
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            if (getUserActiveStatus($con, $user_id)) {
                $response['message'] = translate('blocked_user');
                $response['success'] = false;  
            } else {
                if (!empty($details['transaction_number'])) {
                    $transaction_number = $details['transaction_number'];
                    $stmt = $con->prepare( "SELECT * FROM transactions WHERE transaction_number=?");
                    $stmt->execute(array($transaction_number));
                    $transaction = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($transaction) {
                        if ($admin) {
                            $response['transaction_id'] = $transaction['id'];
                            $response['success'] = true;
                        } else {
                            if ($transaction['sender_id'] == $user_id || $transaction['recipient_id'] == $user_id) {
                                $response['transaction_id'] = $transaction['id'];
                                $response['success'] = true;
                            } else {
                                $response['message'] = translate('transaction_wrong');
                                $response['success'] = false;
                            }
                        }
                    } else {
                        $response['message'] = translate('transaction_wrong');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('transaction_required');
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