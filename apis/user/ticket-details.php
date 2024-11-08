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
            if (empty($details['ticket_id'])) {

            } else {
                $ticket_id = $details['ticket_id'];
                $stmt = $con->prepare( "SELECT * FROM user_tickets WHERE user_id=? AND id=?");
                $stmt->execute(array($user_id, $ticket_id));
                $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($ticket) {
                    $ticket['creationTime'] = convertDate($ticket['creationTime']);

                    $stmt = $con->prepare( "SELECT * FROM user_tickets_reply WHERE ticket_id=?");
                    $stmt->execute(array($ticket_id));
                    $replies = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    $ticket['replies'] =  $replies;
                    $response['data'] = $ticket;
                } else {

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