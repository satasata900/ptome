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
        if (empty($details['new_email'])) {
            $response['message'] = translate('new_mail_required');
            $response['success'] = false;
        } else {
            $new_email = $details['new_email'];
            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = translate('valid_email');
                $response['success'] = false;
            } else {
                $user = getUserDetails($con, $user_id, $images_url);
                if ($user) {
                    if ($user['email'] == $new_email) {
                        $response['message'] = translate('new_mail_diff_old');
                        $response['success'] = false;
                    } else {
                        $stmt = $con->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
                        $stmt->execute(array($new_email));
                        $new_emailIs_present = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($new_emailIs_present) {
                            $response['message'] = translate('email_registered_before');
                            $response['success'] = false;
                        } else {
                            $stmt = $con->prepare("UPDATE users SET email=?, verified_email=? WHERE id=? ");
                            $stmt->execute(array($new_email, '0', $user['id']));
                            $response['success'] = true;
                            $response['message'] = translate('email_changed');
                        }
                    }
                } else {
                    $response['message'] = translate('no_user');
                    $response['success'] = false;
                }
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
