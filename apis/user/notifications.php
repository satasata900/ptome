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
            $stmt = $con->prepare( "SELECT COUNT(*) FROM notifications WHERE sender_id=? OR recipient_id=?");
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
            $stmt = $con->prepare( "SELECT * FROM notifications WHERE recipient_id=? Order By creationTime DESC LIMIT $items_per_page OFFSET $offset");
            $stmt->execute(array($user_id));
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($notifications); $i++) {
                $notifications[$i]['creationTime'] = date('Y-m-d h:i:s', $notifications[$i]['creationTime']);
                if ($notifications[$i]['type'] == '1') {
                    // mean service
                    $stmt = $con->prepare( "SELECT * FROM services WHERE id=?");
                    $stmt->execute(array($notifications[$i]['notification_id']));
                    $service = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($lang == 'ar') {
                        $notifications[$i]['title'] = $service['service_name_ar'];
                    } else {
                        $notifications[$i]['title'] = $service['service_name_en'];
                    }
                    $notifications[$i]['image'] = $images_url . $service['service_image'];
                    if ($notifications[$i]['body'] == 'service_member_accept') {
                        $notifications[$i]['body'] = translate($notifications[$i]['body']);
                    } else if ($notifications[$i]['body'] == 'service_pay') {
                        $stmt = $con->prepare( "SELECT * FROM users WHERE id=?");
                        $stmt->execute(array($notifications[$i]['sender_id']));
                        $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $notifications[$i]['body'] = $sender['user_name'] . ' ' . translate($notifications[$i]['body']);
                    }
                } else if ($notifications[$i]['type'] == '2') {
                    $stmt = $con->prepare( "SELECT * FROM users WHERE id=?");
                    $stmt->execute(array($notifications[$i]['sender_id']));
                    $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $stmt = $con->prepare( "SELECT a.amount, b.wallet_currency FROM transactions a INNER JOIN wallets b ON a.wallet = b.id WHERE a.id=?");
                    $stmt->execute(array($notifications[$i]['notification_id']));
                    $transaction = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $notifications[$i]['title'] = translate('money_recieved');
                    $notifications[$i]['body'] = translate($notifications[$i]['body']) . ' ' . $transaction['amount'] . $transaction['wallet_currency'] . ' ' . translate('from') . ' ' . $sender['user_name'] . ' ';
                }
            }
            $response['data'] = $notifications;
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