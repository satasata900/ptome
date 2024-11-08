<?php

use \Firebase\JWT\JWT;


if (!empty($_SERVER['HTTP_TIME_ZONE'])) {
    $time_zone = $_SERVER['HTTP_TIME_ZONE'];
    date_default_timezone_set($time_zone);
}

if (empty($home)) {
    if (!empty($_SERVER['HTTP_LANG'])) {
        $lang = $_SERVER['HTTP_LANG'];
        include('../i18n/' . $lang . '.php');
    } else {
        $lang = $default_language;
        include('../i18n/' . $lang . '.php');
    }
} else {
    if (!empty($_SERVER['HTTP_LANG'])) {
        $lang = $_SERVER['HTTP_LANG'];
        include('i18n/' . $lang . '.php');
    } else {
        $lang = $default_language;
        include('i18n/' . $lang . '.php');
    }
}


function insert($table, $data)
{
    if (!empty($data) && is_array($data)) {
        $columns = '';
        $values  = '';
        $i = 0;
        foreach ($data as $key => $val) {
            $pre = ($i > 0) ? ', ' : '';
            $columns .= $pre . $key;
            $values  .= $pre . "'" . $val . "'";
            $i++;
        }
        $query = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
        return $query;
    } else {
        return false;
    }
}


function randomDigits($numDigits)
{
    if ($numDigits <= 0) {
        return '';
    }
    return mt_rand(0, 9) . randomDigits($numDigits - 1);
}

function createSecretKey()
{
    $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $secret_key = '';
    for ($i = 0; $i < 250; $i++) {
        $secret_key .= $alph[rand(0, 35)];
    }
    return $secret_key;
}

function createInvoiceToken()
{
    $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $secret_key = '';
    for ($i = 0; $i < 200; $i++) {
        $secret_key .= $alph[rand(0, 35)];
    }
    return $secret_key;
}

function getFullimageURL($images_url, $image)
{
    return $images_url . $image;
}


function convertDate($time)
{
    return date('d-m-Y', $time);
}

function convertFullDate($time)
{
    return date('d-m-Y H:i:s A', $time);
}


function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
/**
 * get access token from header
 * */
function getBearerToken()
{
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}


function createToken($user_id)
{
    $iat = time(); // time of token issued at
    $nbf = $iat + 1; //not before in seconds
    $exp = $iat + 126230400; // expire time of token in seconds
    $token = array(
        "iat" => $iat,
        "nbf" => $nbf,
        "exp" => $exp,
        "data" => array(
            "id" => $user_id
        )
    );
    $token = JWT::encode($token, SECRET_KEY);
    return $token;
}

function getToken($token)
{
    if (property_exists(JWT::class, 'leeway')) {
        JWT::$leeway = max(JWT::$leeway, 60);
    }
    try {
        $decoded = JWT::decode($token, SECRET_KEY, array(ALGORITHM));
        $decoded_array = (array) $decoded;
        $data = (array) $decoded_array['data'];
        return $data['id'];
    } catch (Exception $e) {
        return null;
    }
}


function ago($time)
{
    $periods = array("ثانية", "دقيقة", "ساعة", "يوم", "اسبوع", "شهر", "سنة", "عقد");
    $lengths = array("60","60","24","7","4.35","12","10");

    $now = time();
    $difference = $now - $time;

    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    return 'منذ ' . $difference . ' ' . $periods[$j];
}



