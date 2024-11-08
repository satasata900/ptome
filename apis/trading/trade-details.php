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
            if (empty($details['trade_service_id'])) {
                $response['message'] = translate('trade_required');
                $response['success'] = false;
            } else {
                $trade_service_id = $details['trade_service_id'];
                // get trade service
                $sth= $con->prepare("SELECT * FROM traders_services WHERE id=?");
                $sth->execute(array($trade_service_id));
                $trade_service = $sth->fetch(\PDO::FETCH_ASSOC);          
                if ($trade_service) {
                    // get trader details
                    $sth= $con->prepare("SELECT user_id FROM traders WHERE id=?");
                    $sth->execute(array($trade_service['trader_id']));
                    $trader_user_id = $sth->fetch(\PDO::FETCH_ASSOC);
                    if ($user_id == $trader_user_id['user_id']) {
                        $trade_service['owner'] = true;
                    } else {
                        $trade_service['owner'] = false;
                    }

                    $trade_service['creationTime'] = convertDate($trade_service['creationTime']);
                    $stmt = $con->prepare('SELECT id, id AS wallet_id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                    $stmt->execute(array($trade_service['from_wallet']));
                    $trade_service['from_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                    $stmt->execute(array($trade_service['to_wallet']));
                    $trade_service['to_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $response['success'] = true;
                    $response['data'] = $trade_service;
                } else {
                    $response['message'] = translate('trade_required');
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