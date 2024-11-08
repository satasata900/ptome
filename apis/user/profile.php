<?php
include('../connect_database.php');
$response = array();
if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'GET') {
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

    $token = getBearerToken();
    $user_id = getToken(getBearerToken());
    if ($token && $user_id) {
        $userdetails = getUserDetails($con, $user_id, $images_url);
        $response['success'] = true;
        $response['user'] = $userdetails;
    } else {
        $response['success'] = false;
        $response['message'] = translate('not_authentication');
        header('HTTP/1.1 401 Unauthorized');
    }
} else {
    $response['message'] = translate('not_access_file');
    $response['success'] = false;
}
echo json_encode($response);
