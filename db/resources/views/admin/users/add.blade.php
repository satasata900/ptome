@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('system_users') }}">Users</a>
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
                        <form class="forms-sample mt-4" id="createForm" action="{{ route('system_users_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="fullName">
                                            Full Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="fullName" id="fullName" class="form-control" placeholder="Enter User Full Name">
                                    </div>
                                </div>

                                <div class="col-md-6 d-flex justify-content-md-end">
                                    <div class="form-group mb-4">
                                        <label for="suspended" class="toggle-switchy" data-color="red" data-text="suspended">
                                            <span class="mr-3">Suspended</span>
                                            <input type="checkbox" id="suspended" name="suspended">
                                            <span class="toggle">
		                                        <span class="switch"></span>
	                                        </span>
                                        </label>
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
                                        <label for="lang">
                                            Language
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <select class="form-control" name="lang" id="lang">
                                            <option value="en">ENGLISH</option>
                                            <option value="ar">ARABIC</option>
                                        </select>
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
                                            Password
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Retype password">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="user_email">
                                            App Email
                                            <span class="text-warning mx-1">( Optional )</span>
                                        </label>
                                        <input type="text" name="user_email" id="user_email" class="form-control no-space" placeholder="Email in the application">
                                    </div>
                                </div>

                                <div class="col-12">

                                    <div class="form-group">
                                        <label>Image upload</label>
                                        <input type="file" name="img" class="file-upload-default" id="avatarInput">
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" id="avatarInfo" disabled placeholder="Upload Image">
                                            <span class="input-group-append">
                                                <button id="uploadFileButton" data-target="#avatarInput" data-info="#avatarInfo" class="file-upload-browse btn btn-info" type="button">Upload</button>
                                            </span>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <button type="submit" class="btn btn-success mt-3">Create User</button>
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
                fullName: {
                    required: true,
                },
                email: {
                    required: true,
                    email:true
                },
                password: {
                    required: true,
                    minlength:6,
                    maxlength:255,
                },
                confirmPassword: {
                    equalTo : "#password"
                },
                user_email: {
                    email : true
                }
            },
            messages: {
                fullName: {
                    required: "Name is required",
                },
                email: {
                    required: "Email address is required",
                    email:"Enter a valid email address"
                },
                password: {
                    required: "Password is required",
                    minlength:"Password must be at least 6 characters",
                    maxlength:"Password maximum length is 255 characters",
                },
                confirmPassword: {
                    equalTo : "Must be the same as password"
                },
                user_email : {
                    email : "Enter a valid an email address"
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

    });

</script>
@endpush
