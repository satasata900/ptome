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
                if (!empty($details['group_id'])) {
                    $group_id = $details['group_id'];
                    if (!empty($details['member_username'])) {
                        $member_username = $details['member_username'];
                        if (!empty($details['member_nick_name'])) {
                            $member_nick_name = $details['member_nick_name'];
                            $sth= $con->prepare("SELECT * FROM user_groups WHERE id=? AND user_id=?");
                            $sth->execute(array($group_id, $user_id));
                            $group_present = $sth->fetch(\PDO::FETCH_ASSOC);
                            if ($group_present) {
                                $sth= $con->prepare("SELECT * FROM users WHERE user_name=?");
                                $sth->execute(array($member_username));
                                $user = $sth->fetch(\PDO::FETCH_ASSOC);
                                if ($user) {
                                    if ($user['id'] == $user_id) {
                                        $response['message'] = translate('not_add_yourself');
                                        $response['success'] = false; 
                                    } else {
                                        $sth = $con->prepare("SELECT a.*, b.user_name FROM group_members a INNER JOIN users b ON a.member_id = b.id WHERE a.group_id=? AND a.member_id=?");
                                        $sth->execute(array($group_id,$user['id']));
                                        $member_found = $sth->fetch(\PDO::FETCH_ASSOC);
                                        if ($member_found) {
                                            $response['message'] = $member_found['user_name'] . ' ' . translate('member_already_present');
                                            $response['success'] = false;  
                                        } else {
                                            $data = array(
                                                'group_id'=>$group_id,
                                                'member_id'=>$user['id'],
                                                'member_nick_name'=>$member_nick_name
                                            );
                                            $insert_request = $con->prepare(insert('group_members', $data))->execute();
                                            if ($insert_request) {
                                                $response['message'] = translate('member_added');
                                                $response['success'] = true;
                                            }
                                        }
                                    }
                                } else {
                                    $response['message'] = translate('no_user');
                                    $response['success'] = false;  
                                }
                            } else {
                                $response['message'] = translate('group_required');
                                $response['success'] = false; 
                            }
                        } else {
                            $response['message'] = translate('member_nick_name_required');
                            $response['success'] = false;
                        }  
                    } else {
                        $response['message'] = translate('username_required');
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