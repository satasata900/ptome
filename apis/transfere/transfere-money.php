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
            $transaction_type = $details['type']; // username or transaction_code or group
            $money = $details['money'];
            $wallet_id = $details['wallet'];

            // get sender wallet details
            $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
            $sth->execute(array($user_id,$wallet_id));
            $sender_wallet = $sth->fetch(\PDO::FETCH_ASSOC);

            // if transaction_type by username or transaction_code
            if (($transaction_type != 'group')) {
                if ($transaction_type == 'username') {
                    // get recipient wallet details
                    if (!empty($details['username'])) {
                        $username = $details['username'];
                        $sth= $con->prepare("SELECT * FROM users WHERE user_name=?");
                        $sth->execute(array($username));
                        $recipientDetails = $sth->fetch(\PDO::FETCH_ASSOC);
                        if ($recipientDetails) {
                            if ($recipientDetails['id'] == $user_id) {
                                $response['message'] = translate('no_transfere_to_yourself');
                                $response['success'] = false;
                                echo json_encode($response); 
                                return;
                            } else {
                                $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                $sth->execute(array($recipientDetails['id'],$wallet_id));
                                $recipient_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                if (!$recipient_wallet) {
                                   // create wallet for this user 
                                   $recipient_wallet = createWallet($con, $recipientDetails['id'],$wallet_id);
                                }
                            }
                        } else {
                            $response['message'] = translate('username_not_found');
                            $response['success'] = false;
                            echo json_encode($response); 
                            return;
                        }
                    } else {
                        $response['message'] = translate('username_required');
                        $response['success'] = false;
                        echo json_encode($response); 
                        return;
                    }
                } else if ($transaction_type == 'transaction_code') {
                    // get recipient wallet details
                    if (!empty($details['transaction_code'])) {
                        $transaction_code = $details['transaction_code'];
                        $sth= $con->prepare("SELECT * FROM transactions_codes WHERE transaction_code=?");
                        $sth->execute(array($transaction_code));
                        $transaction_codeDetails = $sth->fetch(\PDO::FETCH_ASSOC);
                        if ($transaction_codeDetails) {
                            if ($transaction_codeDetails['user_id'] == $user_id) {
                                $response['message'] = translate('no_transfere_to_yourself');
                                $response['success'] = false;
                                echo json_encode($response); 
                                return;
                            } else {
                                $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                $sth->execute(array($transaction_codeDetails['user_id'],$wallet_id));
                                $recipient_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                if (!$recipient_wallet) {
                                   // create wallet for this user 
                                   $recipient_wallet = createWallet($con, $recipientDetails['id'],$wallet_id);
                                }
                            }
                        } else {
                            $response['message'] = translate('transaction_code_not_found');
                            $response['success'] = false;
                            echo json_encode($response); 
                            return;
                        }
                    } else {
                        $response['message'] = translate('transaction_code_required');
                        $response['success'] = false;
                        echo json_encode($response); 
                        return;
                    }
                }
                // make the transfere
                if (!empty($sender_wallet) && !empty($recipient_wallet)) {
                    if ($sender_wallet['price'] >= $money) {
                        try {
                            $con->beginTransaction();
                            // update sender price
                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                            $stmt->execute(array($sender_wallet['price'] - $money,$sender_wallet['id']));
                            // update recipient price
                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                            $stmt->execute(array($recipient_wallet['price'] + $money,$recipient_wallet['id']));
                            // delete transaction_code if type is transaction_code
                            if ($transaction_type == 'transaction_code') {
                                $stmt=$con->prepare("DELETE FROM transactions_codes WHERE id=?");
                                $stmt->execute(array($transaction_codeDetails['id']));
                            } else {
                                $transaction_code = NULL;
                            }
                            // add transaction
                            $transaction = makeTransaction($con, $sender_wallet['user_id'], $recipient_wallet['user_id'], $money, $wallet_id, $transaction_type, $transaction_code, NULL, NULL, NULL);
                            // send notification
                            makeNotification($con, $recipient_wallet['user_id'], $user_id, 'money_transfer', $transaction['transaction_id'], '2', getUserTestStatus($con, $user_id));
                            $con->commit();
                            $response['transaction_id'] = $transaction['transaction_id'];
                            $response['recipient_id'] = $recipient_wallet['user_id'];
                            $response['message'] = translate('successfull_transfere');
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
            } else {
                // convert to group
                // check if group present
                if (!empty($details['group_id'])) {
                    $group_id = $details['group_id'];
                    $sth= $con->prepare("SELECT * FROM user_groups WHERE id=? AND user_id=?");
                    $sth->execute(array($group_id,$user_id));
                    $group = $sth->fetch(\PDO::FETCH_ASSOC);
                    if ($group) {
                        $sth= $con->prepare("SELECT * FROM group_members WHERE group_id=?");
                        $sth->execute(array($group_id));
                        $members = $sth->fetchAll(\PDO::FETCH_ASSOC);
                        if (count($members) == 0) {
                            $response['message'] = translate('group_has_no_member');
                            $response['success'] = false;
                        } else {
                            // make the transfere
                            if ($sender_wallet['price'] >= $money * count($members)) {
                                try {
                                    $con->beginTransaction(); 
                                    // update sender price
                                    $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                    $stmt->execute(array($sender_wallet['price'] - $money * count($members),$sender_wallet['id']));
                                    for ($i = 0; $i < count($members); $i++) {
                                        $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                                        $sth->execute(array($members[$i]['member_id'],$wallet_id));
                                        $recipient_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                        if ($recipient_wallet) {
                                            // update recipient price
                                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                            $stmt->execute(array($recipient_wallet['price'] + $money,$recipient_wallet['id']));
                                        } else {
                                            // create recipient wallet
                                            $recipient_wallet = createWallet($con, $recipient_wallet['user_id'], $wallet_id);
                                            // update recipient price
                                            $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                            $stmt->execute(array($recipient_wallet['price'] + $money,$recipient_wallet['id']));
                                        }
                                        // add transaction
                                        $transaction = makeTransaction($con, $sender_wallet['user_id'], $recipient_wallet['user_id'], $money, $wallet_id, 'group', NULL, NULL, NULL, NULL);
                                        // send notification
                                        makeNotification($con, $members[$i]['member_id'], $user_id, 'money_transfer', $transaction['transaction_id'], '2', getUserTestStatus($con, $user_id));
                                    }
                                    $con->commit();
                                    $response['message'] = translate('successfull_transfere');
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
                        }
                    } else {
                        $response['message'] = translate('no_group_found');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('group_required');
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