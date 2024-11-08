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
            if (getUserActiveStatus($con, $user_id)) {
                $response['message'] = translate('blocked_user');
                $response['success'] = false;  
            } else {
                if (empty($details['provider_name'])) {
                    $response['message'] = translate('provider_name_required');
                    $response['success'] = false;
                } else {
                    $provider_name = $details['provider_name'];
                    if (empty($details['provider_phone'])) {
                        $response['message'] = translate('provider_phone');
                        $response['success'] = false;
                    } else {
                        $provider_phone = $details['provider_phone'];
                        
                            $stmt = $con->prepare('SELECT * FROM providers WHERE user_id=?');
                            $stmt->execute(array($user_id));
                            $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($provider) {
                                if ($provider['approved_provider'] == '0') {
                                    $response['message'] = translate('already_have_request');
                                    $response['success'] = false;
                                } else {
                                    $response['message'] = translate('already_provider');
                                    $response['success'] = false;
                                }
                            } else {
                                $provider_data = array(
                                    'user_id'=>$user_id,
                                    'provider_name'=>$provider_name,
                                    'provider_phone'=>$provider_phone,
                                    'registeredAt'=>time()
                                );
                                if (!empty($details['provider_image'])) {
                                    $provider_image = $details['provider_image'];
                                    // upload image
                                    // Create path of files if not found
                                    if(!is_dir('../' . $imgs_dir)) {
                                        mkdir('../' . $imgs_dir); 
                                    }
                                    if(!is_dir('../' . $imgs_dir . 'providers')) {
                                        mkdir('../' . $imgs_dir . 'providers'); 
                                    }
                                    $extension = explode('/', mime_content_type($provider_image))[1];
                                    $image_path = 'providers/' .  time() . '.' . $extension;
                                    $full_path = '../' . $imgs_dir . $image_path;

                                    $exp = explode(',', $provider_image);
                                    $data = base64_decode($exp[1]);

                                    if(file_exists($full_path)){
                                        $counter = 0;
                                        while(file_exists($full_path)) {
                                            $counter++;
                                            $image_path =  'providers/' . time() . '_' . $counter . '.' . $extension;
                                            $full_path = '../' . $imgs_dir . $image_path;
                                        }
                                    }
                                    file_put_contents($full_path, $data);
                                    $provider_data['provider_image'] = $image_path;
                                }
                                $insert_request = $con->prepare(insert('providers', $provider_data))->execute();
                                if ($insert_request) {
                                    $response['message'] = translate('be_provider_successfully_added');
                                    // get provider details
                                    $stmt = $con->prepare('SELECT * FROM providers WHERE user_id=?');
                                    $stmt->execute(array($user_id));
                                    $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                                    $provider['provider_image'] = $images_url . $provider['provider_image'];
                                    $provider['registeredAt'] = convertDate($provider['registeredAt']);
                                    $response['provider'] = $provider;
                                    $response['success'] = true;
                                } else {
                                    $response['message'] = translate('try_again');
                                    $response['success'] = false;
                                }
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
    