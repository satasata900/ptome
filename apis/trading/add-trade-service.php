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
                if (empty($details['from_wallet'])) {
                    $response['message'] = translate('wallet_required');
                    $response['success'] = false;
                } else {
                    $stmt = $con->prepare('SELECT * FROM traders WHERE user_id=?');
                    $stmt->execute(array($user_id));
                    $trader = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($trader) {
                        $from_wallet_id = $details['from_wallet'];
                        if (empty($details['to_wallet'])) {
                            $response['message'] = translate('wallet_required');
                            $response['success'] = false;
                        } else {
                            $to_wallet_id = $details['to_wallet'];
                            if (empty($details['exchange_rate'])) {
                                $response['message'] = translate('exchange_rate_required');
                                $response['success'] = false;
                            } else {
                                $exchange_rate = $details['exchange_rate'];
                                // get from wallet details
                                $sth= $con->prepare("SELECT * FROM wallets WHERE id=?");
                                $sth->execute(array($from_wallet_id));
                                $from_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                // get to wallet details
                                $sth= $con->prepare("SELECT * FROM wallets WHERE id=?");
                                $sth->execute(array($to_wallet_id));
                                $to_wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                                if ($from_wallet && $to_wallet) {
                                    if ($from_wallet_id == $to_wallet_id) {
                                        $response['message'] = translate('from_equal_to');
                                        $response['success'] = false;
                                    } else {
                                        // check if has another trade
                                        $sth= $con->prepare("SELECT * FROM traders_services WHERE trader_id=? AND from_wallet=? AND to_wallet=?");
                                        $sth->execute(array($trader['id'],$from_wallet_id,$to_wallet_id));
                                        $is_found = $sth->fetch(\PDO::FETCH_ASSOC);
                                        if ($is_found) {
                                            $response['message'] = translate('another_trade_found');
                                            $response['success'] = false;
                                        } else {
                                            $data = array(
                                                'trader_id'=>$trader['id'],
                                                'from_wallet'=>$from_wallet_id,
                                                'to_wallet'=>$to_wallet_id,
                                                'exchange_rate'=>$exchange_rate,
                                                'creationTime'=>time(),
                                                'test_mode'=>getUserTestStatus($con, $user_id)
                                            );
                                            $insert_request = $con->prepare(insert('traders_services', $data))->execute();
                                            if ($insert_request) {
                                                $response['message'] = translate('trade_added');
                                                $response['success'] = true;
                                            } else {
                                                $response['message'] = translate('try_again');
                                                $response['success'] = false;
                                            }
                                        }    
                                    }
                                } else {
                                    $response['message'] = translate('wallet_required');
                                    $response['success'] = false;
                                }
                            }
                        }
                    } else {
                        $response['message'] = translate('not_trader');
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