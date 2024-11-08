@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('app_users') }}">Users</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>New User</span>
                </h4>
            </div>

            @if($errors->any())

            <div class="col-12 grid-margin stretch-card">

                <div class="crud-error bg-danger text-white">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>
                                <i class="mdi mdi-close-circle-outline"></i>
                                <span class="mx-2">{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            @endif

            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title">Add New User</h4>
                        <p class="card-description"> Complete User information and click  "ADD" </p>

                        <form class="forms-sample mt-4" id="createForm" action="{{ route('app_users_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">

                                <div class="col-md-6"></div>

                                <div class="col-md-3 d-flex justify-content-md-end">
                                    <div class="form-group mb-4">
                                        <label for="notifications" class="toggle-switchy" data-color="red">
                                            <span class="mr-3">Notifications</span>
                                            <input type="checkbox" id="notifications" name="notifications" checked>
                                            <span class="toggle">
		                                        <span class="switch"></span>
	                                        </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex justify-content-md-end">
                                    <div class="form-group mb-4">
                                        <label for="active" class="toggle-switchy" data-color="green">
                                            <span class="mr-3">Active</span>
                                            <input type="checkbox" id="active" name="active" checked>
                                            <span class="toggle">
		                                        <span class="switch"></span>
	                                        </span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="fullName">
                                            Custom ID
                                            <span class="text-warning mx-1">( Enter if you need a specific id )</span>
                                        </label>
                                        <input type="text" name="id" id="id" class="form-control numeric-input no-paste" placeholder="User ID">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="full_name">
                                            Full Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Enter User Full Name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="email">
                                            Email
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="email" id="email" class="form-control no-space" placeholder="Enter Email Address">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="user_name">
                                            Username
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="user_name" id="user_name" class="form-control no-space" placeholder="Enter username">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="phone">
                                            Phone
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="phone" id="phone" class="form-control numeric-input no-space" placeholder="Enter mobile number">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="birthdate">
                                            Birthdate
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="birthdate" id="birthdate" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="isoCode">
                                            Iso Code
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="isoCode" id="isoCode" class="form-control" placeholder="Enter iso code">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="pin_code">
                                            Pin Code
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="pin_code" id="pin_code" class="form-control" placeholder="Enter pin code">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="password">
                                            Password
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="confirmPassword">
                                            Confirm Password
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Retype password">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="verified_email">
                                            <input type="checkbox" name="verified_email" id="verified_email" class="form-control">
                                            Mark Email as verified
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="verified_phone">
                                            <input type="checkbox" name="verified_phone" id="verified_phone" class="form-control">
                                            Mark Phone as verified
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="pincode_require">
                                            <input type="checkbox" name="pincode_require" id="pincode_require" class="form-control">
                                            Mark Pincode as required
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="test_mode">
                                            <input type="checkbox" name="test_mode" id="test_mode" class="form-control">
                                            Test Mode
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Create User</button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){

        const validator = $("#createForm").validate({
            rules: {
                full_name: {
                    required: true,
                },
                email: {
                    required: true,
                    email:true
                },
                user_name: {
                    required: true
                },
                phone:{
                    required: true,
                    number : true
                },
                password: {
                    required: true,
                    minlength:6,
                    maxlength:255,
                },
                confirmPassword: {
                    equalTo : "#password"
                },
                pin_code: {
                    required : true
                },
                isoCode: {
                    required : true
                }
            },
            messages: {
                full_name: {
                    required: "Name is required",
                },
                email: {
                    required: "Email address is required",
                    email:"Enter a valid email address"
                },
                user_name: {
                    required: "Username is required",
                },
                phone: {
                    required: "Phone number is required",
                    number : "Must be a number"
                },
                password: {
                    required: "Password is required",
                    minlength:"Password must be at least 6 characters",
                    maxlength:"Password maximum length is 255 characters",
                },
                confirmPassword: {
                    equalTo : "Must be the same as password"
                },
                pin_code : {
                    required : "Pin Code is required"
                },
                isoCode : {
                    required : "Iso Code is required"
                }
            },

        });

        $(".file-upload-browse").click(function(){

            const targetInput = $($(this).attr('data-target'));
            targetInput.trigger('click');

        });

        $("#avatarInput").change(function (){

            const avatarInfo = $("#avatarInfo");
            avatarInfo.val($(this).val());

        });

        $('input[name="birthdate"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            maxYear: parseInt(moment().format('YYYY'),10)
        }, function(start, end, label) {
            var years = moment().diff(start, 'years');
        });

    });

</script>
@endpush
