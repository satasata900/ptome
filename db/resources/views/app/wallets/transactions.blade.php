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
</style>
@section('content')

    <div class="content-wrapper">

        <div class="row">
            <div class="col-9" ></div>
            <div class="col-3" >
                <form method="GET" action="{{ route('show_wallet_transactions', $wallet->id) }}" id="filter_by_date">
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
        </div>

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('wallets') }}">Wallets</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Transactions of ( {{ $wallet->wallet_name }} )</span>
                </h4>
            </div>

       

            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>Total amount of Type</h4>
                                <h5>Username : <strong class="text text-info">{{$totalAmountUsername}} {{ $wallet->wallet_currency }}</strong></h5>
                                <h5>Group : <strong class="text text-info">{{$totalAmountGroup}} {{ $wallet->wallet_currency }}</strong></h5>
                                <h5>Transaction Code : <strong class="text text-info">{{$totalAmountTransCode}} {{ $wallet->wallet_currency }}</strong></h5>
                                <h5>Service : <strong class="text text-info">{{$totalAmountService}} {{ $wallet->wallet_currency }}</strong></h5>
                                <h5>Trade : <strong class="text text-info">{{$totalAmountTrade}} {{ $wallet->wallet_currency }}</strong></h5>
                                <h5>Invoice : <strong class="text text-info">{{$totalAmountInvoice}} {{ $wallet->wallet_currency }}</strong></h5>
                            </div>
                            <div class="col-md-4">
                                 <h3>Total Amount : <strong class="text text-danger">{{ $totalAmountUsername + $totalAmountGroup + $totalAmountTransCode + $totalAmountService + $totalAmountTrade + $totalAmountInvoice }} {{ $wallet->wallet_currency }}</strong></h3>
                            </div>

                        </div>

                           

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
                                
                                @if(count($transArray) > 0)
                                    @foreach($transArray as $org)
                                    
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

    </div>

@endsection

@push('scripts')

<script type="text/javascript">
   

    $("#filter_by_date select").on('change',function(){
        $("#filter_by_date").submit();
    });

</script>

@endpush 