function generateTransactionCode($con, $user_id)
{
    $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $transaction_code = '';
    for ($i = 0; $i < 15; $i++) {
        $transaction_code .= $alph[rand(0, 35)];
    }
    $sth = $con->prepare("SELECT * FROM transactions_codes WHERE transaction_code=?");
    $sth->execute(array($transaction_code));
    $code_present = $sth->fetch(\PDO::FETCH_ASSOC);
    if ($code_present) {
        generateTransactionCode($con, $user_id);
    } else {
        $sth = $con->prepare("SELECT * FROM transactions_codes WHERE user_id=? AND type=?");
        $sth->execute(array($user_id, '1'));
        $code_present = $sth->fetch(\PDO::FETCH_ASSOC);
        if ($code_present) {
            $stmt = $con->prepare("UPDATE transactions_codes SET transaction_code=? WHERE id=? ");
            $stmt->execute(array($transaction_code, $code_present['id']));
            return $transaction_code;
        } else {
            $data = array(
                'user_id' => $user_id,
                'transaction_code' => $transaction_code
            );
            $insert_request = $con->prepare(insert('transactions_codes', $data))->execute();
            if ($insert_request) {
                return $transaction_code;
            }
        }
    }
}



function generateUsernameBarcode($con, $user_id, $wallet_id, $money)
{
    $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $username_barcode = '';
    for ($i = 0; $i < 15; $i++) {
        $username_barcode .= $alph[rand(0, 35)];
    }
    $sth = $con->prepare("SELECT * FROM transactions_codes WHERE transaction_code=?");
    $sth->execute(array($username_barcode));
    $code_present = $sth->fetch(\PDO::FETCH_ASSOC);
    if ($code_present) {
        generateUsernameBarcode($con, $user_id, $wallet_id, $money);
    } else {
        $data = array(
            'user_id' => $user_id,
            'transaction_code' => $username_barcode,
            'wallet_id' => $wallet_id,
            'type' => '2',
            'money' => $money
        );
        $insert_request = $con->prepare(insert('transactions_codes', $data))->execute();
        if ($insert_request) {
            return $username_barcode;
        }
    }
}


function makeTransaction($con, $sender_id, $recipient_id, $amount, $wallet, $transaction_type, $transaction_code, $service_id, $trade_service_id, $type_details)
{
    $alph = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $transaction_number = '';
    for ($i = 0; $i < 12; $i++) {
        $transaction_number .= $alph[rand(0, 35)];
    }
    $creationTime = time();
    $data = array(
        'sender_id' => $sender_id,
        'recipient_id' => $recipient_id,
        'amount' => $amount,
        'wallet' => $wallet,
        'transaction_number' => $recipient_id . $transaction_number . $sender_id,
        'creationTime' => $creationTime,
        'test_mode' => getUserTestStatus($con, $sender_id)
    );
    if (!empty($transaction_type)) {
        $data['transaction_type'] = $transaction_type;
    }
    if (!empty($transaction_code)) {
        $data['transaction_code'] = $transaction_code;
    }
    if (!empty($service_id)) {
        $data['service_id'] = $service_id;
    }
    if (!empty($trade_service_id)) {
        $data['trade_service_id'] = $trade_service_id;
    }
    if (!empty($type_details)) {
        $data['type_details'] = $type_details;
    }
    $insert_request = $con->prepare(insert('transactions', $data))->execute();
    if ($insert_request) {
        $conn = ("SELECT LAST_INSERT_ID()");
        $stmt = $con->prepare($conn);
        $stmt->execute();
        $transaction_id = $stmt->fetchColumn();
        $res['transaction_number'] = $recipient_id . $transaction_number . $sender_id;
        $res['transaction_id'] = $transaction_id;
        $res['creationTime'] = $creationTime;
        return $res;
    }
}


function createWallet($con, $user_id, $wallet_id)
{
    $sth = $con->prepare("SELECT * FROM wallets WHERE id=?");
    $sth->execute(array($wallet_id));
    $wallet = $sth->fetch(\PDO::FETCH_ASSOC);
    $data = array(
        'user_id' => $user_id,
        'wallet_id' => $wallet_id,
        'price' => $wallet['price'],
        'test_mode'=>$wallet['test_wallet']
    );
    $response['sender_wallet'] = $data;
    $insert_request = $con->prepare(insert('users_wallets', $data))->execute();
    if ($insert_request) {
        $conn = ("SELECT LAST_INSERT_ID()");
        $stmt = $con->prepare($conn);
        $stmt->execute();
        $user_wallet_id = $stmt->fetchColumn();
        $sth = $con->prepare("SELECT * FROM users_wallets WHERE id=?");
        $sth->execute(array($user_wallet_id));
        $walletDetails = $sth->fetch(\PDO::FETCH_ASSOC);
        return $walletDetails;
    }
}




