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
                if (!empty($details['id'])) {
                    $member_id = $details['id'];
                    if (!empty($details['member_nick_name'])) {
                        $member_nick_name = $details['member_nick_name'];
                        if (!empty($details['group_id'])) {
                            $group_id = $details['group_id'];
                            $sth= $con->prepare("SELECT * FROM group_members WHERE id=? AND group_id=?");
                            $sth->execute(array($member_id, $group_id));
                            $member_present = $sth->fetch(\PDO::FETCH_ASSOC);
                            if ($member_present) {
                                $stmt=$con->prepare("UPDATE group_members SET member_nick_name=? WHERE id=? ");
                                $stmt->execute(array($member_nick_name,$member_id));
                                $response['message'] = translate('member_edited');
                                $response['success'] = true;     
                            } else {
                                $response['message'] = translate('group_member_required');
                                $response['success'] = false;
                            }
                        } else {
                            $response['message'] = translate('group_required');
                            $response['success'] = false;
                        }
                    } else {
                        $response['message'] = translate('group_name_required');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('group_required');
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