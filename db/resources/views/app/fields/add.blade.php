@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('fields') }}">Fields</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>New Field</span>
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
                        <h4 class="card-title">New field</h4>
                        <p class="card-description"> Complete field information and click  "ADD" </p>

                        <form class="forms-sample mt-4" id="createForm" action="{{ route('fields_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_ar">
                                            Arabic Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="name_ar" id="name_ar" class="form-control no-space" placeholder="Arabic Name" value="{{ old('name_ar') }}">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_en">
                                            English Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="name_en" id="name_en" class="form-control no-space" placeholder="English Name" value="{{ old('name_en') }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="active">
                                            <input type="checkbox" name="active" id="active" class="form-control" checked>
                                            Mark field as active
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Add Field</button>

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
                name_ar: {
                    required: true,
                },
                name_en: {
                    required: true,
                }
            },
            messages: {
                name_ar: {
                    required: "Arabic name is required",
                },
                name_en: {
                    required: "English name is required",
                }
            }

        });

        $('.js-example-basic-single').select2();

    });

</script>
@endpush
