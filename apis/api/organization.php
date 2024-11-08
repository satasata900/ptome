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
        $user_id;
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $user_id = getToken($_SERVER['HTTP_TOKEN']);
        } 
        if (empty($user_id)) {
            $response['message'] = translate('no_user');
            $response['success'] = false;
        } else {
            //get organization
            $stmt = $con->prepare( "SELECT * FROM organizations WHERE user_id=?");
            $stmt->execute(array($user_id));
            $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($organization) {
                $organization['creationTime'] = date('d-m-Y H:i', $organization['creationTime']);
                if ($lang == 'ar') {
                    $query = "SELECT id, country_ar As country FROM countries WHERE id=?";
                } else if ($lang == 'en') {
                    $query = "SELECT id, country_en As country FROM countries WHERE id=?";
                }
                $stmt = $con->prepare($query);
                $stmt->execute(array($organization['country']));
                $country = $stmt->fetch(\PDO::FETCH_ASSOC);
                $organization['country'] = $country;
                $response['organization'] = $organization;
                $stmt = $con->prepare( "SELECT id,wallet_name AS wallet, wallet_currency FROM wallets");
                $stmt->execute();
                $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $response['wallets'] = $wallets;
                $response['success'] = true;  
            } else {
                $response['organization'] = $organization;
                $stmt = $con->prepare( "SELECT * FROM countries");
                $stmt->execute();
                $countries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                for ($i = 0; $i < count($countries); $i++) {
                    if ($lang == 'ar' && $countries[$i]['country_ar']) {
                        $countries[$i]['country'] = $countries[$i]['country_ar'];
                    } else {
                        $countries[$i]['country'] = $countries[$i]['country_en'];
                    }
                    unset($countries[$i]['country_ar']);
                    unset($countries[$i]['country_en']);
                }
                usort($countries, function ($item1, $item2) {
                    if ($item1['country'] == $item2['country']) return 0;
                    return $item1['country'] < $item2['country'] ? -1 : 1;
                });
                $response['countries'] = $countries;
                $response['success'] = true;  
            }
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 


  