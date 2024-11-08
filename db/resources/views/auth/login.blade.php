<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ادفعلى | تسجيل الدخول</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('dashboard/img/icon.png') }}" />
    <link rel="stylesheet" href="{{ asset('dashboard/styles/css/style.css') }}">
</head>
<body>

    @include('sweetalert::alert')

    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="row w-100 m-0">
                <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
                    <div class="card col-lg-4 mx-auto">
                        <div class="card-body px-5 py-5">
                            <div class="brand text-center mb-3">
                                <img src="{{ asset('dashboard/img/logo.png') }}" width="125" alt="Edf3ly">
                            </div>
                            @if(Session::has('error'))
                            <div class="auth-errors text-center">
                                <div class="auth-error">
                                    <i class="error-icon mdi mdi-close-circle"></i>
                                    {{ Session::get('error') }}
                                </div>
                            </div>
                            @endif
                            @if(Session::has('logout_message'))
                            <div class="auth-successes text-center">
                                <div class="auth-success">
                                    <i class="success-icon mdi mdi-check-circle"></i>
                                    {{ Session::get('logout_message') }}
                                </div>
                            </div>
                            @endif

                            <h3 class="card-title text-left mb-4">Sign in</h3>

                            <form class="auth-form login-form" id="loginForm" action="login_user">

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control p_input" name="email" id="email" placeholder="johndoe@info.com">
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control p_input" name="password" placeholder="********">
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="remember_token"> Remember me </label>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-main btn-block enter-btn" id="loginButton">Login</button>
                                </div>

                            </form>

                        </div>

                    </div>

                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- row ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>

    <script>

        $(document).ready(function(){

            const validator = $("#loginForm").validate({

                rules: {
                    email: {
                        required: true,
                        email : true
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    email: {
                        required: "Email Address is required",
                        email : "Please Enter a valid Email Address"
                    },
                    password: {
                        required: "Password is required"
                    },
                },
                errorPlacement: function(error, element){
                    error.insertAfter(element).fadeIn(500);
                }

            });

        });

    </script>

</body>
</html>
