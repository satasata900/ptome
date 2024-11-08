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
        if (empty($details['search'])) {
            $search = '';
        } else {
            $search = $details['search'];
        }
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $token = $_SERVER['HTTP_TOKEN'];
            $user_id = getToken($token);
            if (empty($details['field_id'])) {
                $response['message'] = translate('field_required');
                $response['success'] = false;
            } else {
                $field_id = $details['field_id'];
                $stmt = $con->prepare('SELECT * FROM fields WHERE id=?');
                $stmt->execute(array($field_id));
                $field = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($field) {

                    // Find out how many items are in the table
                    $stmt = $con->prepare( "SELECT COUNT(*) FROM services WHERE field_id=? AND active='1' AND test_mode='" . getUserTestStatus($con, $user_id) . "'");
                    $stmt->execute(array($field_id));
                    $total = $stmt->fetchColumn();
                    // How many pages will there be
                    $pages = ceil($total / $items_per_page);
                    // What page are we currently on?
                    if (empty($details['page'])) {
                        $page = 1;
                    } else {
                        $page = $details['page'];
                    }
                    // Calculate the offset for the query
                    $offset = ($page - 1)  * $items_per_page;
                    // Some information to display to the user
                    $start = $offset + 1;
                    $end = min(($offset + $items_per_page), $total);
                    $stmt = $con->prepare("SELECT * FROM services WHERE field_id=? AND active='1' AND approved='1' AND test_mode=? AND (service_name_ar LIKE '%".$search."%' OR service_name_en LIKE '%".$search."%') LIMIT $items_per_page OFFSET $offset");
                    $stmt->execute(array($field_id, getUserTestStatus($con, $user_id)));
                    $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    for ($i = 0; $i < count($services); $i++) {
                        $services[$i]['service_image'] = $images_url . $services[$i]['service_image'];
                        $services[$i]['creationTime'] = convertDate($services[$i]['creationTime']);
                        if ($lang == 'ar') {
                            $services[$i]['service_name'] = $services[$i]['service_name_ar'];
                            $field_query = 'SELECT id, filed_ar_name AS field_name FROM fields WHERE id=?';
                            $city_query = 'SELECT id, city_ar_name AS city_name FROM cities WHERE id=?';
                        } else if ($lang == 'en') {
                            $services[$i]['service_name'] = $services[$i]['service_name_en'];
                            $field_query = 'SELECT id, filed_en_name AS field_name FROM fields WHERE id=?';
                            $city_query = 'SELECT id, city_en_name AS city_name FROM cities WHERE id=?';
                        }
                        // get provider details
                        $stmt = $con->prepare('SELECT provider_name FROM providers WHERE id=?');
                        $stmt->execute(array($services[$i]['provider_id']));
                        $provider = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $services[$i]['provider_name'] = $provider['provider_name'];
                        unset($services[$i]['service_name_ar']);
                        unset($services[$i]['service_name_en']);
                        $stmt = $con->prepare($city_query);
                        $stmt->execute(array($services[$i]['city_id']));
                        $services[$i]['city'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                        unset($services[$i]['city_id']);
                        $stmt = $con->prepare($field_query);
                        $stmt->execute(array($services[$i]['field_id']));
                        $services[$i]['field'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                        unset($services[$i]['field_id']);
                        $stmt = $con->prepare('SELECT * FROM wallets WHERE id=?');
                        $stmt->execute(array($services[$i]['wallet_id']));
                        $services[$i]['wallet'] = $stmt->fetch(\PDO::FETCH_ASSOC);
                        unset($services[$i]['wallet_id']);
                    }
                    $response['data'] = $services;
                    $response['success'] = true;
                    $response['total'] = (int) $total;
                    $response['pages_no'] = $pages;
                    $response['current_page'] = (int) $page;
                    $response['items_per_page'] = (int) $items_per_page;
                } else {
                    $response['message'] = translate('field_required');
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