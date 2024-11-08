<?php
    $home = true;
    include('connect_database.php');
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
        $user_id;
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $user_id = getToken($_SERVER['HTTP_TOKEN']);
        } 
        if (empty($user_id)) {
            $response['message'] = translate('no_user');
            $response['success'] = false;
        } else {
            //get user name
            $stmt = $con->prepare( "SELECT id,full_name,user_name,pincode_require,notifications_on_off,test_mode FROM users WHERE id=?");
            $stmt->execute(array($user_id));
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($user['pincode_require'] == '1') {
                $user['pincode_require'] = true;
            } else {
                $user['pincode_require'] = false;
            }
            if ($user['notifications_on_off'] == '1') {
                $user['notifications_on_off'] = true;
            } else {
                $user['notifications_on_off'] = false;
            }
            if ($user['test_mode'] == '1') {
                $user['test_mode'] = true;
            } else {
                $user['test_mode'] = false;
            }
            $response['data']['user'] = $user;
            // get unreaded notifications
            $stmt = $con->prepare( "SELECT COUNT(*) FROM notifications WHERE recipient_id=? AND readedAt IS NULL");
            $stmt->execute(array($user_id));
            $total = $stmt->fetchColumn();
            $response['data']['notifications_count'] = $total;

            // get app version
            $stmt = $con->prepare('SELECT * FROM app_settings WHERE name=?');
            $stmt->execute(array('version'));
            $present = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($present) {
                $response['success'] = true; 
                $response['data']['version'] = $present['body']; 
            } else {
                $response['success'] = true;
                $response['data']['version'] = '1.0.0'; 
            } 
            $response['success'] = true;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 