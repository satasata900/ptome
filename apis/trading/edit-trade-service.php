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
                if (empty($details['id'])) {
                    $response['message'] = translate('trade_required');
                    $response['success'] = false;
                } else {
                    $trade_id = $details['id'];
                    if (empty($details['exchange_rate'])) {
                        $response['message'] = translate('exchange_rate');
                        $response['success'] = false;
                    } else {
                        $exchange_rate = $details['exchange_rate'];
                        $stmt = $con->prepare('SELECT * FROM traders WHERE user_id=?');
                        $stmt->execute(array($user_id));
                        $trader = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($trader) {
                            // check if trade present
                            $sth= $con->prepare("SELECT * FROM traders_services WHERE trader_id=? AND id=?");
                            $sth->execute(array($trader['id'],$trade_id));
                            $is_found = $sth->fetch(\PDO::FETCH_ASSOC);
                            if ($is_found) {
                                $stmt=$con->prepare("UPDATE traders_services SET exchange_rate=? WHERE id=? ");
                                $stmt->execute(array($exchange_rate,$trade_id));
                                $response['message'] = translate('trade_edited');
                                $response['success'] = true;
                            } else {
                                $response['message'] = translate('trade_required');
                                $response['success'] = false;
                            }
                        } else {
                            $response['message'] = translate('not_trader');
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