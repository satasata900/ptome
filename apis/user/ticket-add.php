<?php
include('../connect_database.php');
$response = array();
if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // to get data in post
    if (!empty($_POST)) {
        $details = $_POST;
    } else {
        $post = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() == JSON_ERROR_NONE) {
            $details = $post;
        } else {
            $details = array();
        }
    }
    // to get data in get
    if (!empty($_GET)) {
        $details += $_GET;
    }
    if (!empty($_SERVER['HTTP_TOKEN'])) {
        $token = $_SERVER['HTTP_TOKEN'];
        $user_id = getToken($token);
        if (empty($details['ticket_id'])) {
            // new ticket
            if (empty($details['message'])) {
                $response['message'] = translate('contact_message_required');
                $response['success'] = false;
            } else {
                $message = $details['message'];
                $time = time();
                $data = array(
                    'user_id' => $user_id,
                    'creationTime' => $time
                );
                $insert_request = $con->prepare(insert('user_tickets', $data))->execute();
                if ($insert_request) {
                    $conn = ("SELECT LAST_INSERT_ID()");
                    $stmt = $con->prepare($conn);
                    $stmt->execute();
                    $ticket_id = $stmt->fetchColumn();
                    $data = array(
                        'user_id' => $user_id,
                        'ticket_id' => $ticket_id,
                        'sender' => '2',
                        'message' => $message,
                        'creationTime' => $time
                    );
                    $insert_request = $con->prepare(insert('user_tickets_reply', $data))->execute();
                    if ($insert_request) {
                        $response['success'] = true;
                        $response['message'] = translate('contact_message_sent');
                    }
                }
            }
        } else {
            $ticket_id = $details['ticket_id'];
            $stmt = $con->prepare( "SELECT * FROM user_tickets WHERE user_id=? AND id=?");
                $stmt->execute(array($user_id, $ticket_id));
                $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($ticket) {
                    if ($ticket['state'] == 'closed') {
                        $response['message'] = translate('closed_ticket');
                        $response['success'] = false;
                    } else {
                        $data = array(
                            'user_id' => $user_id,
                            'ticket_id' => $ticket_id,
                            'sender' => '2',
                            'message' => $details['message'],
                            'creationTime' => time()
                        );
                        $insert_request = $con->prepare(insert('user_tickets_reply', $data))->execute();
                        if ($insert_request) {
                            $response['success'] = true;
                            $response['message'] = translate('contact_message_sent');
                        }
                    }
                } else {
                    $response['message'] = translate('no_ticket');
                    $response['success'] = false;
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
