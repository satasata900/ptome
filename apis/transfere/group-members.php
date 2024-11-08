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
            if (!empty($details['group_id'])) {
                $group_id = $details['group_id'];
                // Find out how many items are in the table
                $stmt = $con->prepare( "SELECT COUNT(*) FROM group_members WHERE group_id=?");
                $stmt->execute(array($group_id));
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
                $stmt = $con->prepare( "SELECT a.*, b.user_name AS member_username FROM group_members a INNER JOIN users b ON a.member_id = b.id WHERE a.group_id=? AND a.member_nick_name LIKE '%".$search."%' ORDER BY member_nick_name LIMIT $items_per_page OFFSET $offset");
                $stmt->execute(array($group_id));
                $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $response['data'] = $members;
                $response['success'] = true;
                $response['total'] = (int) $total;
                $response['pages_no'] = $pages;
                $response['current_page'] = (int) $page;
                $response['items_per_page'] = (int) $items_per_page;
            } else {
                $response['message'] = translate('group_required');
                $response['success'] = false;
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