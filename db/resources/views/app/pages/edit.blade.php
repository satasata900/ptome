@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="#">Pages</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>update @if($content->name == 'terms-conditions') Terms & Conditions @elseif($content->name == 'privacy-policy') Privacy & Policy @elseif($content->name == 'about') About us @endif</span>
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
                        <h4 class="card-title">Edit page</h4>
                        <p class="card-description"> Change page information and click  "Update" </p>

                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('update_page') }}" method="POST">
                            @csrf

                            <div class="row">
                                <input type="hidden" name="id" value="{{$content->id}}">
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="ar_content">
                                            Arabic content
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <textarea name="ar_content" id="ar_content" class="form-control no-space" placeholder="Arabic content">{!! $content->ar_content !!}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="en_content">
                                            English content
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <textarea name="en_content" id="en_content" class="form-control no-space" placeholder="English content">{!! $content->en_content !!}</textarea>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Update Page</button>

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
                ar_content: {
                    required: true,
                },
                en_content: {
                    required: true,
                }
            },
            messages: {
                ar_content: {
                    required: "Arabic content is required",
                },
                en_content: {
                    required: "English content is required",
                }
            }

        });

        $('.js-example-basic-single').select2();

    });

</script>
@endpush
