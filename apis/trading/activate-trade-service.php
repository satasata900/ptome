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
                    // get trade service
                    $sth= $con->prepare("SELECT * FROM traders_services WHERE id=?");
                    $sth->execute(array($trade_service_id));
                    $trade_service = $sth->fetch(\PDO::FETCH_ASSOC);          
                    if ($trade_service) {
                        // get trader details
                        $sth= $con->prepare("SELECT user_id FROM traders WHERE id=?");
                        $sth->execute(array($trade_service['trader_id']));
                        $trader_user_id = $sth->fetch(\PDO::FETCH_ASSOC);
                        if ($trader_user_id && $trader_user_id['user_id'] == $user_id) {
                            $stmt=$con->prepare("UPDATE traders_services SET active=? WHERE id=? ");
                            $stmt->execute(array('1',$trade_service_id));
                            $response['success'] = true; 
                            $response['message'] = translate('trade_successfully_activated');
                        } else {
                            $response['message'] = translate('not_access_file');
                            $response['success'] = false;
                        }
                    } else {
                        $response['message'] = translate('trade_required');
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