function getWallets($con, $user_id)
{
    // get trade wallets
    $stmt = $con->prepare("SELECT a.id, a.wallet_id, b.wallet_name, b.wallet_currency FROM users_wallets a INNER JOIN wallets b ON a.wallet_id = b.id WHERE a.user_id=? AND a.test_mode=?");
    $stmt->execute(array($user_id, getUserTestStatus($con, $user_id)));
    $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    return $wallets;
}




function makeNotification($con, $recipient_id, $sender_id, $body, $notification_id, $type, $test_mode)
{
    $data = array(
        'recipient_id' => $recipient_id,
        'sender_id' => $sender_id,
        'body' => $body,
        'notification_id' => $notification_id,
        'type' => $type,
        'test_mode' => $test_mode,
        'creationTime' => time()
    );
    $insert_request = $con->prepare(insert('notifications', $data))->execute();
    if ($insert_request) {
        pushNotification($con, $recipient_id, $sender_id, $type, $body, $notification_id);
        return true;
    }
}


function pushNotification($con, $recipient_id, $sender_id, $type, $body, $notification_id)
{
    // check if user turnned on notifications
    $stmt = $con->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute(array($recipient_id));
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($user && $user['notifications_on_off'] == '1') {
        // get firebase_key
        $stmt = $con->prepare("SELECT * FROM app_settings WHERE name=?");
        $stmt->execute(array('firebase_key'));
        $firebase_key = $stmt->fetch(\PDO::FETCH_ASSOC);
        // get user tokens
        $stmt = $con->prepare("SELECT * FROM tokens WHERE user_id=?");
        $stmt->execute(array($recipient_id));
        $tokens = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($type == '1') {
            // mean service
            $stmt = $con->prepare("SELECT * FROM services WHERE id=?");
            $stmt->execute(array($notification_id));
            $service = $stmt->fetch(\PDO::FETCH_ASSOC);
            $title = $service['service_name_en'];
            if ($body == 'service_member_accept') {
                $msg_body = translate($body);
            } else if ($body == 'service_pay') {
                $stmt = $con->prepare("SELECT * FROM users WHERE id=?");
                $stmt->execute(array($sender_id));
                $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
                $msg_body = $sender['user_name'] . ' ' . translate($body);
            }
        } else if ($type == '2') {
            // mean money recieved
            $stmt = $con->prepare("SELECT * FROM users WHERE id=?");
            $stmt->execute(array($sender_id));
            $sender = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt = $con->prepare("SELECT a.amount, b.wallet_currency FROM transactions a INNER JOIN wallets b ON a.wallet = b.id WHERE a.id=?");
            $stmt->execute(array($notification_id));
            $transaction = $stmt->fetch(\PDO::FETCH_ASSOC);
            $title = translate('money_recieved');
            $msg_body = translate($body) . ' ' . $transaction['amount'] . $transaction['wallet_currency'] . ' ' . translate('from') . ' ' . $sender['user_name'] . ' ';
        }
        for ($i = 0; $i < count($tokens); $i++) {
            $data = '{"notification":{"title":"' . $title . '", "body": "' . $msg_body . '"' . ', "click_action": "FCM_PLUGIN_ACTIVITY"' . '}';
            $data .= ',"data":{"title":"' . $title . '", "body": "' . $msg_body . '",}';
            $data .= ',"registration_ids":["' . $tokens[$i]['platform_token'] . '"]';
            $data .= '}';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: ' . $firebase_key['body']
                ),
            ));
            curl_exec($curl);
            curl_close($curl);
        }
    } else {
        return true;
    }
}
