@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('trader') }}">Traders</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
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
                        <h4 class="card-title">Send Notification <span class="text-info mx-1">for all traders</span></h4>
                        <p class="card-description"> Complete information and click  "Send" </p>

                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('send_notification_for_all_traders_post') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            
                            <div class="row">

                                
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="title">
                                            Title
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="message">
                                            Message
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <textarea  name="message" id="message" class="form-control" placeholder="Enter Message"></textarea>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Send Notifications</button>

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
                title: {
                    required: true,
                },
                message: {
                    required: true,
                }
                
               
            },
            messages: {
               
                title: {
                    required: "Title is required",
                },
                
                message: {
                    required: "Message is required",
                }
            },

        });

       
      
    });

</script>
@endpush
