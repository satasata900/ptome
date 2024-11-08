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
                if (empty($details['trade_service_id'])) {
                    $response['message'] = translate('trade_required');
                    $response['success'] = false;
                } else {
                    $trade_service_id = $details['trade_service_id'];
                    if (empty($details['exchanged_amount'])) {
                        $response['message'] = translate('exchanged_amount_required');
                        $response['success'] = false;
                    } else {
                        $exchanged_amount = $details['exchanged_amount'];
                        // get trade service
                        $sth= $con->prepare("SELECT * FROM traders_services WHERE id=?");
                        $sth->execute(array($trade_service_id));
                        $trade_service = $sth->fetch(\PDO::FETCH_ASSOC);  
                        if ($trade_service) {
                            if ($trade_service['active'] == '0') {
                                $response['message'] = translate('trade_service_inactive');
                                $response['success'] = false;
                            } else {
                                // get trader details
                                $sth= $con->prepare("SELECT * FROM traders WHERE id=?");
                                $sth->execute(array($trade_service['trader_id']));
                                $trader = $sth->fetch(\PDO::FETCH_ASSOC);
                                if ($trader) {
                                    if (getUserActiveStatus($con, $trader['user_id'])) {
                                        $response['message'] = translate('user_blocked');
                                        $response['success'] = false;  
                                    } else {
                                        if ($trader['user_id'] == $user_id) {
                                            $response['success'] = false; 
                                            $response['message'] = translate('no_convert_for_yourself');
                                        } else {
                                            $trader_user_id = $trader['user_id'];
                                            // get user from wallet
                                            $from_wallet_id = $trade_service['from_wallet'];
                                            $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                            $sth->execute(array($user_id,$from_wallet_id));
                                            $user_from_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                            // get user to wallet
                                            $to_wallet_id = $trade_service['to_wallet'];
                                            $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                            $sth->execute(array($user_id,$to_wallet_id));
                                            $user_to_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                            // get trader from wallet
                                            $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                            $sth->execute(array($trader_user_id,$to_wallet_id));
                                            $trade_from_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                            // get trader to wallet
                                            $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                            $sth->execute(array($trader_user_id,$from_wallet_id));
                                            $trade_to_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                            $exchange_rate = $trade_service['exchange_rate'];
                                            if ($user_from_wallet && $user_to_wallet && $trade_from_wallet && $trade_to_wallet) {
                                                if ($user_from_wallet['price'] >= $exchanged_amount) {
                                                    if ($trade_from_wallet['price'] >= $exchanged_amount * $exchange_rate) {
                                                        try {
                                                            $con->beginTransaction();
                                                            // update user from wallet
                                                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                                            $stmt->execute(array($user_from_wallet['price'] - $exchanged_amount,$user_from_wallet['id']));
                                                            // update user to wallet
                                                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                                            $stmt->execute(array($user_to_wallet['price'] + ($exchanged_amount * $exchange_rate),$user_to_wallet['id']));
                                                            // update trade from wallet
                                                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                                            $stmt->execute(array($trade_from_wallet['price'] - ($exchanged_amount * $exchange_rate),$trade_from_wallet['id']));
                                                            // update trade to wallet
                                                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                                            $stmt->execute(array($trade_to_wallet['price'] + $exchanged_amount,$trade_to_wallet['id']));
                                                            // add transactions
                                                            $transaction = makeTransaction($con, $user_id, $trader_user_id, $exchanged_amount, $user_from_wallet['wallet_id'], 'trade', NULL, NULL, $trade_service_id, json_encode($trade_service), $trade_service['test_mode']);
                                                            $new_amount = $exchanged_amount * $exchange_rate;
                                                            $response['transaction_number'] = $transaction['transaction_number'];
                                                            $response['transaction_id'] = $transaction['transaction_id'];
                                                            makeNotification($con, $trader_user_id, $user_id, 'money_transfer', $transaction['transaction_id'], '2', $trade_service['test_mode']);
                                                            $transaction = makeTransaction($con, $trader_user_id, $user_id, $new_amount, $trade_from_wallet['wallet_id'], 'trade', NULL, NULL, $trade_service_id, json_encode($trade_service), $trade_service['test_mode']);
                                                            makeNotification($con, $user_id, $trader_user_id, 'money_transfer', $transaction['transaction_id'], '2', $trade_service['test_mode']);
                                                            $con->commit();
                                                            $response['recipient_id'] = $trader_user_id;
                                                            $response['message'] = translate('successfull_convert');
                                                            $response['success'] = true;
                                                        } catch (Exception $e) {
                                                            $con->rollback();
                                                            $response['message'] = translate('try_again');
                                                            $response['success'] = false;
                                                        }
                                                    } else {
                                                        $response['message'] = translate('tader_no_have_enough_money');
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
                                } else {
                                    $response['message'] = translate('trader_required');
                                    $response['success'] = false;
                                }
                            }
                        } else {
                            $response['message'] = translate('trade_required');
                            $response['success'] = false;
                        }
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