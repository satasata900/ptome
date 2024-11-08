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
        if (empty($details['email'])) {
            $response['message'] = translate('email_required');
            $response['success'] = false;
        } else {
            $email = $details['email'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = translate('valid_email');
                $response['success'] = false; 
            } else {
                $stmt = $con->prepare( "SELECT * FROM users WHERE email=? LIMIT 1");
                $stmt->execute(array($email));
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($user) {
                    if ($user['verified_email'] == '0') {
                        $maxq = 6;
                        for ($i = 1; $i <= $maxq; $i++) {
                            $ran_no = randomDigits($i);
                        }
                        $stmt=$con->prepare("UPDATE users SET email_verification_code=? WHERE id=? ");
                        $stmt->execute(array(sha1($ran_no),$user['id']));
                        $response['success'] = true;
                        $response['user_id'] = $user['id'];
                        $response['message'] = translate('verification_code_sent');
                        /****************************************************************************************/
                        //    Here the server will send an email containig the verification code to the user    //
                        /****************************************************************************************/
                        $headers = 'From:' . $server_email . "\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $subject = $app_name . ' | Email Verification';
                        $to      = $email;
                        $message = '<html><body style="direction:ltr;text-align: center;padding-top: 50px;">';
                        $message .= '<img src="' . $images_url . 'logo.png" height="90px" >';
                        $message .= '<h4>Hi ' . $user['full_name'] . ',</h4>';
                        $message .= '<p>To complete your sign up, please verify your email using the below code</p>';
                        $message .= '<p>Your verification code is : <span class="code" style="font-size: 1.4rem;font-weight: 700;color: #387dec;">' . $ran_no . '</span></p>';
                        $message .= '<p>Enter this code in our application to activate your account.</p>';
                        $message .= '<p>If you have any questions, send us an email</p>';
                        $message .= '<h4>Cheers,</h4>';
                        $message .= '<h2 style="color: #c93290;">' . $app_name . ' Team</h2>';
                        $message .= '</body></html>';
                        mail($to, $subject, $message, $headers);
                    } else {
                        $response['message'] = translate('email_already_verified');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('email_not_registered');
                    $response['success'] = false;
                }
            }
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 
                                                             
    
            


