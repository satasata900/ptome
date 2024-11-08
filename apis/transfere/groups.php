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
            $stmt = $con->prepare( "SELECT COUNT(*) FROM user_groups WHERE user_id=?");
            $stmt->execute(array($user_id));
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
            $stmt = $con->prepare( "SELECT * FROM user_groups WHERE user_id=? AND group_name LIKE '%".$search."%' ORDER BY group_name LIMIT $items_per_page OFFSET $offset");
            $stmt->execute(array($user_id));
            $groups = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($groups); $i++) {
                unset($groups[$i]['user_id']);
                $groups[$i]['creationTime'] = date('d-m-Y H:i', $groups[$i]['creationTime']);
                $stmt = $con->prepare( "SELECT COUNT(*) FROM group_members WHERE group_id=?");
                $stmt->execute(array($groups[$i]['id']));
                $group_members = $stmt->fetchColumn();
                $groups[$i]['members_count'] = (int) $group_members;
            }
            $response['data'] = $groups;
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