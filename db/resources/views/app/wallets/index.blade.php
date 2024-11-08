@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Wallets</span>
                </h4>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Wallets Information</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($wallets) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-human-male icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Wallets</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $live_wallet }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-human-male icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Live Wallets</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ $test_wallet }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-human-male icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Test Wallets</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">

                            <div class="row d-flex justify-content-end">

                                @role('system admin')
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check text-right">
                                        <label class="form-check-label">
                                            <a class="btn btn-outline-info" href="{{ route('wallets_add') }}" id="addNewWallet">
                                                <i class="mdi mdi-plus"></i>
                                                <span class="mx-1">
                                                    Add New Wallet
                                                </span>
                                            </a>
                                        </label>
                                    </div>
                                </div>
                                @endrole

                            </div>

                        

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th> Name (Currency) </th>
                                    <th> Quantity </th>
                                    <th> Test </th>
                                    <th> Transactions Count </th>
                                    <th> Transactions Users Count </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('wallets_search') }}" class="search-form" id="searchForm">
                                        
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="name" id="name" placeholder="Wallet name" value="{{ Request::get('name') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th></th>
                                        <th></th>
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
                                @if(count($wallets) > 0)
                                    @foreach($wallets as $wallet)
                                    <tr>
                                        <td>
                                            {{ $wallet->wallet_name }} ({{ $wallet->wallet_currency }})
                                        </td>
                                        <td class="text-center">
                                            {{ $wallet->price }}
                                        </td>                                        
                                        <td class="text-center">
                                            {{ $wallet->test_wallet == 1 ? 'Yes' : 'No' }}
                                        </td>

                                        <td class="text-center">
                                            {{ \App\Models\Transaction::where('wallet', $wallet->id)->count() }}
                                        </td>
                                        <td class="text-center">
                                            {{ \App\Models\Transaction::where('wallet', $wallet->id)->groupBy('sender_id')->get()->count()  }}
                                        </td>
                                        <td class="text-center">

                                            <a class="btn btn-outline-success dropdown-toggle" id="actionDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu py-0" aria-labelledby="actionDropdown">

                                                @if($wallet->test_wallet != 1)
                                                <a class="dropdown-item py-3" href="{{ route('show_wallet_transactions',$wallet->id) }}">
                                                    <i class="mdi mdi-account-edit text-info"></i>
                                                    <span class="mx-2">Wallet Transactions</span>
                                                </a>
                                                @endif
                                                @role('system admin')
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3" href="{{ route('wallets_edit',$wallet->id) }}">
                                                    <i class="mdi mdi-account-edit text-info"></i>
                                                    <span class="mx-2">Edit</span>
                                                </a>
                                                
                                                @endrole
                                               
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td class="px-0" colspan="7">
                                            <div class="alert alert-danger bg-danger text-white py-4">
                                                <i class="mdi mdi-message"></i>
                                                <span class="mx-1">No Wallets to display</span>
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
                    {{ $wallets->links() }}
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
            content: 'Do you want to delete this message ?',
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

        $(".suspend-item").confirm(function(){return true;},{
            title: 'Suspend Data!',
            content: 'Do you want to suspend this item ?',
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
                    title: 'Deactivate users!',
                    content: 'Are you want to deactivate selected users ?',
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
                                url : "{{ route('app_users_deactivate_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('app_users') }}";
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
                    content: 'You should select at least one user',
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
                    title: 'Delete users!',
                    content: 'Are you want to delete selected users ?',
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
                                url : "{{ route('app_users_delete_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('app_users') }}";
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
                    content: 'You should select at least one user',
                });
            }

        });

    });

</script>
@endpush
