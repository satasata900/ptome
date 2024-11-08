<?php
    include('../connect_database.php');
    $response = array();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
        if (!empty($_SERVER['HTTP_SECRET_KEY'])) {
            $secret_key = $_SERVER['HTTP_SECRET_KEY'];
            if (!empty($details['amount'])) {
                $amount = $details['amount'];
                if (!empty($details['currency'])) {
                    $currency = $details['currency'];
                    if (!empty($details['redirection_url'])) {
                        $redirection_url = $details['redirection_url'];
                        if (filter_var($redirection_url, FILTER_VALIDATE_URL)) {
                            //get organization
                            $stmt = $con->prepare( "SELECT * FROM wallets WHERE wallet_name=?");
                            $stmt->execute(array($currency));
                            $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if ($wallet) {
                                //get organization
                                $stmt = $con->prepare( "SELECT * FROM organizations WHERE secret_key=?");
                                $stmt->execute(array($secret_key));
                                $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
                                if ($organization) {
                                    $invoice_token = createInvoiceToken();
                                    $data = array(
                                        'organization_id'=>$organization['id'],
                                        'wallet_id'=>$wallet['id'],
                                        'amount'=>$amount,
                                        'redirection_url'=>$redirection_url,
                                        'created_at'=>time(),
                                        'invoice_token'=> $invoice_token
                                    );
                                    $insert_request = $con->prepare(insert('organizations_invoices', $data))->execute();
                                    if ($insert_request) {
                                        $conn = ("SELECT LAST_INSERT_ID()");
                                        $stmt = $con->prepare($conn);
                                        $stmt->execute();
                                        $invoice_id = $stmt->fetchColumn(); 
                                        $response['invoice_link'] = $server_url . 'api/pay-invoice?id=' . $invoice_token;
                                        $response['message'] = translate('invoice_created');
                                        $response['success'] = true; 
                                    }
                                } else {
                                    $response['message'] = translate('wrong_secret_key');
                                    $response['success'] = false;
                                }
                            } else {
                                $response['message'] = translate('wrong_currency');
                                $response['success'] = false;
                            }
                        } else {
                            $response['message'] = translate('wrong_redirection_url');
                            $response['success'] = false;
                        }
                    } else {
                        $response['message'] = translate('redirection_url_required');
                        $response['success'] = false;
                    }
                } else {
                    $response['message'] = translate('currency_required');
                    $response['success'] = false;
                }
            } else {
                $response['message'] = translate('exchanged_amount_required');
                $response['success'] = false;
            }
        } else {
            $response['message'] = translate('no_secret_key');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('method_not_support');
        $response['success'] = false;
    }
    echo json_encode($response); 