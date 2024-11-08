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
            if (!empty($details['code'])) {
                $code = $details['code'];
                $sth= $con->prepare("SELECT a.*, b.user_name AS username FROM transactions_codes a INNER JOIN users b ON a.user_id = b.id WHERE a.transaction_code=?");
                $sth->execute(array($code));
                $code = $sth->fetch(\PDO::FETCH_ASSOC);
                if ($code) {
                    $sth= $con->prepare("SELECT * FROM wallets WHERE id=?");
                    $sth->execute(array($code['wallet_id']));
                    $wallet = $sth->fetch(\PDO::FETCH_ASSOC);
                    $code['wallet'] = $wallet;
                    unset($code['wallet_id']);
                    if ($code['type'] == '1') {
                        $code['type'] = 'transaction_code';
                    } else {
                        $code['type'] = 'username';
                    }
                    $response['data'] = $code;
                    $response['success'] = true;
                } else {
                    $response['message'] = translate('code_no_present');
                    $response['success'] = false;
                }
            } else {
                $response['message'] = translate('try_again');
                $response['success'] = false;
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