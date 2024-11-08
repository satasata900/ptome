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
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            if (empty($details['service_id'])) {
                $response['message'] = translate('service_required');
                $response['success'] = false;
            } else {
                $service_id = $details['service_id'];
                // get service details
                $stmt = $con->prepare('SELECT * FROM services WHERE id=?');
                $stmt->execute(array($service_id));
                $service = $stmt->fetch(\PDO::FETCH_ASSOC);
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
                    $response['data']['service'] = $service;
                    if ($provider['user_id'] == $user_id) {
                        // get members if this is the owner
                        $response['data']['owner'] = true;
                        if ($service['approved']) {
                            $stmt = $con->prepare('SELECT a.*,b.user_name FROM service_members a INNER JOIN users b ON a.member_id = b.id WHERE a.service_id=? AND a.active="0" ORDER BY b.user_name ASC');
                            $stmt->execute(array($service['id']));
                            $inactivated_members = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            for ($i = 0; $i < count($inactivated_members); $i++) {
                                $inactivated_members[$i]['subscribtion_date'] = convertDate($inactivated_members[$i]['subscribtion_date']);
                                if (!empty($inactivated_members[$i]['last_bill_date'])) {
                                    $inactivated_members[$i]['last_bill_date'] = convertDate($inactivated_members[$i]['last_bill_date']);
                                }
                            }
                            $response['data']['inactivated_members'] = $inactivated_members;
                            $stmt = $con->prepare('SELECT a.*,b.user_name FROM service_members a INNER JOIN users b ON a.member_id = b.id WHERE a.service_id=? AND a.active="1" ORDER BY b.user_name ASC');
                            $stmt->execute(array($service['id']));
                            $activated_members = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                            for ($i = 0; $i < count($activated_members); $i++) {
                                $activated_members[$i]['subscribtion_date'] = convertDate($activated_members[$i]['subscribtion_date']);
                                if (!empty($activated_members[$i]['last_bill_date'])) {
                                    $activated_members[$i]['last_bill_date'] = convertDate($activated_members[$i]['last_bill_date']);
                                }
                            }
                            $response['data']['activated_members'] = $activated_members;
                        } else {
                            $response['data']['members'] = array();
                        }
                    } else {
                        // this is not the user
                        $response['data']['owner'] = false;
                        $stmt = $con->prepare('SELECT * FROM service_members WHERE service_id=? AND member_id=?');
                        $stmt->execute(array($service['id'], $user_id));
                        $is_subscribed = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($is_subscribed) {
                            $response['data']['subscribed'] = true;
                            if ($is_subscribed['active'] == '0') {
                                $response['data']['subscription_status'] = false;
                            } else {
                                $response['data']['subscription_status'] = true;
                                $is_subscribed['last_bill_date'] = date('Y-m-d h:i:s', $is_subscribed['last_bill_date']);
                                $response['data']['subscription'] = $is_subscribed;
                                // return notifications
                                $response['data']['notifications'] = array();
                                // return transactions
                                $stmt = $con->prepare( "SELECT * FROM transactions WHERE (sender_id=? OR recipient_id=?) AND service_id=? Order By creationTime");
                                $stmt->execute(array($user_id,$user_id,$service_id));
                                $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                                for ($i = 0; $i < count($transactions); $i++) {
                                    $transactions[$i]['creationTime'] = date('Y-m-d h:i:s', $transactions[$i]['creationTime']);
                                    $stmt = $con->prepare( "SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?");
                                    $stmt->execute(array($transactions[$i]['wallet']));
                                    $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                                    $transactions[$i]['wallet'] = $wallet;
                                    $stmt = $con->prepare( "SELECT id, user_name FROM users WHERE id=?");
                                    $stmt->execute(array($transactions[$i]['sender_id']));
                                    $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
                                    $transactions[$i]['sender'] = $sender;
                                    $stmt = $con->prepare( "SELECT id, user_name FROM users WHERE id=?");
                                    $stmt->execute(array($transactions[$i]['recipient_id']));
                                    $recipient = $stmt->fetch(\PDO::FETCH_ASSOC);
                                    $transactions[$i]['recipient'] = $recipient;
                                    if ($user_id == $transactions[$i]['sender_id']) {
                                        $transactions[$i]['type'] = 'send';
                                    } else if ($user_id == $transactions[$i]['recipient_id']) {
                                        $transactions[$i]['type'] = 'recieve';
                                    }
                                    unset($transactions[$i]['sender_id']);
                                    unset($transactions[$i]['recipient_id']);
                                }
                                $response['data']['transactions'] = $transactions;
                            }
                        } else {
                            $response['data']['subscribed'] = false;
                        }
                    }
                    $response['success'] = true;
                } else {
                    $response['message'] = translate('service_required');
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