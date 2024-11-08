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
        $user_id;
        if (!empty($_SERVER['HTTP_TOKEN'])) {
            $user_id = getToken($_SERVER['HTTP_TOKEN']);
        } 
        if (empty($user_id)) {
            $response['message'] = translate('no_user');
            $response['success'] = false;
        } else {
            if (empty($details['organization_name'])) {
                $response['message'] = translate('organization_name_required');
                $response['success'] = false;
            } else {
                $organization_name = $details['organization_name'];
                if (empty($details['country'])) {
                    $response['message'] = translate('country_required');
                    $response['success'] = false;
                } else {
                    $country_id = $details['country'];
                    $stmt = $con->prepare( "SELECT * FROM countries WHERE id=?");
                    $stmt->execute(array($country_id));
                    $country = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($country) {
                        $stmt = $con->prepare( "SELECT * FROM organizations WHERE user_id=?");
                        $stmt->execute(array($user_id));
                        $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if ($organization) {
                            $response['message'] = translate('already_organizer');
                            $response['success'] = false; 
                        } else {
                            $data = array(
                                'user_id'=>$user_id,
                                'organization_name'=>$organization_name,
                                'country'=>$country['id'],
                                'secret_key'=>$user_id . createSecretKey(),
                                'creationTime'=>time()
                            );
                            $insert_request = $con->prepare(insert('organizations', $data))->execute();
                            if ($insert_request) {
                                $response['message'] = translate('organizer_sent');
                                $response['success'] = true; 
                            }
                        }
                    } else {
                        $response['message'] = translate('country_required');
                        $response['success'] = false; 
                    }
                } 
            }  
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 

