@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('transaction') }}">Transactions</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>{{ $transaction->transaction_number }}</span>
                </h4>
            </div>


            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title text-info">Transaction Details</h4>

                        
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        Sender : 
                                        <strong class="mx-2">{{ \App\Models\User::find($transaction->sender_id)->user_name }}</strong>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        Recipient : 
                                        <strong class="mx-2">{{ \App\Models\User::find($transaction->recipient_id)->user_name }}</strong>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        Transaction number : 
                                        <strong class="mx-2">{{ $transaction->transaction_number }}</strong>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        Amount : 
                                        <strong class="mx-2">{{ $transaction->amount }} {{ \App\Models\Wallet::find($transaction->wallet)->wallet_name }}</strong>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        Transaction type : 
                                        <strong class="mx-2">{{ $transaction->transaction_type }}</strong>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        Creation Time  : 
                                        <strong class="mx-2">{{ $transaction->creationTime }}</strong>
                                    </div>

                                    @if($transaction->transaction_code)
                                    <div class="col-md-6 mb-3">
                                        Transaction type : 
                                        <strong class="mx-2">{{ $transaction->transaction_code }}</strong>
                                    </div>
                                    @endif


                                    <div class="col-md-12 my-4"><span style="width:96%;height: 2px; background: #cb5454; position: absolute;"></span></div>
                                    
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <a style="width:100%" class="btn btn-info" href="{{ route('app_users_view',$transaction->sender_id) }}">Sender</a>
                                </div>
                                <div>
                                    <a style="width:100%" class="btn btn-warning" href="{{ route('app_users_view',$transaction->recipient_id) }}">Recipient</a>
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            @if($transaction->transaction_type == 'trade')
                                <h4 class="card-title text-info">Trade Service Details</h4>
                                <div class="col-md-12">
                                    <?php
                                        $type_details = json_decode($transaction->type_details, true);
                                    ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>#ID</th>
                                            <th>Trader</th>
                                            <th>From Wallet</th>
                                            <th>To Wallet</th>
                                            <th>Exchange rate</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{$type_details['id']}}</td>
                                                <td>{{ \App\Models\Trader::find($type_details['trader_id'])->trader_name }}</td>
                                                <td>{{ \App\Models\Wallet::find($type_details['from_wallet'])->wallet_name }}</td>
                                                <td>{{ \App\Models\Wallet::find($type_details['to_wallet'])->wallet_name }}</td>
                                                <td>{{$type_details['exchange_rate']}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($transaction->transaction_type == 'service')
                                <h4 class="card-title text-info">Service Details</h4>
                                <div class="col-md-12">
                                    <?php
                                        $type_details = json_decode($transaction->type_details, true);
                                    ?>

                                    <table class="table table-bordered">
                                        <thead>
                                            <th>#ID</th>
                                            <th>Service name arabic</th>
                                            <th>Service name english</th>
                                            <th>Amount</th>
                                            <th>Wallet</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{$type_details['id']}}</td>
                                                <td>{{$type_details['service_name_ar']}}</td>
                                                <td>{{$type_details['service_name_en']}}</td>
                                                <td>{{$type_details['amount']}}</td>
                                                <td>{{ \App\Models\Wallet::find($type_details['wallet_id'])->wallet_name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($transaction->transaction_type == 'invoice')
                                <h4 class="card-title text-info">Service Details</h4>
                                <div class="col-md-12">
                                    <?php
                                        $type_details = json_decode($transaction->type_details, true);
                                    ?>

                                    <table class="table table-bordered">
                                        <thead>
                                            <th>#ID</th>
                                            <th>Organization</th>
                                            <th>User</th>
                                            <th>Amount</th>
                                            <th>Wallet</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{$type_details['id']}}</td>
                                                <td>{{ \App\Models\Organization::find($type_details['organization_id'])->organization_name }}</td>
                                                <td>{{ \App\Models\User::find($type_details['user_id'])->full_name }}</td>
                                                <td>{{$type_details['amount']}}</td>
                                                <td>{{ \App\Models\Wallet::find($type_details['wallet_id'])->wallet_name }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
            


                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')


@endpush
