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
        if (!empty($details['admin'])) {
            $admin = true;
        } else {
            $admin = false;
        }
        if (empty($details['type'])) {
            $response['message'] = translate('app_settings_name');
            $response['success'] = false;
        } else {
            $type = $details['type'];
            // check if present
            $stmt = $con->prepare('SELECT * FROM pages WHERE name=?');
            $stmt->execute(array($type));
            $present = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($present) {
                $response['success'] = true; 
                if ($admin) {
                    $response['data'] = $present;
                } else {
                    if ($lang == 'ar') {
                        $response['data'] = $present['ar_content'];
                    } else {
                        $response['data'] = $present['en_content'];
                    }
                }
            } else {
                $response['success'] = true;
                $response['data'] = ''; 
            }      
        }          
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response);
    