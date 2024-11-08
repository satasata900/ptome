<?php
include('../connect_database.php');
$response = array();
if (!empty($_SERVER['HTTP_API_TOKEN']) && $_SERVER['HTTP_API_TOKEN'] == $api_token && $_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_SERVER['HTTP_TOKEN'])) {
        // $stmt = $con->prepare("SELECT id,wallet_name, wallet_currency FROM wallets");
        // $stmt->execute();
        $token = $_SERVER['HTTP_TOKEN'];
        $user_id = getToken($token);
        $stmt = $con->prepare("SELECT id,wallet_name, wallet_currency FROM wallets WHERE test_wallet=?");
        $stmt->execute(array(getUserTestStatus($con, $user_id)));
        $wallets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $response['wallets'] = $wallets;
    } else {
        $response['message'] = translate('not_authentication');
        $response['success'] = false;
    }
} else {
    $response['message'] = translate('not_access_file');
    $response['success'] = false;
}
echo json_encode($response);
