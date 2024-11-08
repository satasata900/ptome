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
                if (empty($details['trader_id'])) {
                    $response['message'] = translate('trader_required');
                    $response['success'] = false;
                } else {
                    $trader_id = $details['trader_id'];
                    if (empty($details['rate'])) {
                        $response['message'] = translate('rate_required');
                        $response['success'] = false;
                    } else {
                        $rate = $details['rate'];
                        // get trader details
                        $sth= $con->prepare("SELECT * FROM traders WHERE id=?");
                        $sth->execute(array($trader_id));
                        $trader = $sth->fetch(\PDO::FETCH_ASSOC);
                        if ($trader) {
                            if ($user_id == $trader['user_id']) {
                                $response['success'] = false; 
                                $response['message'] = translate('no_rate_yourself');
                            } else {
                                // check if user rate before
                                // get trader details
                                $sth= $con->prepare("SELECT * FROM trader_rates WHERE user_id=? AND trader_id=?");
                                $sth->execute(array($user_id,$trader_id));
                                $rate_before = $sth->fetch(\PDO::FETCH_ASSOC);
                                if ($rate_before) {
                                    $stmt=$con->prepare("UPDATE trader_rates SET rate=?, creationTime=? WHERE id=? ");
                                    $stmt->execute(array($rate,time(),$rate_before['id']));
                                    $response['success'] = true; 
                                    $response['message'] = translate('trade_successfully_rated');
                                } else {
                                    $data = array(
                                        'trader_id'=>$trader_id,
                                        'user_id'=>$user_id,
                                        'rate'=>$rate,
                                        'creationTime'=>time()
                                    );
                                    $insert_request = $con->prepare(insert('trader_rates', $data))->execute();
                                    if ($insert_request) {
                                        $response['success'] = true; 
                                        $response['message'] = translate('trade_successfully_rated');
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