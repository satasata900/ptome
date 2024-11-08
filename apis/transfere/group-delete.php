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
                if (!empty($details['group_id'])) {
                    $group_id = $details['group_id'];
                    $sth= $con->prepare("SELECT * FROM user_groups WHERE id=? AND user_id=?");
                    $sth->execute(array($group_id, $user_id));
                    $group_present = $sth->fetch(\PDO::FETCH_ASSOC);
                    if ($group_present) {
                        $stmt=$con->prepare("DELETE FROM user_groups WHERE id=?");
                        $stmt->execute(array($group_id));
                        $response['message'] = translate('group_deleted');
                        $response['success'] = true;
                    } else {
                        $response['message'] = translate('group_required');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('group_required');
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