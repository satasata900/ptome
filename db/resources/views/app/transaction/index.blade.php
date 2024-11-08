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
    .mySelect
    {
        width: 100%;
        padding: 8px;
    }
    .pricesBox
    {
        background:#673ab7;
        padding: 10px 0 0;
        border-radius: 5px;
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

            <div class="col-9 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Transactions</span>
                </h4>
            </div>
            <div class="col-3" >
                <form method="GET" action="{{ route('transaction') }}" id="filter_by_date">
                    <select class="mb-3 mySelect" name="filter_by_date" >
                        <option value="today" @if(request('filter_by_date') == 'today') selected @endif >Today</option>
                        <option value="yesterday"  @if(request('filter_by_date') == 'yesterday') selected @endif >Yesterday</option>
                        <option value="last_7_days"  @if(request('filter_by_date') == 'last_7_days') selected @endif >Last 7 Days</option>
                        <option value="last_30_days"  @if(request('filter_by_date') == 'last_30_days') selected @endif >Last 30 Days</option>
                        <option value="last_month"  @if(request('filter_by_date') == 'last_month') selected @endif >Last Month</option>
                        <option value="this_month"  @if(request('filter_by_date') == 'this_month') selected @endif >This Month</option>
                    </select>
                </form>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">App Transactions Type Count with Amount of each wallet</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-12 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $transaction_username }}</h3>
                                                </div>
                                                <h6 class="font-weight-normal">Type Username</h6>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-wallet icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                            @include('app.transaction_types_includes.username')
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $transaction_group }}</h3>
                                                </div>
                                                <h6 class="font-weight-normal">Type Group</h6>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-wallet icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                            @include('app.transaction_types_includes.group')
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $transaction_transaction_code }}</h3>
                                                </div>
                                                <h6 class="font-weight-normal">Type Transaction code</h6>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-wallet icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @include('app.transaction_types_includes.transaction_code')
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $transaction_service }}</h3>
                                                </div>
                                                <h6 class="font-weight-normal">Type Service</h6>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-wallet icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @include('app.transaction_types_includes.service')
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $transaction_trade }}</h3>
                                                </div>
                                                <h6 class="font-weight-normal">Type Trade</h6>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-wallet icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @include('app.transaction_types_includes.trade')
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $transaction_invoice }}</h3>
                                                </div>
                                                <h6 class="font-weight-normal">Type Invoice</h6>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-wallet icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        @include('app.transaction_types_includes.invoice')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">

                            <div class="row d-flex justify-content-end">

                               <!--  <div class="col-lg-3 col-sm-4">
                                    <div class="form-check text-right">
                                        <label class="form-check-label">
                                            <a class="btn btn-outline-info" href="{{ route('fields_add') }}" id="addNewField">
                                                <i class="mdi mdi-plus"></i>
                                                <span class="mx-1">
                                                    Add new field
                                                </span>
                                            </a>
                                        </label>
                                    </div>
                                </div> -->

                            </div>

                            <!-- <div class="row d-flex justify-content-between my-2 px-4">

                                <div class="col-lg-3 col-sm-4 my-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="checkAll"> Check All <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-4 my-4">
                                    <a id="deactivateSelected" href="{{ route('fields_deactivate_selected') }}" class="text-warning">
                                        <i class="mdi mdi-minus-circle"></i>
                                        <span class="mx-1">Deactivate selected</span>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-sm-4 my-4">
                                    <a id="deleteSelected" href="{{ route('fields_deactivate_selected') }}" class="text-danger">
                                        <i class="mdi mdi-trash-can"></i>
                                        <span class="mx-1">Delete selected</span>
                                    </a>
                                </div>

                            </div> -->

                        </div>

                        <div class="table-responsivee">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class=""> Transaction number </th>
                                    <th class=""> Transaction Type </th>
                                    <th class=""> Wallet</th>
                                    <th class=""> Amount</th>
                                    <th class=""> CreationTime</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('transaction') }}" class="search-form" id="searchForm">
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="transaction_number" id="transaction_number" placeholder="Transaction number" value="{{ Request::get('transaction_number') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <select  class="form-control text-white" name="transaction_type" id="transaction_type" >
                                                    <option value="" >Transaction Type</option>
                                                    <option value="username" @if(Request::get('transaction_type') == 'username') selected @endif>Username</option>
                                                    <option value="group" @if(Request::get('transaction_type') == 'group') selected @endif>Group</option>
                                                    <option value="transaction_code" @if(Request::get('transaction_type') == 'transaction_code') selected @endif>Transaction code</option>
                                                    <option value="service" @if(Request::get('transaction_type') == 'service') selected @endif>Service</option>
                                                    <option value="trade" @if(Request::get('transaction_type') == 'trade') selected @endif>Trade</option>
                                                    <option value="invoice" @if(Request::get('transaction_type') == 'invoice') selected @endif>Invoice</option>
                                                </select>
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                             <select  class="form-control text-white" name="wallet" id="transaction_wallet" >
                                                <option value="" >Wallet</option>
                                                @foreach($wallets as $wall)
                                                <option value="{{$wall->id}}"  @if(Request::get('wallet') == $wall->id) selected @endif>{{$wall->wallet_name}}</option>
                                                @endforeach
                                             </select>
                                        </th>
                                        <th></th>
                                        <th></th>

                                        <th class="text-center">
                                            <button class="btn btn-outline-success">
                                                <i class="mdi mdi-search-web"></i>
                                                search
                                            </button>
                                        </th>
                                    </form>
                                </tr>
                                @if(count($transaction) > 0)
                                    @foreach($transaction as $org)
                                    
                                    <tr onclick="window.location.href='{{ route("transaction_view",$org->id) }}'" class="tr">
                                  
                                        <td class=" py-1">
                                            {{ $org->transaction_number }}
                                        </td>
                                        <td class="">
                                            {{ $org->transaction_type }}
                                            @if($org->transaction_type == 'username')
                                            <span class="text text-success">( {{ \App\Models\User::find($org->recipient_id)->user_name }} )</span>
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
                                        <td></td>
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
                    </div>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="pagination-links">
                    {{ $transaction->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){

        const loader =  $("#loader");

        $(".delete-item").confirm(function(){return true;},{
            title: 'Delete Data!',
            content: 'Do you want to delete this field ?',
            type: 'red',
            buttons: {
                confirm: function () {
                    return true;
                },
                cancel: function () {
                    return true;
                }
            }
        });

        $("#checkAll").change(function (){
            let inputs = $("input").filter(function(el){
                return $(this).attr("data-toggler") === "#checkAll";
            });
            if($(this).is(":checked")){
                $.each(inputs,function(){
                    $(this).prop("checked",true);
                });
            }
            else{
                $.each(inputs,function(){
                    $(this).prop("checked",false);
                });
            }
        });

        $("#deactivateSelected").click(function (e){
            e.preventDefault();
            let inputs = $("input").filter(function(el){
                return $(this).attr("data-toggler") === "#checkAll" && $(this).is(":checked");
            });
            if(inputs.length && inputs.length > 0){
                $.confirm({
                    title: 'Deactivate fields!',
                    content: 'Are you want to deactivate selected fields ?',
                    type: 'red',
                    buttons: {
                        confirm: function () {
                            loader.css('display','flex');
                            let data_ids_array = [];
                            let inputs = $("input").filter(function(el){
                                return $(this).attr("data-toggler") === "#checkAll" && $(this).is(":checked");
                            });
                            $.each(inputs,function(){
                                data_ids_array.push($(this).attr("data-id"));
                            });
                            $.ajax({
                                type: 'GET',
                                url : "{{ route('fields_deactivate_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('fields') }}";
                                }
                            });
                        },
                        cancel: function () {
                        }
                    }
                });
            }

            else{
                e.preventDefault();
                $.alert({
                    title: 'Alert!',
                    content: 'You should select at least one field',
                });
            }

        });

        $("#deleteSelected").click(function (e){
            e.preventDefault();
            let inputs = $("input").filter(function(el){
                return $(this).attr("data-toggler") === "#checkAll" && $(this).is(":checked");
            });
            if(inputs.length && inputs.length > 0){
                $.confirm({
                    title: 'Delete Field!',
                    content: 'Are you want to delete selected fields ?',
                    type: 'red',
                    buttons: {
                        confirm: function () {
                            loader.css('display','flex');
                            let data_ids_array = [];
                            let inputs = $("input").filter(function(el){
                                return $(this).attr("data-toggler") === "#checkAll" && $(this).is(":checked");
                            });
                            $.each(inputs,function(){
                                data_ids_array.push($(this).attr("data-id"));
                            });
                            $.ajax({
                                type: 'GET',
                                url : "{{ route('fields_delete_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('fields') }}";
                                }
                            });
                        },
                        cancel: function () {
                        }
                    }
                });
            }

            else{
                e.preventDefault();
                $.alert({
                    title: 'Alert!',
                    content: 'You should select at least one field',
                });
            }

        });

        $("#filter_by_date select").on('change',function(){
            $("#filter_by_date").submit();
        });

    });



</script>
@endpush
