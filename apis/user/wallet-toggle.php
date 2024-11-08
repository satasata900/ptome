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
            if (empty($details['wallet_id'])) {
                $response['message'] = translate('wallet_required');
                $response['success'] = false;
            } else {
                $wallet_id = $details['wallet_id'];
                $stmt = $con->prepare( "SELECT * FROM users_wallets WHERE id=? LIMIT 1");
                $stmt->execute(array($wallet_id));
                $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($wallet) {
                    if ($wallet['user_id'] == $user_id) {
                        if ($wallet['hidden'] == '0') {
                            $stmt = $con->prepare( "SELECT COUNT(*) FROM users_wallets WHERE user_id=? AND hidden=?");
                            $stmt->execute(array($user_id, '0'));
                            $not_hidden = $stmt->fetchColumn();
                            $response['not_hidden'] = $not_hidden;
                            if ($not_hidden == 1) {
                                $response['message'] = translate('one_wallet_at_least');
                                $response['success'] = false;
                            } else {
                                $stmt=$con->prepare("UPDATE users_wallets SET hidden=? WHERE id=? ");
                                $stmt->execute(array('1',$wallet_id));
                                $response['success'] = true;
                                $response['message'] = translate('wallet_toggled');
                            }
                        } else {
                            $stmt=$con->prepare("UPDATE users_wallets SET hidden=? WHERE id=? ");
                            $stmt->execute(array('0',$wallet_id));
                            $response['success'] = true;
                            $response['message'] = translate('wallet_toggled');
                        }
                    } else {
                        $response['message'] = translate('wallet_required');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('wallet_required');
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

