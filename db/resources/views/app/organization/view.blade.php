@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('organization') }}">Organizations</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>{{ $organization->organization_name }}</span>
                </h4>
            </div>


            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title text-info">Organization Details</h4>

                        
                        <div class="row">
                                
                            <div class="col-md-4 mb-3">Name : <strong class="mx-2">{{ $organization->organization_name }}</strong></div>
                            <div class="col-md-4 mb-3">Username : <strong class="mx-2">{{ $organization->user->full_name }}</strong></div>
                            <div class="col-md-4 mb-3">Country : <strong class="mx-2">{{ \App\Models\Country::find($organization->country)->country_en }}</strong></div>
                            <div class="col-md-12 mb-3">Secret key : <strong class="mx-2">{{ $organization->secret_key }}</strong></div>

                            <div class="col-md-6 mb-3 alert alert-primary">Organization Invoices Count : <strong class="mx-2">{{ $organization_invoices_count}}</strong></div>

                            <div class="col-md-12 my-4"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>


                            

                        </div>


                        <h4 class="card-title text-info">User Details</h4>

                        
                        <div class="row">
                                
                            <div class="col-md-6 mb-3">Name : <strong class="mx-2">{{ $organization->user->full_name }}</strong></div>
                            <div class="col-md-6 mb-3">Username : <strong class="mx-2">{{ $organization->user->user_name }}</strong></div>
                            <div class="col-md-6 mb-3">Email : <strong class="mx-2">{{ $organization->user->email }}</strong></div>
                            <div class="col-md-6 mb-3">Phone : <strong class="mx-2">{{ $organization->user->phone }}</strong></div>
                            <div class="col-md-6 mb-3">Active : <strong class="mx-2">{{ $organization->user->active == 1 ? 'On' : 'Off' }}</strong></div>
                            <div class="col-md-6 mb-3">Iso-code : <strong class="mx-2">{{ $organization->user->isoCode }}</strong></div>

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
