@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('settings_edit') }}">Update Settings</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Update Settings</span>
                </h4>
            </div>


            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title">Edit Settings</h4>
                        <p class="card-description"> Change Settings information and click  "Update" </p>

                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('settings_update') }}" method="POST">
                            @csrf

                            <div class="row">

                                @foreach($settings as $setting)
                                <div class="col-md-7">
                           

                                    <div class="form-group mb-4">
                                        <label for="active{{$setting->id}}" class="toggle-switchy" data-color="green">
                                            <span class="mr-5">{{$setting->name}}</span>
                                            <input type="checkbox" name="active[{{$setting->id}}]" id="active{{$setting->id}}" value="1"  @if($setting->body == 1) checked @endif>
                                            <span class="toggle">
                                                <span class="switch"></span>
                                            </span>
                                        </label>
                                    </div>

                                </div>
                                @endforeach

                              
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="title">
                                           Version
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="version" id="version" class="form-control" value="{{ $version->body }}" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="title">
                                            Firebase Key
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="firebase_key" id="firebase_key" class="form-control" value="{{ $firebase_key->body }}" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="title">
                                            App Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="app_name" id="app_name" class="form-control" value="{{ $app_name->body }}" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="title">
                                            App Email
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="app_email" id="app_email" class="form-control" value="{{ $app_email->body }}" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Update</button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
@push('scripts')

<script type="text/javascript">
     function toggle(id)
            {
              alert($("#active"+id).val())
            }
</script>
@endpush