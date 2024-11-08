@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('services') }}">Services</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>{{ $service->service_name_ar }}</span>
                </h4>
            </div>


            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title text-info">Service Details</h4>


                        <div class="row">

                            <div class="col-md-7 mb-4">
                                <img class="mb-5" src="https://ramybadr.com/pay-to-me/images/{{ $service->service_image }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        @if($service->active == 1)
                                        <a style="border:1px solid darkgreen" class="dropdown-item py-3 suspend-item" data-title="Deactivate service ?" data-content="Do you want to deactivate this service ?" href="{{ route('services_deactivate',$service->id) }}">
                                            <i class="mdi mdi-minus-circle text-danger"></i>
                                            <span class="mx-2">Deactivate</span>
                                        </a>
                                        @endif
                                        @if($service->active == 0)
                                        <a style="border:1px solid darkgreen" class="dropdown-item py-3 suspend-item" data-title="Activate service ?" data-content="Do you want to activate this service ?" href="{{ route('services_activate',$service->id) }}">
                                            <i class="mdi mdi-shield-account-outline text-warning"></i>
                                            <span class="mx-2">Activate</span>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($service->approved == 1)
                                        <a style="border:1px solid darkgreen" class="dropdown-item py-3 suspend-item" data-title="Disapproved service ?" data-content="Do you want to Disapproved this service ?" href="{{ route('services_disapproved',$service->id) }}">
                                            <i class="mdi mdi-minus-circle text-danger"></i>
                                            <span class="mx-2">Disapproved</span>
                                        </a>
                                        @endif
                                        @if($service->approved == 0)
                                        <a style="border:1px solid darkgreen" class="dropdown-item py-3 suspend-item" data-title="approved service ?" data-content="Do you want to approved this service ?" href="{{ route('services_approved',$service->id) }}">
                                            <i class="mdi mdi-shield-account-outline text-warning"></i>
                                            <span class="mx-2">Approved</span>
                                        </a>
                                        @endif
                                    </div>
                                </div>


                                
                            </div>
                            <div class="col-md-5 mb-4">
                                <div class="mb-3">
                                    <a style="width:100%" class="btn btn-info" href="{{ route('app_users_view',$service->user_id) }}">View User</a>
                                </div>
                                <div>
                                    <a style="width:100%" class="btn btn-warning" href="{{ route('providers_edit',$service->provider_id) }}">View Provider</a>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">Provider name : <strong class="mx-2">{{ $service->provider_name }}</strong></div>
                            <div class="col-md-6 mb-3">Username : <strong class="mx-2">{{ $service->user_name }}</strong></div>
                            <div class="col-md-6 mb-3">Name arabic  : <strong class="mx-2">{{ $service->service_name_ar }}</strong></div>
                            <div class="col-md-6 mb-3">Name english : <strong class="mx-2">{{ $service->service_name_en }}</strong></div>
                            <div class="col-md-6 mb-3">Amount : <strong class="mx-2">{{ $service->amount }} {{ $service->wallet_name }}</strong></div>
                            <div class="col-md-6 mb-3">City : <strong class="mx-2">{{ $service->city_en_name }}</strong></div>
                            <div class="col-md-6 mb-3">Field : <strong class="mx-2">{{ $service->filed_en_name }}</strong></div>
                            <div class="col-md-12 mb-3">Description : <strong class="mx-2">{{ $service->description }}</strong></div>

                            <div class="col-md-8 mb-3 alert alert-primary">Service Member Count : <strong class="mx-2">{{ $service_member_count}}</strong></div>

                            <div class="col-md-12 my-6"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>


                            

                        </div>


                        


                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection



