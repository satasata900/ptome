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
                if (empty($details['service_id'])) {
                    $response['message'] = translate('service_required');
                    $response['success'] = false;
                } else {
                    $service_id = $details['service_id'];
                    $stmt = $con->prepare('SELECT * FROM services WHERE id=?');
                    $stmt->execute(array($service_id));
                    $service = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($service) {
                        if ($service['active'] == '0') {
                            $active = '1';
                            $response['active'] = true;
                            $response['message'] = translate('service_status_activated');    
                        } else {
                            $active = '0';
                            $response['active'] = false;
                            $response['message'] = translate('service_status_deactivated');    
                        }
                        $stmt=$con->prepare("UPDATE services SET active=? WHERE id=? ");
                        $stmt->execute(array($active,$service_id));
                        $response['success'] = true; 
                    } else {
                        $response['message'] = translate('service_required');
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