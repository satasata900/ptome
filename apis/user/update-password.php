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
        if (empty($details['new_password'])) {
            $response['message'] = translate('new_pass_required');
            $response['success'] = false;
        } else {
            $new_password = $details['new_password'];
            $uppercase = preg_match('@[A-Z]@', $new_password);
            $lowercase = preg_match('@[a-z]@', $new_password);
            $number    = preg_match('@[0-9]@', $new_password);
            $specialChars = preg_match('@[^\w]@', $new_password);
            if (strlen($new_password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
                $response['message'] = translate('password_valid');
                $response['success'] = false;
            } else {
                if (empty($details['new_password_confirm'])) {
                    $response['message'] = translate('new_password_confirm_required');
                    $response['success'] = false;
                } else {
                    $new_password_confirm = $details['new_password_confirm'];
                    $uppercase = preg_match('@[A-Z]@', $new_password_confirm);
                    $lowercase = preg_match('@[a-z]@', $new_password_confirm);
                    $number    = preg_match('@[0-9]@', $new_password_confirm);
                    $specialChars = preg_match('@[^\w]@', $new_password_confirm);
                    if (strlen($new_password_confirm) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
                        $response['message'] = translate('new_password_confirm_valid');
                        $response['success'] = false;
                    } else {
                        if ($new_password_confirm != $new_password) {
                            $response['message'] = translate('password_not_matched');
                            $response['success'] = false;
                        } else {
                            $stmt = $con->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
                            $stmt->execute(array($user_id));
                            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($user) {
                                if ($user['password'] == sha1($new_password)) {
                                    $response['message'] = translate('new_diff_old');
                                    $response['success'] = false;
                                } else {
                                    $stmt = $con->prepare("UPDATE users SET password=? WHERE id=? ");
                                    $stmt->execute(array(sha1($new_password), $user['id']));
                                    $response['success'] = true;
                                    $response['message'] = translate('password_changed');
                                }
                            } else {
                                $response['message'] = translate('no_user');
                                $response['success'] = false;
                            }
                        }
                    }
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
