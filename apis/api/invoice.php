<?php
    $_SERVER['HTTP_LANG'] = 'en';
    include('../connect_database.php');
    $response = array();
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
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
        

        if (empty($details['id'])) {
            $response['message'] = translate('invoice_required');
            $response['success'] = false;
        } else {
            $invoice_id = $details['id'];
            //get invoice
            $stmt = $con->prepare( "SELECT * FROM organizations_invoices WHERE id=? OR invoice_token=?");
            $stmt->execute(array($invoice_id,$invoice_id));
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($invoice) {
                $stmt = $con->prepare( "SELECT * FROM wallets WHERE id=?");
                $stmt->execute(array($invoice['wallet_id']));
                $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                $invoice['currency'] = $wallet['wallet_name'];
                $invoice['amount'] = $invoice['amount'] . $wallet['wallet_currency'];
                unset($invoice['wallet_id']);
                $invoice['created_at'] = date('d-m-Y H:i', $invoice['created_at']);
                if ($invoice['captured_at']) {
                    $invoice['captured_at'] = date('d-m-Y H:i', $invoice['captured_at']);
                } else {
                    unset($invoice['captured_at']);
                }
                if ($invoice['captured'] == '1') {
                    $invoice['captured'] = true;
                } else {
                    $invoice['captured'] = false;
                }
                if ($invoice['success'] == '1') {
                    $invoice['success'] = true;
                } else {
                    $invoice['success'] = false;
                }
                if ($invoice['status'] == '0') {
                    $invoice['status'] = translate('pending_invoice');
                } else if ($invoice['status'] == '1') {
                    $invoice['status'] = translate('declined_invoice');
                } else if ($invoice['status'] == '2') {
                    $invoice['status'] = translate('captured_invoice');
                }
                unset($invoice['invoice_token']);
                unset($invoice['user_id']);
                unset($invoice['redirection_url']);
                $stmt = $con->prepare( "SELECT organization_name FROM organizations WHERE id=?");
                $stmt->execute(array($invoice['organization_id']));
                $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
                $invoice['organization'] = $organization['organization_name'];
                unset($invoice['organization_id']);
                $response['invoice'] = $invoice;
                // $response['success'] = true;  
            } else {
                $response['message'] = translate('invoice_required');
                $response['success'] = false;
            }
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response);
?>