@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('wallets') }}">Wallets</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>{{ $wallet->wallet_name }}</span>
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
                        <h4 class="card-title">Edit Wallet</h4>
                        <p class="card-description"> Complete wallet information and click  "Update" </p>

                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('wallets_update',$wallet->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="wallet_name">
                                            Wallet Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="wallet_name" id="wallet_name" class="form-control" placeholder="Enter wallet name"  value="{{ $wallet->wallet_name }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="wallet_currency">
                                            Currency Name
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="wallet_currency" id="wallet_currency" class="form-control no-space" placeholder="Enter wallet currency" value="{{ $wallet->wallet_currency }}">
                                    </div>
                                </div>

                                @if($wallet->test_wallet == 1)
                                <div class="col-md-12">
                                    <div class="form-group mb-4">
                                        <label for="price">
                                            Quantity
                                            <span class="text-danger mx-1">*</span>
                                        </label>
                                        <input type="text" name="price" id="price" class="form-control no-space" placeholder="Enter wallet price" value="{{ $wallet->price }}">
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="default_wallet">
                                            <input type="checkbox" value="1" name="default_wallet" id="default_wallet" class="form-control"  @if($wallet->default_wallet == 1) checked @endif >
                                            Set as default
                                        </label>
                                    </div>
                                </div>
                               <!--  <div class="col-md-5">
                                    <div class="form-check form-check-flat form-check-info mb-2">
                                        <label class="form-check-label" for="test_wallet">
                                            <input type="checkbox" value="1" name="test_wallet" id="test_wallet" class="form-control"  @if($wallet->test_wallet == 1) checked @endif >
                                            Set as Test
                                        </label>
                                    </div>
                                </div>
 -->
                            </div>

                            <button type="submit" class="btn btn-success mt-lg-5 mt-4">Update Wallet</button>

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

        const validator = $("#updateForm").validate({
            rules: {
                wallet_name: {
                    required: true,
                },
                wallet_currency: {
                    required: true,
                },
                price: {
                    required: true,
                    number : true
                }
            },
            messages: {
                wallet_name: {
                    required: "Wallet name is required",
                },
                wallet_currency: {
                    required: "Currency is required",
                },
                price: {
                    required: "Price is required",
                    number : "Price must be numeric"
                }
            }

        });

    });

</script>
@endpush
