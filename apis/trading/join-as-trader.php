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
        if (getUserActiveStatus($con, $user_id)) {
            $response['message'] = translate('blocked_user');
            $response['success'] = false;  
        } else {
            if (empty($details['trader_name'])) {
                $response['message'] = translate('trader_name_required');
                $response['success'] = false;
            } else {
                $trader_name = $details['trader_name'];

                $stmt = $con->prepare('SELECT * FROM traders WHERE user_id=?');
                $stmt->execute(array($user_id));
                $trader = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($trader) {
                    $response['message'] = translate('already_trader');
                    $response['success'] = false;
                } else {
                    // upload image
                    // Create path of files if not found
                    if (!empty($details['trader_image'])) {
                        $trader_image = $details['trader_image'];
                        if (!is_dir('../' . $imgs_dir)) {
                            mkdir('../' . $imgs_dir);
                        }
                        if (!is_dir('../' . $imgs_dir . 'traders')) {
                            mkdir('../' . $imgs_dir . 'traders');
                        }
                        $extension = explode('/', mime_content_type($trader_image))[1];
                        $image_path = 'traders/' .  time() . '.' . $extension;
                        $full_path = '../' . $imgs_dir . $image_path;

                        $exp = explode(',', $trader_image);
                        $data = base64_decode($exp[1]);

                        if (file_exists($full_path)) {
                            $counter = 0;
                            while (file_exists($full_path)) {
                                $counter++;
                                $image_path =  'traders/' . time() . '_' . $counter . '.' . $extension;
                                $full_path = '../' . $imgs_dir . $image_path;
                            }
                        }
                        file_put_contents($full_path, $data);
                    } else {
                        $image_path = 'traders/no_image.png	';
                    }
                    $trader_data = array(
                        'user_id' => $user_id,
                        'trader_name' => $trader_name,
                        'trader_image' => $image_path,
                        'registeredAt' => time()
                    );
                    $insert_request = $con->prepare(insert('traders', $trader_data))->execute();
                    if ($insert_request) {
                        $response['message'] = translate('be_trader_successfully_added');
                        // get trader details
                        $stmt = $con->prepare('SELECT * FROM traders WHERE user_id=?');
                        $stmt->execute(array($user_id));
                        $trader = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $trader['trader_image'] = $images_url . $trader['trader_image'];
                        $trader['registeredAt'] = convertDate($trader['registeredAt']);
                        $trader['myservices'] = array();
                        $response['trader'] = $trader;
                        $response['success'] = true;
                    } else {
                        $response['message'] = translate('try_again');
                        $response['success'] = false;
                    }
                }
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
