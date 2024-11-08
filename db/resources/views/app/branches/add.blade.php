@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('branches') }}">Branches</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>New Branch</span>
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
                        <h4 class="card-title">New branch</h4>
                        <p class="card-description"> Complete City information and click  "ADD" </p>

                        <form class="forms-sample mt-4" id="createForm" action="{{ route('branches_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_ar">
                                            Branch Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="branch_name" id="branch_name" class="form-control no-space" placeholder="Branch Name" value="{{ old('branch_name') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_ar">
                                            Address
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="address" id="address" class="form-control no-space" placeholder="Address" value="{{ old('address') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_ar">
                                            Latitude 
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="lat" id="lat" class="form-control no-space" placeholder="Latitude" value="{{ old('lat') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_ar">
                                            Longitude
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="lon" id="lon" class="form-control no-space" placeholder="Longitude" value="{{ old('lon') }}" required>
                                    </div>
                                </div>


                                
                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Add Branch</button>

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
                branch_name: {
                    required: true,
                },
                address: {
                    required: true,
                },
                lat: {
                    required: true,
                },
                lon: {
                    required: true,
                }
            },
           

        });

        $('.js-example-basic-single').select2();

    });

</script>
@endpush
