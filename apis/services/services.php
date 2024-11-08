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
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);

            // get fields
            if ($lang == 'ar') {
                $query = "SELECT id, filed_ar_name AS field_name FROM fields WHERE active='1' ORDER BY filed_ar_name";
            } else if ($lang == 'en') {
                $query = "SELECT id, filed_en_name AS field_name FROM fields WHERE active='1' ORDER BY filed_en_name";
            }
            $stmt = $con->prepare($query);
            $stmt->execute();
            $fields = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $response['data']['fields'] = $fields;

            // get provider details
            $stmt = $con->prepare('SELECT * FROM providers WHERE user_id=?');
            $stmt->execute(array($user_id));
            $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($provider) {
                $provider['provider_image'] = $images_url . $provider['provider_image'];
                $provider['registeredAt'] = convertDate($provider['registeredAt']);
                if ($provider['approved_provider'] == '1') {
                    $stmt = $con->prepare('SELECT * FROM services WHERE provider_id=? AND test_mode=?');
                    $stmt->execute(array($provider['id'], getUserTestStatus($con, $provider['user_id'])));
                    $provider_services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($provider_services); $i++) {
                        $provider_services[$i]['provider_name'] = $provider['provider_name'];
                        $provider_services[$i]['service_image'] = $images_url . $provider_services[$i]['service_image'];
                        $provider_services[$i]['creationTime'] = convertDate($provider_services[$i]['creationTime']);
                        if ($lang == 'ar') {
                            $provider_services[$i]['service_name'] = $provider_services[$i]['service_name_ar'];
                            $field_query = 'SELECT id, filed_ar_name AS field_name FROM fields WHERE id=?';
                            $city_query = 'SELECT id, city_ar_name AS city_name FROM cities WHERE id=?';
                        } else if ($lang == 'en') {
                            $provider_services[$i]['service_name'] = $provider_services[$i]['service_name_en'];
                            $field_query = 'SELECT id, filed_en_name AS field_name FROM fields WHERE id=?';
                            $city_query = 'SELECT id, city_en_name AS city_name FROM cities WHERE id=?';
                        }
                        unset($provider_services[$i]['service_name_ar']);
                        unset($provider_services[$i]['service_name_en']);
                        $stmt = $con->prepare($city_query);
                        $stmt->execute(array($provider_services[$i]['city_id']));
                        $provider_services[$i]['city'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                        unset($provider_services[$i]['city_id']);
                        $stmt = $con->prepare($field_query);
                        $stmt->execute(array($provider_services[$i]['field_id']));
                        $provider_services[$i]['field'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                        unset($provider_services[$i]['field_id']);
                        $stmt = $con->prepare('SELECT * FROM wallets WHERE id=?');
                        $stmt->execute(array($provider_services[$i]['wallet_id']));
                        $provider_services[$i]['wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                        unset($provider_services[$i]['wallet_id']);
                    }
                    $provider['services'] = $provider_services;
                }
            }
            $response['data']['provider'] = $provider;
            // get my subscriptions
            $stmt = $con->prepare('SELECT b.* FROM service_members a INNER JOIN services b ON a.service_id = b.id WHERE a.member_id=? AND b.approved="1" AND b.test_mode=?');
            $stmt->execute(array($user_id, getUserTestStatus($con, $user_id)));
            $myservices = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            for ($i = 0; $i < count($myservices); $i++) {
                $myservices[$i]['service_image'] = $images_url . $myservices[$i]['service_image'];
                $myservices[$i]['creationTime'] = convertDate($myservices[$i]['creationTime']);
                // get provider details
                $stmt = $con->prepare('SELECT provider_name FROM providers WHERE id=?');
                $stmt->execute(array($myservices[$i]['provider_id']));
                $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                $myservices[$i]['provider_name'] = $provider['provider_name'];
                if ($lang == 'ar') {
                    $myservices[$i]['service_name'] = $myservices[$i]['service_name_ar'];
                    $field_query = 'SELECT id, filed_ar_name AS field_name FROM fields WHERE id=?';
                    $city_query = 'SELECT id, city_ar_name AS city_name FROM cities WHERE id=?';
                } else if ($lang == 'en') {
                    $myservices[$i]['service_name'] = $myservices[$i]['service_name_en'];
                    $field_query = 'SELECT id, filed_en_name AS field_name FROM fields WHERE id=?';
                    $city_query = 'SELECT id, city_en_name AS city_name FROM cities WHERE id=?';
                }
                unset($myservices[$i]['service_name_ar']);
                unset($myservices[$i]['service_name_en']);
                $stmt = $con->prepare($city_query);
                $stmt->execute(array($myservices[$i]['city_id']));
                $myservices[$i]['city'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                unset($myservices[$i]['city_id']);
                $stmt = $con->prepare($field_query);
                $stmt->execute(array($myservices[$i]['field_id']));
                $myservices[$i]['field'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                unset($myservices[$i]['field_id']);
                $stmt = $con->prepare('SELECT * FROM wallets WHERE id=?');
                $stmt->execute(array($myservices[$i]['wallet_id']));
                $myservices[$i]['wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                unset($myservices[$i]['wallet_id']);
            }
            $response['data']['myservices'] = $myservices;
            $response['success'] = true;
        } else {
            $response['message'] = translate('not_authentication');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 