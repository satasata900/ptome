@extends('layout.master')
<style type="text/css">
    .mySelect
    {
        width: 100%;
        padding: 8px;
    }
    .pricesBox1
    {
        background: #398158;
        padding: 26px 10px;
        border-radius: 5px;
        margin: 0px;
    }
    .pricesBox2
    {
        background: #a17316;
        padding: 26px 10px;
        border-radius: 5px;
        margin: 0px;
    }
</style>
@section('content')


<?php 

        $today = \Carbon\Carbon::today()->format('Y-m-d'); //today
        $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d'); //yesterday
        $last_7_days = \Carbon\Carbon::now()->subDays(7)->format('Y-m-d'); //last_7_days
        $last_30_days = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'); //last_30_days
        $last_month = \Carbon\Carbon::now()->subMonth()->format('m'); //last_month
        $this_month = \Carbon\Carbon::now()->format('m'); //this_month

?>

    <div class="content-wrapper">

        <div class="row">

            <div class="col-8 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Transfer money</span>
                </h4>
            </div>
            <div class="col-lg-4 col-sm-4">
                <div class="form-check text-right">
                    <label class="form-check-label">
                        <a class="btn btn-outline-info" href="{{ route('admin_transactions_add') }}" id="addNewService">
                            <i class="mdi mdi-plus"></i>
                            <span class="mx-1">
                                Send or Withdraw Money
                            </span>
                        </a>
                    </label>
                </div>
            </div>
            

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">App Transfer money</h4>
                        <div class="table-action-row mb-2">
                            <div class="row d-flex justify-content-end">
                                

                                <div class="col-3" >
                                    <form method="GET" action="{{ route('admin_transactions') }}" id="filter_by_created_at">
                                        <select class="mb-3 mySelect" name="filter_by_created_at" >
                                            <option value="all">Filter by created date...</option>
                                            <option value="today" @if(request('filter_by_created_at') == 'today') selected @endif >Today</option>
                                            <option value="yesterday"  @if(request('filter_by_created_at') == 'yesterday') selected @endif >Yesterday</option>
                                            <option value="last_7_days"  @if(request('filter_by_created_at') == 'last_7_days') selected @endif >Last 7 Days</option>
                                            <option value="last_30_days"  @if(request('filter_by_created_at') == 'last_30_days') selected @endif >Last 30 Days</option>
                                            <option value="last_month"  @if(request('filter_by_created_at') == 'last_month') selected @endif >Last Month</option>
                                            <option value="this_month"  @if(request('filter_by_created_at') == 'this_month') selected @endif >This Month</option>
                                        </select>
                                    </form>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-6 grid-margin">
                                <div class="card bg-success">
                                    <div class="card-body">
                                        <h3 class="text-white">Send</h3>
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-xl-12 my-auto">
                                               <div class="row pricesBox1">
                                                @foreach($wallets as $wallet)

                                                <div class="col-6 mb-3">{{$wallet->wallet_name}}</div>
                                                <div class="col-6 mb-3">
                                                    <?php 
                                                     if(request('filter_by_created_at') == 'today')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->whereDate('created_at', $today)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'yesterday')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->whereDate('created_at', $yesterday)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'last_7_days')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->whereDate('created_at', '>=',$last_7_days)->whereDate('created_at', '<=',$today)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'last_30_days')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->whereDate('created_at', '>=',$last_30_days)->whereDate('created_at', '<=',$today)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'last_month')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->whereMonth('created_at', $last_month)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'this_month')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->whereMonth('created_at', $this_month)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'all' || !request('filter_by_created_at'))
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('send')->whereSuccess(1)->sum('amount');
                                                     }
                                                    ?>

                                                    {{$totalAmount}} {{$wallet->wallet_currency}}</div>
                                                @endforeach
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 grid-margin">
                                <div class="card bg-warning">
                                    <div class="card-body">
                                        <h3 class="text-white">Withdraw</h3>
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-xl-12 my-auto">
                                               <div class="row pricesBox2">
                                                @foreach($wallets as $wallet)

                                                <div class="col-6 mb-3">{{$wallet->wallet_name}}</div>
                                                <div class="col-6 mb-3">
                                                    <?php 
                                                     if(request('filter_by_created_at') == 'today')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->whereDate('created_at', $today)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'yesterday')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->whereDate('created_at', $yesterday)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'last_7_days')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->whereDate('created_at', '>=',$last_7_days)->whereDate('created_at', '<=',$today)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'last_30_days')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->whereDate('created_at', '>=',$last_30_days)->whereDate('created_at', '<=',$today)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'last_month')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->whereMonth('created_at', $last_month)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'this_month')
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->whereMonth('created_at', $this_month)->sum('amount');
                                                     }
                                                     elseif(request('filter_by_created_at') == 'all' || !request('filter_by_created_at'))
                                                     {
                                                        $totalAmount = \App\Models\AdminTransaction::where('wallet', $wallet->id)->whereType('draw')->whereSuccess(1)->sum('amount');
                                                     }
                                                    ?>

                                                    {{$totalAmount}} {{$wallet->wallet_currency}}</div>
                                                @endforeach
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center"> Username </th>
                                    <th class="text-center"> Wallet </th>
                                    <th class="text-center"> Type </th>
                                    <th class="text-center"> Amount Transfer </th>
                                    <th class="text-center"> Created at </th>
                                    
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('admin_transactions_search') }}" class="search-form" id="searchForm">
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="username" id="username" placeholder="username" value="{{ Request::get('username') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group form-select mb-0">
                                                    <select  class="form-control text-white" name="wallet" id="transaction_wallet" >
                                                    <option value="" >Wallet</option>
                                                    @foreach($wallets as $wall)
                                                    <option value="{{$wall->id}}"  @if(Request::get('wallet') == $wall->id) selected @endif>{{$wall->wallet_name}}</option>
                                                    @endforeach
                                                 </select>
                                            </div>
                                        </th>
                                        
                                        <th>
                                            <div class="form-group form-select mb-0">
                                                <select name="type" id="type" class="form-control form-select text-white">
                                                    <option value="" @if(Request::get('type')=='') selected @endif>All</option>
                                                    <option value="send" @if(Request::get('type')=='send') selected @endif>Send</option>
                                                    <option value="draw" @if(Request::get('type')=='draw') selected @endif>Withdraw</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th></th>
                                        <th>
                                           
                                        </th>
                                        <th class="text-center">
                                            <button class="btn btn-outline-success">
                                                <i class="mdi mdi-search-web"></i>
                                                search
                                            </button>
                                        </th>
                                    </form>
                                </tr>
                                @if(count($transfer) > 0)
                                    @foreach($transfer as $trans)
                                    <tr>
                                        <td class="text-center py-1">
                                            {{ $trans->user_name }}
                                        </td>
                                        <td class="text-center">
                                            {{  \App\Models\Wallet::find($trans->wallet)->wallet_name }}
                                        </td>
                                        <td class="text-center @if($trans->type == 'send') text-success @else text-warning @endif">
                                            {{$trans->type == 'draw' ? 'Withdraw' : 'Send'}}
                                        </td>
                                        <td class="text-center py-1">
                                            {{ $trans->amount }}
                                        </td>
                                        <td class="text-center py-1">
                                            {{date("d-m-Y", strtotime( $trans->created_at)) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td class="px-0" colspan="7">
                                            <div class="alert alert-danger bg-danger text-white py-4">
                                                <i class="mdi mdi-message"></i>
                                                <span class="mx-1">No Transfers to display</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="pagination-links">
                    {{ $transfer->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("#filter_by_created_at select").on('change',function(){
            $("#filter_by_created_at").submit();
        });
</script>
@endpush
