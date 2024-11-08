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

            // generate transaction code
            $transaction_code = generateTransactionCode($con, $user_id);
            $response['data']['transaction_code'] = $transaction_code;
            

            // get groups
            $stmt = $con->prepare( "SELECT id,group_name FROM user_groups WHERE user_id=? ORDER BY group_name");
            $stmt->execute(array($user_id));
            $groups = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($groups); $i++) {
                $stmt = $con->prepare( "SELECT COUNT(*) FROM group_members WHERE group_id=?");
                $stmt->execute(array($groups[$i]['id']));
                $group_members = $stmt->fetchColumn();
                $groups[$i]['members_count'] = (int) $group_members;
            }
            $response['data']['groups'] = $groups;

            // get wallets
            $response['data']['wallets'] = getWallets($con,$user_id);

            $response['success'] = true;
        } else {
            $response['message'] = translate('not_authentication');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 