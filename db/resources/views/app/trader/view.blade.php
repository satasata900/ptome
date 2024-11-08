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
                    <span>{{ $trader->trader_name }}</span>
                </h4>
            </div>


            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title text-info">Trader Details</h4>

                        
                        <div class="row">

                            <div class="col-md-9 mb-4">
                                <img src="https://ramybadr.com/pay-to-me/images/{{ $trader->trader_image }}">
                            </div>
                                
                            <div class="col-md-4 mb-3">Name : <strong class="mx-2">{{ $trader->trader_name }}</strong></div>
                            <div class="col-md-4 mb-3">Username : <strong class="mx-2">{{ $trader->user->full_name }}</strong></div>
                            
                            <div class="col-md-6 mb-3 alert alert-primary">Trader Services Count : <strong class="mx-2">{{ $trader_services_count}}</strong></div>

                            <div class="col-md-12 my-4"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>


                            

                        </div>


                        <h4 class="card-title text-info">User Details</h4>

                        
                        <div class="row">
                                
                            <div class="col-md-6 mb-3">Name : <strong class="mx-2">{{ $trader->user->full_name }}</strong></div>
                            <div class="col-md-6 mb-3">Username : <strong class="mx-2">{{ $trader->user->user_name }}</strong></div>
                            <div class="col-md-6 mb-3">Email : <strong class="mx-2">{{ $trader->user->email }}</strong></div>
                            <div class="col-md-6 mb-3">Phone : <strong class="mx-2">{{ $trader->user->phone }}</strong></div>
                            <div class="col-md-6 mb-3">Active : <strong class="mx-2">{{ $trader->user->active == 1 ? 'On' : 'Off' }}</strong></div>
                            <div class="col-md-6 mb-3">Iso-code : <strong class="mx-2">{{ $trader->user->isoCode }}</strong></div>
                            
                            <div class="col-md-12 my-4"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>

                        </div>



                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')


@endpush
