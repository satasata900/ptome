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
        if (!empty($details['invoice_token'])) {
            $invoice_token = $details['invoice_token'];
            $user_id = $details['user_id'];
            $stmt = $con->prepare( "SELECT a.*, b.user_id AS organization_user_id FROM organizations_invoices a INNER JOIN organizations b ON a.organization_id = b.id WHERE a.invoice_token=?");
            $stmt->execute(array($invoice_token));
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($invoice) {
                if ($invoice['captured']) {
                    $response['message'] = translate('invalid_invoice');
                    $response['success'] = false;
                } else {
                    // get recipient details
                    $sth= $con->prepare("SELECT * FROM users WHERE id=?");
                    $sth->execute(array($invoice['organization_user_id']));
                    $recipientDetails = $sth->fetch(\PDO::FETCH_ASSOC);

                    // get recipient wallet details
                    $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                    $sth->execute(array($invoice['organization_user_id'],$invoice['wallet_id']));
                    $recipient_wallet = $sth->fetch(\PDO::FETCH_ASSOC);

                    // get sender wallet details
                    $sth= $con->prepare("SELECT * FROM users_wallets WHERE user_id=? AND wallet_id=?");
                    $sth->execute(array($user_id,$invoice['wallet_id']));
                    $sender_wallet = $sth->fetch(\PDO::FETCH_ASSOC);

                    // get sender details
                    $sth= $con->prepare("SELECT * FROM users WHERE id=?");
                    $sth->execute(array($user_id));
                    $senderDetails = $sth->fetch(\PDO::FETCH_ASSOC);

                    if ($sender_wallet && $recipient_wallet) {
                        if ($recipient_wallet['user_id'] == $sender_wallet['user_id']) {
                            $response['message'] = translate('no_transfere_to_yourself');
                            $response['success'] = false;
                        } else {
                            if ($sender_wallet['price'] >= $invoice['amount']) {
                                try {
                                    $con->beginTransaction();
                                    // update sender price
                                    $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                    $stmt->execute(array($sender_wallet['price'] - $invoice['amount'],$sender_wallet['id']));
                                    // update recipient price
                                    $stmt=$con->prepare("UPDATE users_wallets SET price=? WHERE id=? ");
                                    $stmt->execute(array($recipient_wallet['price'] + $invoice['amount'],$recipient_wallet['id']));
                                    // add transaction
                                    $transaction = makeTransaction($con, $sender_wallet['user_id'], $recipient_wallet['user_id'], $invoice['amount'], $invoice['wallet_id'], 'invoice', NULL, NULL, NULL, json_encode($invoice), $invoice['test_mode']);
                                    // send notification
                                    // makeNotification($con, $recipient_wallet['user_id'], $user_id, 'money_transfer', $transaction['transaction_id'], '2', $invoice['test_mode']);
                                    // update invoice
                                    $stmt=$con->prepare("UPDATE organizations_invoices SET success=?, status=?, captured_at=?, captured=?, user_id=? WHERE id=? ");
                                    $stmt->execute(array('1', '2', time(),'1',$sender_wallet['user_id'],$invoice['id']));
                                    $con->commit();
                                    $response['transaction_id'] = $transaction['transaction_id'];
                                    $response['recipient_id'] = $recipient_wallet['user_id'];
                                    $response['message'] = translate('successfull_transfere');
                                    $response['redirection_url'] = $invoice['redirection_url'];
                                    $response['transaction_number'] = $transaction['transaction_number'];
                                    $response['invoice_token'] = $invoice['invoice_token'];
                                    $response['success'] = true;
                                } catch (Exception $e) {
                                    $con->rollback();
                                    $response['message'] = translate('try_again');
                                    $response['success'] = false;
                                }
                            } else {
                                $response['message'] = translate('not_have_enough_money');
                                $response['success'] = false;
                            }
                        }
                    } else {
                        $response['message'] = translate('try_again');
                        $response['success'] = false;  
                    }
                }
            } else {
                
                $response['message'] = translate('not_access_file');
                $response['success'] = false;
            }
        } else {
            $response['message'] = translate('not_access_file');
            $response['success'] = false;
        }
    } else {
        $response['message'] = translate('not_access_file');
        $response['success'] = false;
    }
    echo json_encode($response); 