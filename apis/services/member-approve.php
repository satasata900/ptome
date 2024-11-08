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
                if (empty($details['member_id'])) {
                    $response['message'] = translate('member_required');
                    $response['success'] = false;
                } else {
                    $member_id = $details['member_id'];
                    $stmt = $con->prepare('SELECT * FROM service_members WHERE id=?');
                    $stmt->execute(array($member_id));
                    $member = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($member) {
                        if ($member['active'] == '1') {
                            $response['message'] = translate('already_approved');
                            $response['success'] = false; 
                        } else {
                            $stmt = $con->prepare('SELECT * FROM services WHERE id=?');
                            $stmt->execute(array($service_id));
                            $service = $stmt->fetch(\PDO::FETCH_ASSOC);
                            $stmt=$con->prepare("UPDATE service_members SET active=? WHERE id=? ");
                            $stmt->execute(array('1',$member_id));
                            $response['success'] = true; 
                            $response['message'] = translate('member_approved');
                            makeNotification($con, $member['member_id'], $user_id, 'service_member_accept', $member['service_id'], '1', $service['test_mode']);
                        }
                    } else {
                        $response['message'] = translate('no_member');
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