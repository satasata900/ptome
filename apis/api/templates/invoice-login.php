        <!-- login form starts -->
        <div class="login-form">
            <div class="form">
                <div class="logo">
                    <img src="<?php echo $images_url . 'logo.png' ?>">
                </div>
                <h2 class="site-name"><?php echo $app_name ?></h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $invoice_id;?>">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="email" type="email" class="form-control input-lg" name="email" placeholder="Email" value="<?php echo $email; ?>">
                    </div>
                    <br>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="password" type="password" class="form-control input-lg" name="password" placeholder="Password" value="<?php echo $password; ?>">
                    </div>
                    <br>

                    <button class="btn btn-primary">login</button>
                </form>
                <br>      
                <?php 
                    if ($msg && !$success) {
                        echo '<div class="alert alert-danger">' . $msg . '</div>';
                    } else if ($msg && $success) {
                        echo '<div class="alert alert-success">' . $msg . '</div>';
                    }
                ?>      
            </div>
        </div>
        <!-- login form ends -->