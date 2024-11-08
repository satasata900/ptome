@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('providers') }}">Providers</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Edit Provider</span>
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
                        <h4 class="card-title">Update provider</h4>
                        <p class="card-description"> Edit provider information and click  "update" </p>
                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('providers_update',$provider->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="providerName">
                                            Full Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="providerName" id="providerName" class="form-control" placeholder="Enter provider name" value="{{ $provider->provider_name }}">
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex justify-content-md-end">
                                    <div class="form-group mb-4">
                                        <label for="approved" class="toggle-switchy" data-color="blue" data-text="Approved">
                                            <span class="mr-3">Approved</span>
                                            <input type="checkbox" id="approved" name="approved" @if($provider->approved_provider == 1) checked @endif>
                                            <span class="toggle">
		                                        <span class="switch"></span>
	                                        </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex justify-content-md-end">
                                    <div class="form-group mb-4">
                                        <label for="confirmed" class="toggle-switchy" data-color="green" data-text="Confirmed">
                                            <span class="mr-3">Confirmed</span>
                                            <input type="checkbox" id="confirmed" name="confirmed" @if($provider->confirmed_provider == 1) checked @endif>
                                            <span class="toggle">
		                                        <span class="switch"></span>
	                                        </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="username">
                                            Username
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <select class="form-control select2" name="username" id="username">
                                            <option value="">Select a user</option>
                                            @foreach($users as $user)
                                                  <option value="{{ $user->id }}" @if($user->id == $provider->user_id) selected @endif>{{ $user->user_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="mobile">
                                            Mobile Number
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="mobile" id="mobile" class="form-control no-space numeric-input" placeholder="Mobile number" value="{{ $provider->provider_phone }}">
                                    </div>
                                </div>

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <label>Image upload</label>
                                        <input type="file" name="img" class="file-upload-default" id="avatarInput">
                                        <input type="hidden" name="old_img" value="{{ $provider->provider_image }}">
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" id="avatarInfo" disabled placeholder="Upload Image">
                                            <span class="input-group-append">
                                                <button id="uploadFileButton" data-target="#avatarInput" data-info="#avatarInfo" class="file-upload-browse btn btn-info" type="button">Upload</button>
                                            </span>
                                        </div>
                                        <div class="image mt-3">
                                            <img src="{{ providerURL($provider->provider_image) }}" class="rounded-circle" width="100" height="100" alt="{{ $provider->user->username }}">
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <button type="submit" class="btn btn-success mt-3">Update provider</button>
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
        const validator = $("#updateForm").validate({

            rules: {
                providerName: {
                    required: true,
                },
                username: {
                    required: true,
                },
                mobile: {
                    required: true,
                    minlength:9,
                    maxlength:15,
                }
            },
            messages: {
                providerName: {
                    required: "Name is required",
                },
                username: {
                    required: "You must select a user",
                },
                mobile: {
                    required: "Mobile number is required",
                    minlength:"Mobile must be at least 9 characters",
                    maxlength:"Mobile maximum length is 15 characters",
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

        $(".select2").select2({});

    });

</script>
@endpush
