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
            // get trader details
            $stmt = $con->prepare('SELECT * FROM traders WHERE user_id=?');
            $stmt->execute(array($user_id));
            $trader = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($trader) {
                $trader['trader_image'] = $images_url . $trader['trader_image'];
                $trader['registeredAt'] = convertDate($trader['registeredAt']);
                $stmt = $con->prepare('SELECT * FROM traders_services WHERE trader_id=? AND test_mode=?');
                $stmt->execute(array($trader['id'], getUserTestStatus($con, $user_id)));
                $trader_services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                for ($i = 0; $i < count($trader_services); $i++) {
                    $trader_services[$i]['trader_name'] = $trader['trader_name'];
                    $trader_services[$i]['trader_image'] = $trader['trader_image'];
                    $trader_services[$i]['creationTime'] = convertDate($trader_services[$i]['creationTime']);
                    $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                    $stmt->execute(array($trader_services[$i]['from_wallet']));
                    $trader_services[$i]['from_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                    $stmt->execute(array($trader_services[$i]['to_wallet']));
                    $trader_services[$i]['to_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                    $stmt = $con->prepare('SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?');
                    $stmt->execute(array($trader['user_id'], $trader_services[$i]['from_wallet']['id']));
                    $user_wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $trader_services[$i]['trader_amount'] = $user_wallet['price'];
                }
                unset($trader['trader_name']);
                unset($trader['trader_image']);
                $trader['myservices'] = $trader_services;
            }
            $response['data']['trader'] = $trader;
            // Find out how many items are in the table
            $stmt = $con->prepare( "SELECT COUNT(*) FROM traders_services WHERE active='1' AND test_mode='" . getUserTestStatus($con, $user_id) . "'");
            $stmt->execute();
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
            // get services
            $stmt = $con->prepare("SELECT a.*, b.trader_name, b.trader_image, user_id FROM traders_services a INNER JOIN traders b ON a.trader_id = b.id WHERE a.active=? AND a.test_mode=? AND b.trader_name LIKE '%".$search."%' LIMIT $items_per_page OFFSET $offset");
            $stmt->execute(array('1', getUserTestStatus($con, $user_id)));
            $trader_services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($trader_services); $i++) {
                unset($trader_services[$i]['trader_name']);
                
                $stmt = $con->prepare('SELECT user_name FROM users WHERE id=?');
                $stmt->execute(array($trader_services[$i]['user_id']));
                $trader_user = $stmt->fetch(\PDO::FETCH_ASSOC);
                $trader_user['user_name'];
                
                $trader_services[$i]['user_name'] = $trader_user['user_name'];
                unset($trader_services[$i]['user_id']);
                
                $trader_services[$i]['trader_image'] = $images_url . $trader_services[$i]['trader_image'];
                $trader_services[$i]['creationTime'] = convertDate($trader_services[$i]['creationTime']);
                $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                $stmt->execute(array($trader_services[$i]['from_wallet']));
                $trader_services[$i]['from_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                $stmt = $con->prepare('SELECT id,wallet_name,wallet_currency FROM wallets WHERE id=?');
                $stmt->execute(array($trader_services[$i]['to_wallet']));
                $trader_services[$i]['to_wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                $stmt = $con->prepare('SELECT * FROM traders WHERE id=?');
                $stmt->execute(array($trader_services[$i]['trader_id']));
                $trader = $stmt->fetch(\PDO::FETCH_ASSOC);
                $stmt = $con->prepare('SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?');
                $stmt->execute(array($trader['user_id'], $trader_services[$i]['from_wallet']['id']));
                $user_wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                $trader_services[$i]['trader_amount'] = $user_wallet['price'];
            }     
            $response['data']['services'] = $trader_services;
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