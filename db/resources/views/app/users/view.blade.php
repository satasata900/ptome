@extends('layout.master')

<style type="text/css">
    .tr
    {
        cursor: pointer;
        transition: all 0.2s;
    }
    .tr:hover
    {
        transform: scale(1.01);
    }
    .mdi
    {
        padding: 5px 5px 1px;
        font-size: 20px;
        border-radius: 50%;
    }
    .mdi-arrow-up
    {
        background: #23bf20;
    }
    .mdi-arrow-down
    {
        background: #ff9800;
    }
</style>
@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('app_users') }}">Users</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>{{ $user->full_name }}</span>
                </h4>
            </div>


            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title text-info">User Details</h4>

                        
                        <div class="row">
                                
                            <div class="col-md-6 mb-3">Name : <strong class="mx-2">{{ $user->full_name }}</strong></div>
                            <div class="col-md-6 mb-3">Username : <strong class="mx-2">{{ $user->user_name }}</strong></div>
                            <div class="col-md-6 mb-3">Email : <strong class="mx-2">{{ $user->email }}</strong></div>
                            <div class="col-md-6 mb-3">Phone : <strong class="mx-2">{{ $user->phone }}</strong></div>
                            <div class="col-md-6 mb-3">Active : <strong class="mx-2">{{ $user->active == 1 ? 'On' : 'Off' }}</strong></div>
                            <div class="col-md-6 mb-3">Iso-code : <strong class="mx-2">{{ $user->isoCode }}</strong></div>

                            <div class="col-md-12 mb-3">Registration Date : <strong class="mx-2">{{ $user->registration_Time }}</strong></div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 mb-3">Provider : <strong class="mx-2">
                                        @if(\App\Models\Provider::where('user_id', $user->id)->exists())
                                            <span class="text text-success">Yes</span><br>
                                            <strong>Services Count : <span class="text text-success">{{\App\Models\Service::where('provider_id', \App\Models\Provider::where('user_id', $user->id)->first()->id)->count()}}</span></strong>
                                        @else
                                            <span class="text text-danger">No</span>
                                        @endif
                                        </strong>
                                    </div>
                                    <div class="col-md-12 mb-3">Trader : <strong class="mx-2">
                                        @if(\App\Models\Trader::where('user_id', $user->id)->exists())
                                            <span class="text text-success">Yes</span><br>
                                             <strong>Trader Service Count : <span class="text text-success">{{\App\Models\Trader_Service::where('trader_id', \App\Models\Trader::where('user_id', $user->id)->first()->id)->count()}}</span></strong>
                                        @else
                                            <span class="text text-danger">No</span>
                                        @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 mb-3">Groups Count : <strong class="mx-2">
                                        <span class="text text-success">
                                            {{ \DB::connection('mysql2')->table("user_groups")->where('user_id', $user->id)->count() }}
                                        </span>
                                        </strong>
                                    </div>
                                    <div class="col-md-12 mb-3">Services subscription count: <strong class="mx-2">
                                        <span class="text text-success">{{ \DB::connection('mysql2')->table("service_members")->where('member_id', $user->id)->count() }}</span>
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 my-4"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>

                        </div>

                        <!-- User Wallets -->

                        <h4 class="card-title text-info">User Wallets</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table text-center table-bordered">
                                    <thead>
                                        <th>#ID</th>
                                        <th>Wallet</th>
                                        <th>Quantity</th>
                                    </thead>
                                    <tbody>
                                        @foreach($users_wallets as $wallet)
                                        <tr>
                                            <td>{{ $wallet->id }}</td>
                                            <td>{{ \App\Models\Wallet::find($wallet->wallet_id)->wallet_name }}</td>
                                            <td>{{ $wallet->price }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-12 my-4"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>
                        </div>



                        <!-- User Transactions -->

                        <h4 class="card-title text-info">User Transactions</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class=""> Transaction number </th>
                                    <th class=""> Transaction Type </th>
                                    <th class=""> Wallet</th>
                                    <th class=""> Amount</th>
                                    <th class=""> CreationTime</th>
                                    <th class=""></th>
                                </tr>
                                </thead>
                                <tbody>
                                
                                @if(count($user_transactions) > 0)
                                    @foreach($user_transactions as $org)
                                    
                                    <tr onclick="window.location.href='{{ route("transaction_view",$org->id) }}'" class="tr">
                                  
                                        <td class=" py-1">
                                            {{ $org->transaction_number }}
                                        </td>
                                        <td class="">
                                            {{ $org->transaction_type }}
                                            @if($org->transaction_type == 'username')
                                            <span class="text text-success">( {{ ($org->recipient_id == $user->id) ? \App\Models\User::find($org->sender_id)->user_name : \App\Models\User::find($org->recipient_id)->user_name}} )</span>
                                            @elseif($org->transaction_type == 'transaction_code')
                                            <span class="text text-success">( {{ $org->transaction_code }} )</span>
                                            @endif

                                        </td>
                                        <td class="">
                                            {{ \App\Models\Wallet::find($org->wallet)->wallet_name }}
                                        </td>
                                        <td class="">
                                            {{ $org->amount }}
                                        </td>

                                        <td class="">
                                         {{$org->creationTime}}   
                                        </td>
                                        <td>
                                            @if($org->sender_id == $user->id)
                                            <span class="mdi mdi-arrow-up mx-1"></span>
                                            @elseif($org->recipient_id == $user->id)
                                            <span class="mdi mdi-arrow-down mx-1"></span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td class="px-0" colspan="7">
                                            <div class="alert alert-danger bg-danger text-white py-4">
                                                <i class="mdi mdi-message"></i>
                                                <span class="mx-1">No Transactions to display</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            </div>

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
