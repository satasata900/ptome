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
                // check if is provider
                $stmt = $con->prepare('SELECT * FROM providers WHERE user_id=?');
                $stmt->execute(array($user_id));
                $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($provider) {
                    if ($provider['approved_provider'] == '1') {
                        if (empty($details['service_name_ar'])) {
                            $response['message'] = translate('service_name_ar');
                            $response['success'] = false;
                        } else {
                            if (empty($details['service_name_en'])) {
                                $response['message'] = translate('service_name_en');
                                $response['success'] = false;
                            } else {
                                    if (empty($details['description'])) {
                                        $response['message'] = translate('service_description');
                                        $response['success'] = false;
                                    } else {
                                        if (empty($details['field'])) {
                                            $response['message'] = translate('service_field');
                                            $response['success'] = false;
                                        } else {
                                            if (empty($details['city'])) {
                                                $response['message'] = translate('service_city');
                                                $response['success'] = false;
                                            } else {
                                                if (empty($details['address'])) {
                                                    $response['message'] = translate('service_address');
                                                    $response['success'] = false;
                                                } else {
                                                    if (empty($details['wallet_id'])) {
                                                        $response['message'] = translate('service_wallet_id');
                                                        $response['success'] = false;
                                                    } else {
                                                        if (empty($details['amount'])) {
                                                            $response['message'] = translate('service_amount');
                                                            $response['success'] = false;
                                                        } else {
                                                            $service_name_ar = $details['service_name_ar'];
                                                            $service_name_en = $details['service_name_en'];
                                                            $service_description = $details['description'];
                                                            $service_field = $details['field'];
                                                            $service_city = $details['city'];
                                                            $service_address = $details['address'];
                                                            $wallet_id = $details['wallet_id'];
                                                            $amount = $details['amount'];
                                                            // wallet check
    
                                                            $new_city_field = false;
    
                                                            $stmt = $con->prepare('SELECT * FROM wallets WHERE id=?');
                                                            $stmt->execute(array($wallet_id));
                                                            $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);  
                                                            if ($wallet) {
                                                                // field check
                                                                $stmt = $con->prepare('SELECT * FROM fields WHERE filed_ar_name=? OR filed_en_name=?');
                                                                $stmt->execute(array($service_field['field_name'],$service_field['field_name']));
                                                                $field = $stmt->fetch(\PDO::FETCH_ASSOC);  
                                                                if ($field) {
                                                                    $field_id = $service_field['id'];
                                                                    $new_city_field = false;
                                                                } else {
                                                                    // add new field with active == 0
                                                                    $field_data = array(
                                                                        'filed_ar_name'=>$service_field['field_name'],
                                                                        'filed_en_name'=>$service_field['field_name'],
                                                                        'active'=>'0'
                                                                    );
                                                                    $insert_request = $con->prepare(insert('fields', $field_data))->execute();
                                                                    if ($insert_request) {
                                                                        $conn = ("SELECT LAST_INSERT_ID()");
                                                                        $stmt = $con->prepare($conn);
                                                                        $stmt->execute();
                                                                        $field_id = $stmt->fetchColumn(); 
                                                                    }
                                                                    $new_city_field = true;
                                                                }
                                                                // city check
                                                                $stmt = $con->prepare('SELECT * FROM cities WHERE city_ar_name=? OR city_en_name=?');
                                                                $stmt->execute(array($service_city['city_name'],$service_city['city_name']));
                                                                $city = $stmt->fetch(\PDO::FETCH_ASSOC);  
                                                                if ($city) {
                                                                    $city_id = $city['id'];
                                                                    $new_city_field = false;
                                                                } else {
                                                                    // add new field with active == 0
                                                                    $city_data = array(
                                                                        'city_ar_name'=>$service_city['city_name'],
                                                                        'city_en_name'=>$service_city['city_name'],
                                                                        'active'=>'0'
                                                                    );
                                                                    $insert_request = $con->prepare(insert('cities', $city_data))->execute();
                                                                    if ($insert_request) {
                                                                        $conn = ("SELECT LAST_INSERT_ID()");
                                                                        $stmt = $con->prepare($conn);
                                                                        $stmt->execute();
                                                                        $city_id = $stmt->fetchColumn(); 
                                                                    }
                                                                    $new_city_field = true;
                                                                }
                                                                // upload image
                                                                // Create path of files if not found
                                                                if (!empty($details['service_image'])) {
                                                                    $service_image = $details['service_image'];
                                                                    if(!is_dir('../' . $imgs_dir)) {
                                                                        mkdir('../' . $imgs_dir); 
                                                                    }
                                                                    if(!is_dir('../' . $imgs_dir . 'services')) {
                                                                        mkdir('../' . $imgs_dir . 'services'); 
                                                                    }
                                                                    $extension = explode('/', mime_content_type($service_image))[1];
                                                                    $image_path = 'services/' .  time() . '.' . $extension;
                                                                    $full_path = '../' . $imgs_dir . $image_path;
        
                                                                    $exp = explode(',', $service_image);
                                                                    $data = base64_decode($exp[1]);
        
                                                                    if(file_exists($full_path)){
                                                                        $counter = 0;
                                                                        while(file_exists($full_path)) {
                                                                            $counter++;
                                                                            $image_path =  'services/' . time() . '_' . $counter . '.' . $extension;
                                                                            $full_path = '../' . $imgs_dir . $image_path;
                                                                        }
                                                                    }
                                                                    file_put_contents($full_path, $data);
                                                                } else {
                                                                    $image_path = 'services/no_image.png';
                                                                }
                                                                // save to database
                                                                if ($provider['confirmed_provider'] == '1' && !$new_city_field) {
                                                                    $approved = '1';
                                                                } else {
                                                                    $approved = '0';
                                                                }
                                                                
                                                                $service_data = array(
                                                                    'provider_id'=>$provider['id'],
                                                                    'service_name_ar'=>$service_name_ar,
                                                                    'service_name_en'=>$service_name_en,
                                                                    'service_image'=>$image_path,
                                                                    'description'=>$service_description,
                                                                    'city_id'=>$city_id,
                                                                    'field_id'=>$field_id,
                                                                    'creationTime'=>time(),
                                                                    'wallet_id'=>$wallet_id,
                                                                    'amount'=>$amount,
                                                                    'address'=>$service_address,
                                                                    'approved'=>$approved,
                                                                    'test_mode'=>getUserTestStatus($con, $user_id)
                                                                );
                                                                $insert_request = $con->prepare(insert('services', $service_data))->execute();
                                                                if ($insert_request) {
                                                                    $response['message'] = translate('service_added');
                                                                    $response['success'] = true;
                                                                }      
                                                            }  else {
                                                                $response['message'] = translate('service_wallet_id');
                                                                $response['success'] = false;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                
                            }
                        }
                    } else {
                        $response['message'] = translate('request_still_pending');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('not_authentication');
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