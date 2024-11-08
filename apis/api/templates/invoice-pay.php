
<!-- invoice start -->
<div class="invoice">

    <!-- header start -->
    <div class="header">
        <div class="logo">
            <img src="<?php echo $images_url . 'logo.png' ?>">
            <h2 class="site-name"><?php echo $app_name ?></h2>
        </div>
    </div>
    <!-- header ends -->



    <!-- invoice details start -->
    <div class="invoice-details">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <h2>Billed To</h2>
                <p><?php echo $invoice['organization'] ?></p>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <h2>Amount Due (<?php echo $invoice['currency'] ?>)</h2>
                <p><?php echo $invoice['amount'] ?></p>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <h2>Invoice Number</h2>
                <p><?php echo $invoice['id'] ?></p>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <h2>Date of Invoice</h2>
                <p><?php echo $invoice['created_at'] ?></p>
            </div>
        </div>
    </div>
    <!-- invoice details ends -->


    <!-- pay btn start -->
    <div class="pay-btn">
        <button class="btn btn-primary" onclick="pay()">Pay <?php echo $invoice['amount'] ?>
    </button>
    </div>
    <!-- pay btn ends -->


</div>
<!-- invoice ends -->




<script>
    function pay() {
        var url = "<?php echo $server_url ?>" + "api/pay.php";
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url);
        xhr.setRequestHeader("Accept", "application/json");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var response = JSON.parse(xhr.responseText)
            if (response.success) {
                document.querySelector('.invoice').innerHTML = '<div class="alert alert-success" style="font-size: 2rem !important;line-height: 1.7;">Your invoice has been successfully paid<br>Transaction Number is : <strong style="color: #13216a;">#' + response.transaction_number + '</strong><br>You will be redirected in <span id="countdown" style="color: #13216a;font-weight: 700;">5</span> seconds</div>'
                var timer = 5;
                var downloadTimer = setInterval(function(){
                    if(timer == 1){
                        clearInterval(downloadTimer);
                        window.location.href = response.redirection_url + '?id=' + response.invoice_token
                    }
                    timer -= 1;
                    document.querySelector('#countdown').innerHTML = timer;
                }, 1000);
            } else {
                document.querySelector('.invoice').innerHTML = '<div class="alert alert-danger">' + response.message + '</div>'
            }
        }};
        var data = {
            "invoice_token": "<?php echo $invoice['invoice_token'] ?>",
            "user_id": "<?php echo $current_user['id'] ?>"
        };
        xhr.send(JSON.stringify(data));
    }
</script>