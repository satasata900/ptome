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
           //get user name
           $stmt = $con->prepare( "SELECT id,full_name,user_name,pincode_require FROM users WHERE id=?");
           $stmt->execute(array($user_id));
           $user = $stmt->fetch(\PDO::FETCH_ASSOC);
           if ($user['pincode_require'] == '1') {
            $user['pincode_require'] = true;
            } else {
                $user['pincode_require'] = false;
            }
           $response['data']['user'] = $user;

           // get unreaded notifications
           $stmt = $con->prepare( "SELECT COUNT(*) FROM notifications WHERE recipient_id=? AND readedAt IS NULL");
           $stmt->execute(array($user_id));
           $total = $stmt->fetchColumn();
           $response['data']['notifications_count'] = $total;

           // get wallets
           $stmt = $con->prepare( "SELECT a.*, b.wallet_name, b.wallet_currency FROM users_wallets a INNER JOIN wallets b ON a.wallet_id = b.id WHERE a.user_id=? AND a.hidden=? AND a.test_mode=?");
           $stmt->execute(array($user_id, '0', getUserTestStatus($con, $user_id)));
           $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           for ($i = 0; $i < count($wallets); $i++) {
               unset($wallets[$i]['user_id']);
           }
           $response['data']['wallets'] = $wallets;

           // get transactions
           $stmt = $con->prepare( "SELECT * FROM transactions WHERE test_mode=? AND (sender_id=? OR recipient_id=?) Order By creationTime DESC LIMIT 5");
           $stmt->execute(array(getUserTestStatus($con, $user_id), $user_id,$user_id));
           $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           for ($i = 0; $i < count($transactions); $i++) {
                $transactions[$i]['creationTime'] = date('Y-m-d h:i:s', $transactions[$i]['creationTime']);
                $stmt = $con->prepare( "SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?");
                $stmt->execute(array($transactions[$i]['wallet']));
                $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                $transactions[$i]['wallet'] = $wallet;

                $stmt = $con->prepare( "SELECT id, user_name,full_name FROM users WHERE id=?");
                $stmt->execute(array($transactions[$i]['sender_id']));
                $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
                $transactions[$i]['sender'] = $sender;

                $stmt = $con->prepare( "SELECT id, user_name,full_name FROM users WHERE id=?");
                $stmt->execute(array($transactions[$i]['recipient_id']));
                $recipient = $stmt->fetch(\PDO::FETCH_ASSOC);
                $transactions[$i]['recipient'] = $recipient;
                
                if ($user_id == $transactions[$i]['sender_id']) {
                    $transactions[$i]['type'] = 'send';
                    // get invoice organization name
                    if ($transactions[$i]['transaction_type'] == 'invoice') {
                        $stmt = $con->prepare( "SELECT * FROM organizations WHERE user_id=?");
                        $stmt->execute(array($transactions[$i]['recipient_id']));
                        $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $transactions[$i]['recipient']['full_name'] = $organization['organization_name'];
                    }
                } else if ($user_id == $transactions[$i]['recipient_id']) {
                    $transactions[$i]['type'] = 'recieve';
                }
                unset($transactions[$i]['sender_id']);
                unset($transactions[$i]['recipient_id']);
           }
           $response['data']['transactions'] = $transactions;
           $response['success'] = true;             
        } 

    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 