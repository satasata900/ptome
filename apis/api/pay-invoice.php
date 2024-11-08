<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <link rel="stylesheet" href="templates/pay-invoice.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>

        <?php
            $html_content = true;
            $_SERVER['HTTP_LANG'] = 'en';
            session_start(); 
            $_SESSION['previous_location'] = 'homepage';
            include('../connect_database.php');
            // Checking for a POST request 

                if (empty($_GET['id'])) {
                    // no invoice id in get
                    $no_invoice = true;
                } else {
                    $email = '';
                    $password = '';
                    $msg = '';
                    $success = false;
                    $logged_in = false;  
                    $paid_invoice = false;  
                    $invoice_id = $_GET['id'];
                    //get invoice
                    $stmt = $con->prepare( "SELECT * FROM organizations_invoices WHERE invoice_token=?");
                    $stmt->execute(array($invoice_id));
                    $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($invoice) {
                        $invoice['created_at'] = convertDate($invoice['created_at']);
                        if ($invoice['captured_at']) {
                            $invoice['captured_at'] = convertDate($invoice['captured_at']);
                        } else {
                            unset($invoice['captured_at']);
                        }
                        if ($invoice['success'] == '1') {
                            $invoice['success'] = true;
                        } else {
                            $invoice['success'] = false;
                        }
                        if ($invoice['captured'] == '1') {
                            $invoice['captured'] = true;
                        } else {
                            $invoice['captured'] = false;
                        }
                        if ($invoice['status'] == '0') {
                            $invoice['status'] = translate('pending_invoice');
                        } else if ($invoice['status'] == '1') {
                            $invoice['status'] = translate('declined_invoice');
                        } else if ($invoice['status'] == '2') {
                            $invoice['status'] = translate('captured_invoice');
                        }
                        // get wallet
                        $invoice['money'] = $invoice['amount'];
                        $stmt = $con->prepare( "SELECT * FROM wallets WHERE id=?");
                        $stmt->execute(array($invoice['wallet_id']));
                        $wallet = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $invoice['currency'] = $wallet['wallet_name'];
                        $invoice['amount'] = $invoice['amount'] . $wallet['wallet_currency'];
                        $invoice['wallet'] = $wallet['id'];
                        unset($invoice['wallet_id']);
                        // get organization
                        $stmt = $con->prepare( "SELECT a.*, b.* FROM organizations a INNER JOIN users b ON a.user_id = b.id WHERE a.id=?");
                        $stmt->execute(array($invoice['organization_id']));
                        $organization = $stmt->fetch(\PDO::FETCH_ASSOC);
                        $invoice['organization'] = $organization['organization_name'];
                        $invoice['username'] = $organization['user_name'];
                        unset($invoice['organization_id']);
                        $no_invoice = false;
                        if ($invoice['captured']) {
                            // invoice has been paid
                            $paid_invoice = true;
                        } else {
                            // user has to login
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                if (empty($_POST['email'])) {
                                    $msg = translate('email_required');
                                    $success = false;
                                    $logged_in = false;
                                } else {
                                    $email = $_POST['email'];
                                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $msg = translate('valid_email');
                                        $success = false;
                                        $logged_in = false;
                                    } else {
                                        if (empty($_POST['password'])) {
                                            $msg = translate('password_required');
                                            $success = false;
                                            $logged_in = false;
                                        } else {
                                            $password = $_POST['password'];
                                            $uppercase = preg_match('@[A-Z]@', $password);
                                            $lowercase = preg_match('@[a-z]@', $password);
                                            $number    = preg_match('@[0-9]@', $password);
                                            $specialChars = preg_match('@[^\w]@', $password);
                                            if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
                                                $msg = translate('password_valid');
                                                $success = false;
                                                $logged_in = false;
                                            } else {
                                                $stmt = $con->prepare( "SELECT * FROM users WHERE email=? LIMIT 1");
                                                $stmt->execute(array($email));
                                                $current_user = $stmt->fetch(\PDO::FETCH_ASSOC);
                                                if ($current_user) {
                                                    if ($current_user['password'] != sha1($password)) {
                                                        $msg = translate('password_is_incorrect');
                                                        $success = false;
                                                        $logged_in = false;
                                                    } else {
                                                        if ($current_user['verified_email'] == '0' || empty($current_user['verified_email'])) {
                                                            $msg = translate('email_not_verified');
                                                            $success = false;
                                                            $logged_in = false;
                                                        } else {
                                                            $msg = translate('successfull_login');
                                                            $success = true;
                                                            $logged_in = true;
                                                        }
                                                    }
                                                } else {
                                                    $msg = translate('email_not_registered');
                                                    $success = false;
                                                    $logged_in = false;
                                                }
                                            }
                                        }
                                    }
                                }      
                            }
                        }
                    } else {
                        // no invoice found with this id
                        $no_invoice = true;
                    }
                }
        ?>

        <?php 
            if (!$no_invoice) {
                if (!$paid_invoice) {
                    if ($logged_in && $invoice) {
                        include 'templates/invoice-pay.php';
                    } else {
                        include 'templates/invoice-login.php';
                    }
                } else {
                    $msg = translate('invoice_paid');
                    include 'templates/404.php';
                }
            } else {
                $msg = translate('invoice_required');
                include 'templates/404.php';
            }
         ?>

    </body>
</html>