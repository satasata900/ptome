<?php


function getUserDetails($con, $user_id, $images_url)
{
    $stmt = $con->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute(array($user_id));
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($user) {
        unset($user['password']);
        unset($user['password_verification_code']);
        unset($user['pin_code']);
        unset($user['pass_reset_code']);
        unset($user['created_at']);
        unset($user['deleted_at']);
        unset($user['updated_at']);
        unset($user['email_verification_code']);
        if ($user['active'] == '1') {
            $user['active'] = true;
        } else {
            $user['active'] = false;
        }
        if ($user['notifications_on_off'] == '1') {
            $user['notifications_on_off'] = true;
        } else {
            $user['notifications_on_off'] = false;
        }
        if ($user['pincode_require'] == '1') {
            $user['pincode_require'] = true;
        } else {
            $user['pincode_require'] = false;
        }
        if ($user['verified_email'] == '1') {
            $user['verified_email'] = true;
        } else {
            $user['verified_email'] = false;
        }
        if ($user['verified_phone'] == '1') {
            $user['verified_phone'] = true;
        } else {
            $user['verified_phone'] = false;
        }
        if ($user['test_mode'] == '1') {
            $user['test_mode'] = true;
        } else {
            $user['test_mode'] = false;
        }
        $user['picture'] = $images_url . $user['picture'];
        $user['registration_Time'] = convertFullDate($user['registration_Time']);
    }
    return  $user;
}



function getUserTestStatus($con, $user_id)
{
    $stmt = $con->prepare("SELECT test_mode FROM users WHERE id=?");
    $stmt->execute(array($user_id));
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($user['test_mode'] == '0') {
        return  false;
    } else if ($user['test_mode'] == '1') {
         return  true;
    }
}



function getUserActiveStatus($con, $user_id)
{
    $stmt = $con->prepare("SELECT active FROM users WHERE id=?");
    $stmt->execute(array($user_id));
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($user['active'] == '0') {
        return  true;
    } else if ($user['active'] == '1') {
         return  false;
    }
}