@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('admin_transactions') }}">App Transfer Money</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>New Transfer</span>
                </h4>
            </div>

            @if($errors->any())

            <div class="col-12 grid-margin stretch-card">

                <div class="crud-error bg-danger text-white">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>
                                <i class="mdi mdi-close-circle-outline"></i>
                                <span class="mx-2">{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            @endif

            <div class="col-md-12 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title">New transfer</h4>
                        <p class="card-description"> Complete transfer information and click  "ADD" </p>

                        <form class="forms-sample mt-4" id="createForm" action="{{ route('admin_transactions_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_ar">
                                            Username
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="user_name" id="user_name" class="form-control no-space" placeholder="Username" value="{{ old('user_name') }}">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="name_en">
                                            Wallet
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <select  class="form-control text-white" name="wallet" id="transaction_wallet" >
                                            <option value="" >Wallet</option>
                                            @foreach($wallets as $wall)
                                            <option value="{{$wall->id}}">{{$wall->wallet_name}}</option>
                                            @endforeach
                                         </select>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group mb-4">
                                        <label for="type">
                                            Type
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <select  class="form-control text-white" name="type" id="type" >
                                            <option value="send">Send</option>
                                            <option value="draw">Withdraw</option>
                                         </select>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="user_name">
                                            Amount
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="number" min="0" name="amount" id="amount" class="form-control no-space" placeholder="Amount" value="{{ old('amount') }}">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <button type="submit" id="send_or_draw" class="btn btn-success mt-lg-5 mt-4">Send Money</button>

                                </div>
                            </div> 

                            
                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){

        const validator = $("#createForm").validate({
            rules: {
                user_name: {
                    required: true,
                },
                wallet: {
                    required: true,
                },
                type: {
                    required: true,
                },
                amount: {
                    required: true,
                }
            },
            messages: {
                user_name: {
                    required: "Username is required",
                },
                wallet: {
                    required: "Wallet is required",
                },
                type: {
                    required: "Type is required",
                },
                amount: {
                    required: "Amount is required",
                },
            }

        });

        $("#type").on('change',function(){
            var val = $(this).val();
            if(val == 'send')
            {
                $("#send_or_draw").html('Send Money');
                $("#send_or_draw").addClass('btn-success');
                $("#send_or_draw").removeClass('btn-danger');
            }
            else if(val == 'draw')
            {
                $("#send_or_draw").html('Withdraw Money');
                $("#send_or_draw").addClass('btn-danger');
                $("#send_or_draw").removeClass('btn-success');
            }
        });

    });

</script>
@endpush
