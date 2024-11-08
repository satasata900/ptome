@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('users_wallets') }}">Users Wallets</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>New User Wallet</span>
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
                        <h4 class="card-title">New user wallet</h4>
                        <p class="card-description"> Complete user and wallet information and click  "ADD" </p>

                        <form class="forms-sample mt-4" id="createForm" action="{{ route('users_wallets_store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="wallet_name">Wallet name</label>
                                        <select class="js-example-basic-single form-control forms-select" style="width:100%" name="wallet_name" id="wallet_name">
                                            @foreach($wallets as $wallet)
                                                <option value="{{ $wallet->id }}">{{ $wallet->wallet_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="owner_name">Owner name</label>
                                        <select class="js-example-basic-single form-control forms-select" style="width:100%" name="owner_name" id="owner_name">
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="price">
                                            Balance
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="price" id="price" class="form-control no-space" placeholder="Wallet balance" value="0">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="hidden_wallet">
                                            <input type="checkbox" name="hidden_wallet" id="hidden_wallet" class="form-control">
                                            Set as Hidden
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Add Wallet</button>

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
                wallet_name: {
                    required: true,
                },
                owner_name: {
                    required: true,
                },
                price: {
                    required: true,
                    number : true
                }
            },
            messages: {
                wallet_name: {
                    required: "Wallet is required",
                },
                owner_name: {
                    required: "Owner is required",
                },
                price: {
                    required: "Balance is required",
                    number : "Balance must be numeric"
                }
            }

        });

        $('.js-example-basic-single').select2();

    });

</script>
@endpush
