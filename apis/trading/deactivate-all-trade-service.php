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
                // get trader details
                $sth= $con->prepare("SELECT * FROM traders WHERE user_id=?");
                $sth->execute(array($user_id));
                $trader = $sth->fetch(\PDO::FETCH_ASSOC);
                if ($trader) {
                    // get trade services
                    $sth= $con->prepare("SELECT * FROM traders_services WHERE trader_id=?");
                    $sth->execute(array($trader['id']));
                    $trade_services = $sth->fetchAll(\PDO::FETCH_ASSOC);  
                    for ($i = 0; $i < count($trade_services); $i++) {
                        $stmt=$con->prepare("UPDATE traders_services SET active=? WHERE id=? ");
                        $stmt->execute(array('0',$trade_services[$i]['id']));
                    }
                    $response['success'] = true; 
                    $response['message'] = translate('trade_successfully_deactivated');
                } else {
                    $response['message'] = translate('not_access_file');
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