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
        $stmt = $con->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute(array($user_id));
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($user) {
            if (empty($details['full_name'])) {
                $response['message'] = translate('full_name_required');
                $response['success'] = false;
            } else {
                $full_name = $details['full_name'];
                if (strlen($full_name) < 6) {
                    $response['message'] = translate('full_name_length');
                    $response['success'] = false;
                } else {
                    $birthdate = $details['birthdate'];

                    if (!empty($details['picture']) && $details['picture'] != $images_url . $user['picture']) {
                        $picture = $details['picture'];
                        // upload image
                        // Create path of files if not found
                        if(!is_dir('../' . $imgs_dir)) {
                            mkdir('../' . $imgs_dir); 
                        }
                        if(!is_dir('../' . $imgs_dir . 'users')) {
                            mkdir('../' . $imgs_dir . 'users'); 
                        }
                        $extension = explode('/', mime_content_type($picture))[1];
                        $image_path = 'users/' .  time() . '.' . $extension;
                        $full_path = '../' . $imgs_dir . $image_path;

                        $exp = explode(',', $picture);
                        $data = base64_decode($exp[1]);

                        if(file_exists($full_path)){
                            $counter = 0;
                            while(file_exists($full_path)) {
                                $counter++;
                                $image_path =  'users/' . time() . '_' . $counter . '.' . $extension;
                                $full_path = '../' . $imgs_dir . $image_path;
                            }
                        }
                        file_put_contents($full_path, $data);
                        if (file_exists('../' . $imgs_dir . $user['picture']) && $user['picture'] != 'users/no_img.png'){
                            unlink('../' . $imgs_dir . $user['picture']);
                        }
                        $stmt = $con->prepare("UPDATE users SET full_name=?, birthdate=?, picture=? WHERE id=? ");
                        $stmt->execute(array($full_name, $birthdate, $image_path, $user_id));
                    } else {
                        $stmt = $con->prepare("UPDATE users SET full_name=?, birthdate=? WHERE id=? ");
                        $stmt->execute(array($full_name, $birthdate, $user_id));
                    }
                    $response['success'] = true;
                    $response['message'] = translate('profile_updated');
                }
            }
        } else {
            $response['message'] = translate('no_user');
            $response['success'] = false;
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
