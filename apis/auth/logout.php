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
        $user_id;
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $user_id = getToken($_SERVER['HTTP_TOKEN']);
        } 
        if (empty($user_id)) {
            $response['message'] = translate('no_user');
            $response['success'] = false;
        } else {
            $stmt = $con->prepare( "SELECT * FROM users WHERE id=? LIMIT 1");
            $stmt->execute(array($user_id));
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($user) {
                if (!empty($details['platform_token'])) {
                    $stmt=$con->prepare("DELETE FROM tokens WHERE platform_token=? ");
                    $stmt->execute(array($details['platform_token']));
                }
                $response['message'] = translate('success_logout');
                $response['success'] = true;
            } else {
                $response['message'] = translate('no_user');
                $response['success'] = false;
            }         
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 

