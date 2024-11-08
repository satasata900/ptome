<?php
    include('../connect_database.php');
    $response = array();
    if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'GET') {
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
        if (empty($details['search'])) {
            $search = '';
        } else {
            $search = $details['search'];
        }
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            if (empty($details['transaction_id'])) {
                $response['message'] = translate('transaction_required');
                $response['success'] = false;
            } else {
                $transaction_id = $details['transaction_id'];
                $stmt = $con->prepare( "SELECT * FROM transactions WHERE id=? OR transaction_number=?");
                $stmt->execute(array($transaction_id, $transaction_id));
                $transaction = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($transaction) {
                    $transaction['creationTime'] = date('Y-m-d h:i:s', $transaction['creationTime']);

                    // get wallet details
                    $stmt = $con->prepare( "SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?");
                    $stmt->execute(array($transaction['wallet']));
                    $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($wallet) {
                        $transaction['wallet'] = $wallet;
                    }

                    // get sender details
                    $stmt = $con->prepare( "SELECT id, user_name,full_name FROM users WHERE id=?");
                    $stmt->execute(array($transaction['sender_id']));
                    $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($sender) {
                        $transaction['sender'] = $sender;
                    }

                    // get recipient details
                    $stmt = $con->prepare( "SELECT id, user_name,full_name FROM users WHERE id=?");
                    $stmt->execute(array($transaction['recipient_id']));
                    $recipient = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($recipient) {
                        $transaction['recipient'] = $recipient;
                    }


                    // transaction_type == transaction_code
                    if ($transaction['transaction_type'] == 'transaction_code') {
                        unset($transaction['trade_service_id']);
                        unset($transaction['service_id']);
                        unset($transaction['type_details']);
                    } else if ($transaction['transaction_type'] == 'username' || $transaction['transaction_type'] == 'group') {
                        unset($transaction['transaction_code']);
                        unset($transaction['trade_service_id']);
                        unset($transaction['service_id']);
                        unset($transaction['type_details']);
                    }

                    // get service details
                    if ($transaction['transaction_type'] == 'service') {
                        unset($transaction['transaction_code']);
                        unset($transaction['trade_service_id']);
                        $service = json_decode($transaction['type_details'], true);
                        if ($service) {
                            $service['service_image'] = $images_url . $service['service_image'];
                            $service['creationTime'] = convertDate($service['creationTime']);

                            if ($lang == 'ar') {
                                $service['service_name'] = $service['service_name_ar'];
                                $field_query = 'SELECT id, filed_ar_name AS field_name FROM fields WHERE id=?';
                                $city_query = 'SELECT id, city_ar_name AS city_name FROM cities WHERE id=?';
                            } else if ($lang == 'en') {
                                $service['service_name'] = $service['service_name_en'];
                                $field_query = 'SELECT id, filed_en_name AS field_name FROM fields WHERE id=?';
                                $city_query = 'SELECT id, city_en_name AS city_name FROM cities WHERE id=?';
                            }
                            // get provider details
                            $stmt = $con->prepare('SELECT * FROM providers WHERE id=?');
                            $stmt->execute(array($service['provider_id']));
                            $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                            $provider['provider_image'] = $images_url . $provider['provider_image'];
                            $provider['registeredAt'] = convertDate($provider['registeredAt']);
                            $service['provider_name'] = $provider['provider_name'];
                            $service['provider'] = $provider;
                            unset($service['provider_id']);
                            unset($service['service_name_ar']);
                            unset($service['service_name_en']);
                            if ($service['approved'] == '0') {
                                $service['approved'] = false;
                            } else {
                                $service['approved'] = true;
                            }
                            if ($service['active'] == '0') {
                                $service['active'] = false;
                            } else {
                                $service['active'] = true;
                            }
                            $stmt = $con->prepare($city_query);
                            $stmt->execute(array($service['city_id']));
                            $service['city'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                            unset($service['city_id']);
                            $stmt = $con->prepare($field_query);
                            $stmt->execute(array($service['field_id']));
                            $service['field'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                            unset($service['field_id']);
                            $stmt = $con->prepare('SELECT * FROM wallets WHERE id=?');
                            $stmt->execute(array($service['wallet_id']));
                            $service['wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                            unset($service['wallet_id']);
                            $transaction['service'] = $service;
                        }
                        unset($transaction['type_details']);
                        unset($transaction['service_id']);
                    }

                    // get trade details
                    if ($transaction['transaction_type'] == 'trade') {
                        $trade_service = json_decode($transaction['type_details'], true);
                        if ($trade_service) {
                            $trade_service['creationTime'] = convertDate($trade_service['creationTime']);
                            // get from_wallet
                            $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                            $stmt->execute(array($trade_service['from_wallet']));
                            $trade_service['from_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                            // get to_wallet
                            $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                            $stmt->execute(array($trade_service['to_wallet']));
                            $trade_service['to_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                            // get trader
                            $stmt = $con->prepare('SELECT id,trader_name,trader_image FROM traders WHERE id=?');
                            $stmt->execute(array($trade_service['trader_id']));
                            $trade = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($trade) {
                                $trade_service['trader_image'] = $images_url . $trade['trader_image'];
                                $trade_service['trader_name'] = $trade['trader_name'];
                            }
                            $transaction['trade'] = $trade_service;
                        }
                        unset($transaction['trade_service_id']);
                        unset($transaction['type_details']);
                        unset($transaction['transaction_code']);
                        unset($transaction['service_id']);
                    }

                    if ($user_id == $transaction['sender_id']) {
                        $transaction['type'] = 'send';
                        // get invoice organization name
                        if ($transaction['transaction_type'] == 'invoice') {
                            $stmt = $con->prepare( "SELECT * FROM organizations WHERE user_id=?");
                            $stmt->execute(array($transaction['recipient_id']));
                            $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
                            $transaction['recipient']['full_name'] = $organization['organization_name'];
                        }
                    } else if ($user_id == $transaction['recipient_id']) {
                        $transaction['type'] = 'recieve';
                    }
                    unset($transaction['sender_id']);
                    unset($transaction['recipient_id']);
                    $response['data'] = $transaction;
                    $response['success'] = true;
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