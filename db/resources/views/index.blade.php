@extends('layout.master')
<style type="text/css">
    .mySelect
    {
        width: 100%;
        padding: 8px;
    }
    .styleStat
    {
        background: #424242;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px #959595;
    }
    .styleStat small
    {
        font-size: 13px;
    }
    .myFirst
    {
        background-color: #0090e7  !important;
    }
    .myFirst .text-primary
    {
        color: #071c93 !important;
    }
    .mySecond
    {
        background-color: #00d25b !important;
    }
    .mySecond .icon-box-primary
    {
        background-color: #0d8541 !important;
        color: #fff !important;
    }
    .myThird
    {
        background-color: #00d15c !important;
    }
    .myThird.user
    {
        background-color: #fc424a  !important;
    }
    .myFourth
    {
        background-color: #0b2093 !important;
    }
    .myFifth
    {
        background-color: #030d44 !important;
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
            <div class="col-9" ></div>
            <div class="col-3" >
                <form method="GET" action="{{ route('dashboard') }}" id="filter_by_date">
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
            
            <div class="col-sm-12 styleStat">
                
            
        
        
                <div class="row">


                    <div class="col-sm-12">
                        <h4> Sum Prices of each wallet <small>(users wallets prices)</small> </h4>
                    </div>

                    @foreach($wallets as $wallet)
                        <div class="col-sm-3 grid-margin">
                            <div class="card myFirst">
                                <div class="card-body">
                                    <h5>Sum Prices of ({{ $wallet->wallet_name }})</h5>
                                    <div class="row">
                                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                                            <div class="d-flex d-sm-block d-md-flex align-items-center">
                                                <h2 class="mb-0">{{ \App\Models\UserWallet::where('wallet_id', $wallet->id)->sum('price') }}</h2>
                                            </div>
                                        </div>
                                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                            <i class="icon-lg mdi mdi-codepen text-primary ml-auto"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach


                    <div class="col-sm-12 mt-3">
                        <h4> Count Transactions of Wallet </h4>
                    </div>
                    @foreach($wallets as $wallet)
                    <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                        <div class="card mySecond">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0">

                                                <?php
                                                   $transWallet = \App\Models\Transaction::where('wallet', $wallet->id)->get();
                                                    $count_of_wallet_transactions = 0;
                                                    $sum_amount_transactions = 0;
                                                    foreach($transWallet as $data)
                                                    {
                                                        $date = date('Y-m-d', strtotime($data->creationTime));
                                                        $dateMonth = date('m', strtotime($data->creationTime));
                                                        if(request('filter_by_date') == 'today')
                                                        {
                                                            if($date == $today)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                        elseif(request('filter_by_date') == 'yesterday')
                                                        {
                                                            if($date == $yesterday)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                        elseif(request('filter_by_date') == 'last_7_days')
                                                        {
                                                            if($date >= $last_7_days && $date <= $today)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                        elseif(request('filter_by_date') == 'last_30_days')
                                                        {
                                                            if($date >= $last_30_days && $date <= $today)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                        elseif(request('filter_by_date') == 'last_month')
                                                        {
                                                            if($dateMonth == $last_month)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                        elseif(request('filter_by_date') == 'this_month')
                                                        {
                                                            if($dateMonth == $this_month)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            if($date == $today)
                                                            {
                                                                $count_of_wallet_transactions +=1;
                                                                $sum_amount_transactions +=$data->amount;
                                                            }
                                                        }
                                                    }

                                                ?>
                                                {{ $count_of_wallet_transactions }} 
                                                <small>Transactions</small></h3>
                                        </div>
                                        <h3 class="text text-white">{{$sum_amount_transactions}} <small>Volume</small></h3>
                                    </div>
                                    <div class="col-3">
                                        <div class="icon icon-box-primary">
                                            <span class="">{{$wallet->wallet_currency}}</span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-muted font-weight-normal">Wallet {{ $wallet->wallet_name }}</h6>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    


                    <div class="col-sm-12 mt-3">
                        <h4> Other Statistics </h4>
                    </div>

                    <div class="col-sm-6 grid-margin">
                        <div class="card myThird">
                            <div class="card-body">
                                <h5><a style="color:#fff" href="{{ route('transaction') }}">Count of Transactions</a></h5>
                                <div class="row">
                                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                                            <h2 class="mb-0">{{ $count_of_transactions }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                        <i class="icon-lg mdi mdi-codepen text-white ml-auto"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 grid-margin">
                        <div class="card myThird user">
                            <div class="card-body">
                                <h5><a style="color:#fff" href="{{ route('app_users') }}">Count of User Registered</a></h5>
                                <div class="row">
                                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                                            <h2 class="mb-0">{{ $count_of_users }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                        <i class="icon-lg mdi mdi-human-male  text-white ml-auto"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card myFourth">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0">{{$count_of_traders}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="icon icon-box-success ">
                                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-muted font-weight-normal"><a style="color:#fff" href="{{ route('trader') }}">Count Of Traders</a></h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card myFourth">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0">{{$count_of_providers}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="icon icon-box-success ">
                                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-muted font-weight-normal"><a style="color:#fff" href="{{ route('providers') }}">Count Of Providers</a></h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card myFourth">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0">{{$count_of_services}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="icon icon-box-success ">
                                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-muted font-weight-normal"><a style="color:#fff" href="{{ route('services') }}">Count Of Services</a></h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                        <div class="card myFourth">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex align-items-center align-self-start">
                                            <h3 class="mb-0">{{$count_of_traders_services}}</h3>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="icon icon-box-success ">
                                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-muted font-weight-normal">Count Of Traders Services</h6>
                            </div>
                        </div>
                    </div>
                   
                </div>


                <div class="row">
                   
                    <div class="col-sm-4 grid-margin">
                        <div class="card myFifth">
                            <div class="card-body">
                                <h5><a style="color:#fff" href="{{ route('cities') }}">Count of Unactive Cities</a></h5>
                                <div class="row">
                                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                                            <h2 class="mb-0">{{ $unactive_cities }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                        <i class="icon-lg mdi mdi-map  text-info ml-auto"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 grid-margin">
                        <div class="card myFifth">
                            <div class="card-body">
                                <h5><a style="color:#fff" href="{{ route('fields') }}">Count of Unactive Fields</a></h5>
                                <div class="row">
                                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                                            <h2 class="mb-0">{{ $unactive_fields }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                        <i class="icon-lg mdi mdi-security  text-warning ml-auto"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-4 grid-margin">
                        <div class="card myFifth">
                            <div class="card-body">
                                <h5><a style="color:#fff" href="{{ route('services') }}">Count of Disapproved Services</a></h5>
                                <div class="row">
                                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                                            <h2 class="mb-0">{{ $disapproved_services }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                                        <i class="icon-lg mdi mdi-security  text-danger ml-auto"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            

        </div>
      
    </div>

@endsection
 
@push('scripts')

<script type="text/javascript">
   

    $("select").on('change',function(){
        $("#filter_by_date").submit();
    });

  // <!--  function filter_by_date(value)
  //   {
  //          $.ajaxSetup({
  //               headers: {
  //                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //               }
  //           });
  //           $.ajax({
  //               type: "post",
  //               cache: false,
  //               url: "{{ route('dashboard') }}",
  //               data:  {value:value},
  //               beforeSend:function(){
                    
  //               },
  //               success:function(response){
                    
                    
                    
  //               },
  //               error:function(xhr){
                  

  //               }
  //           });
  //   }
    -->
</script>

@endpush 