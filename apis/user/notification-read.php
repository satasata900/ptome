<?php
    include('../connect_database.php');
    $response = array();
    if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'POST') {
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
            if (empty($details['notification_id'])) {
                $response['message'] = translate('notification_required');
                $response['success'] = false;
            } else {
                $notification_id = $details['notification_id'];
                $stmt = $con->prepare( "SELECT * FROM notifications WHERE id=? LIMIT 1");
                $stmt->execute(array($notification_id));
                $notification = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($notification) {
                    if ($notification['recipient_id'] == $user_id) {
                        if (empty($notification['readedAt']))  {
                            $readedAt = time();
                            $stmt=$con->prepare("UPDATE notifications SET readedAt=? WHERE id=? ");
                            $stmt->execute(array($readedAt,$notification_id));
                            $response['readedAt'] = $readedAt;
                            $response['success'] = true;
                            $response['message'] = translate('notification_readed');  
                        } else {
                            $response['readedAt'] = (int) $notification['readedAt'];
                            $response['success'] = true;
                        }
                    } else {
                        $response['message'] = translate('notification_required');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('notification_required');
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

