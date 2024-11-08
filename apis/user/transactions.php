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
        if (empty($details['search'])) {
            $search = '';
        } else {
            $search = $details['search'];
        }
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            // Find out how many items are in the table
            $stmt = $con->prepare( "SELECT COUNT(*) FROM transactions WHERE test_mode='" . getUserTestStatus($con, $user_id) . "' AND (sender_id=? OR recipient_id=?)");
            $stmt->execute(array($user_id,$user_id));
            $total = $stmt->fetchColumn();
            // How many pages will there be
            $pages = ceil($total / $items_per_page);
            // What page are we currently on?
            if (empty($details['page'])) {
                $page = 1;
            } else {
                $page = $details['page'];
            }
            // Calculate the offset for the query
            $offset = ($page - 1)  * $items_per_page;
            // Some information to display to the user
            $start = $offset + 1;
            $end = min(($offset + $items_per_page), $total);
            // get data
            if (!empty($details['wallet_id'])) {
                $wallet_id = $details['wallet_id'];
                $stmt = $con->prepare( "SELECT id, sender_id, recipient_id, creationTime, transaction_number, amount, wallet, transaction_type  FROM transactions WHERE (sender_id=? OR recipient_id=?) AND wallet=? AND test_mode=? Order By creationTime DESC LIMIT $items_per_page OFFSET $offset");
                $stmt->execute(array($user_id,$user_id,$wallet_id, getUserTestStatus($con, $user_id)));
            } else {
                $stmt = $con->prepare( "SELECT id, sender_id, recipient_id, creationTime, transaction_number, amount, wallet, transaction_type FROM transactions WHERE test_mode=? AND (sender_id=? OR recipient_id=?) Order By creationTime DESC LIMIT $items_per_page OFFSET $offset");
                $stmt->execute(array(getUserTestStatus($con, $user_id), $user_id,$user_id));
            }
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
            $response['data'] = $transactions;
            $response['success'] = true;
            $response['total'] = (int) $total;
            $response['pages_no'] = $pages;
            $response['current_page'] = (int) $page;
            $response['items_per_page'] = (int) $items_per_page;
        } else {
            $response['message'] = translate('not_authentication');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 