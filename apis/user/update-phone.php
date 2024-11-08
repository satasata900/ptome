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
    $token = getBearerToken();
    $user_id = getToken(getBearerToken());
    if ($token && $user_id) {
        if (empty($details['phone'])) {
            $response['message'] = translate('phone_required');
            $response['success'] = false;
        } else {
            $dialCode = $details['dialCode'];
            $isoCode = $details['isoCode'];
            $phone = $details['phone'];
            $userdetails = getUserDetails($con, $user_id, $images_url);
            if ($userdetails) {
                if ($userdetails['phone'] != $phone) {
                    $stmt = $con->prepare("UPDATE users SET dialCode=?, phone=?, verified_phone=?, isoCode=? WHERE id=? ");
                    $stmt->execute(array($dialCode, $phone, '0', $isoCode, $user_id));
                }
                $response['success'] = true;
                $response['message'] = translate('phone_changed');
            } else {
                $response['message'] = translate('no_user');
                $response['success'] = false;
            }
        }
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
