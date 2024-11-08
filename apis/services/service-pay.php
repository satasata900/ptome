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
            if (getUserActiveStatus($con, $user_id)) {
                $response['message'] = translate('blocked_user');
                $response['success'] = false;  
            } else {
                if (empty($details['service_id'])) {
                    $response['message'] = translate('service_required');
                    $response['success'] = false;
                } else {
                    $service_id = $details['service_id'];
                    $stmt = $con->prepare('SELECT * FROM services WHERE id=?');
                    $stmt->execute(array($service_id));
                    $service = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($service) {
                        $wallet_id = $service['wallet_id'];
                        $stmt = $con->prepare('SELECT user_id FROM providers WHERE id=?');
                        $stmt->execute(array($service['provider_id']));
                        $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($provider) {
                            $provider_user_id = $provider['user_id'];
                            if (getUserActiveStatus($con, $provider['user_id'])) {
                                $response['message'] = translate('user_blocked');
                                $response['success'] = false;  
                            } else {
                                if ($provider_user_id == $user_id) {
                                    $response['message'] = translate('no_transfere_to_yourself');
                                    $response['success'] = false;
                                } else {
                                    // user details
                                    $stmt = $con->prepare('SELECT id,user_id,wallet_id,price FROM users_wallets WHERE user_id=? AND wallet_id=?');
                                    $stmt->execute(array($user_id,$wallet_id));
                                    $sender_wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                                    if (!$sender_wallet) {
                                        // create wallet for this user 
                                        $sender_wallet = createWallet($con, $user_id,$wallet_id);
                                    }
                                    // provider details
                                    $stmt = $con->prepare('SELECT id,user_id,wallet_id,price FROM users_wallets WHERE user_id=? AND wallet_id=?');
                                    $stmt->execute(array($provider_user_id,$wallet_id));
                                    $recipient_wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                                    // make the transfere
                                    if (!empty($sender_wallet) && !empty($recipient_wallet)) {
                                        $money = $service['amount'];
                                        if ($sender_wallet['price'] >= $money) {
                                            try {
                                                $con->beginTransaction();
                                                // update sender price
                                                $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                                $stmt->execute(array($sender_wallet['price'] - $money,$sender_wallet['id']));
                                                // update recipient price
                                                $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                                $stmt->execute(array($recipient_wallet['price'] + $money,$recipient_wallet['id']));
                                                // add transaction
                                                $transaction = makeTransaction($con, $sender_wallet['user_id'], $recipient_wallet['user_id'], $money, $wallet_id, 'service', NULL, $service_id, NULL, json_encode($service), $service['test_mode']);
                                                // update last bill date in service_members
                                                $stmt=$con->prepare("UPDATE service_members SET last_bill_date=? WHERE member_id=? AND service_id=?");
                                                $stmt->execute(array($transaction['creationTime'],$user_id,$service_id));
                                                // send notification
                                                makeNotification($con, $provider_user_id, $user_id, 'service_pay', $service_id, '1', $service['test_mode']);
                                                $con->commit();
                                                $response['message'] = translate('successfull_transfere');
                                                $response['transaction_id'] = $transaction['transaction_id'];
                                                $response['recipient_id'] = $provider_user_id;
                                                $response['transaction_number'] = $transaction['transaction_number'];
                                                $response['success'] = true;
                                            } catch (Exception $e) {
                                                $con->rollback();
                                                $response['message'] = translate('try_again');
                                                $response['success'] = false;
                                            }
                                        } else {
                                            $response['message'] = translate('not_have_enough_money');
                                            $response['success'] = false;
                                        }
                                    } else {
                                        $response['message'] = translate('try_again');
                                        $response['success'] = false;  
                                    }
                                }
                            }
                        }  else {
                            $response['message'] = translate('try_again');
                            $response['success'] = false;  
                        } 
                    } else {
                        $response['message'] = translate('service_required');
                        $response['success'] = false;  
                    }     
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