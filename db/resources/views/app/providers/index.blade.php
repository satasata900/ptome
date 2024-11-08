@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Providers</span>
                </h4>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">App providers <a class="btn btn-info" style="float: right;" href="{{ route('send_notification_for_all_providers') }}"> <i class="mdi mdi-bell text-warning"></i> Send Notifications for all providers</a></h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($providers) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-human-male icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Providers</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($confirmedProviders) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-secondary">
                                                    <span class="mdi mdi-database icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Confirmed Providers</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($approvedProviders) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-database-export icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Approved Providers</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">

                            <div class="row d-flex justify-content-end">

                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check text-right">
                                        <label class="form-check-label">
                                            <a class="btn btn-outline-info" href="{{ route('providers_add') }}" id="addNewProvider">
                                                <i class="mdi mdi-plus"></i>
                                                <span class="mx-1">
                                                    Add new provider
                                                </span>
                                            </a>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="row d-flex justify-content-between">

                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="checkAll"> Check All <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Avatar </th>
                                    <th> username </th>
                                    <th> Provider name </th>
                                    <th> Mobile </th>
                                    <th> Approved </th>
                                    <th> Confirmed </th>
                                    <th> Action </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('providers_search') }}" class="search-form" id="searchForm">
                                        <th></th>
                                        <th></th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="username" id="username" placeholder="username" value="{{ Request::get('username') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="name" id="name" placeholder="Name" value="{{ Request::get('name') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="mobile" id="mobile" placeholder="mobile" value="{{ Request::get('mobile') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group form-select mb-0">
                                                <select name="approved" id="approved" class="form-control form-select text-white">
                                                    <option value="" @if(Request::get('approved')=='') selected @endif>All</option>
                                                    <option value="approved" @if(Request::get('approved')=='approved') selected @endif>Approved</option>
                                                    <option value="unapproved" @if(Request::get('approved')=='unapproved') selected @endif>Not approved</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group form-select mb-0">
                                                <select name="confirmed" id="confirmed" class="form-control form-select text-white">
                                                    <option value="" @if(Request::get('confirmed')=='') selected @endif>All</option>
                                                    <option value="confirmed" @if(Request::get('confirmed')=='confirmed') selected @endif>Confirmed</option>
                                                    <option value="unconfirmed" @if(Request::get('confirmed')=='unconfirmed') selected @endif>Not confirmed</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <button class="btn btn-outline-success">
                                                <i class="mdi mdi-search-web"></i>
                                                search
                                            </button>
                                        </th>
                                    </form>
                                </tr>
                                @if(count($providers) > 0)
                                    @foreach($providers as $provider)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" data-toggler="#checkAll" data-id="{{ $provider->id }}">
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center py-1">
                                            <img width="25" height="25" class="rounded-circle" src="{{ providerURL($provider->provider_image) }}" alt="{{ $provider->provider_name }}">
                                        </td>
                                        <td>
                                            {{ $provider->user->user_name }}
                                        </td>
                                        <td>
                                            {{ $provider->provider_name }}
                                        </td>
                                        <td>
                                            {{ $provider->provider_phone }}
                                        </td>
                                        <td class="text-center">
                                           @if($provider->approved_provider == 0)
                                                <span class="text-danger">No</span>
                                           @else
                                                <span class="text-success">yes</span>
                                           @endif
                                        </td>
                                        <td class="text-center">
                                            @if($provider->confirmed_provider == 0)
                                                <span class="text-danger">No</span>
                                            @else
                                                <span class="text-success">yes</span>
                                            @endif
                                        </td>
                                        <td class="text-center">

                                            <a class="btn btn-outline-success dropdown-toggle" id="actionDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu py-0" aria-labelledby="actionDropdown">
                                                <a class="dropdown-item py-3" href="{{ route('providers_edit',$provider->id) }}">
                                                    <i class="mdi mdi-account-edit text-info"></i>
                                                    <span class="mx-2">Edit</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                @if($provider->approved_provider == 0)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Approve provider ?" data-content="Do you want to approve this provider ?" href="{{ route('providers_approve_change',$provider->id) }}">
                                                    <i class="mdi mdi-check-circle text-success"></i>
                                                    <span class="mx-2">Approve</span>
                                                </a>
                                                @endif
                                                @if($provider->approved_provider == 1)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Disapprove provider ?" data-content="Do you want to disapprove this provider ?" href="{{ route('providers_approve_change',$provider->id) }}">
                                                    <i class="mdi mdi-close-circle text-warning"></i>
                                                    <span class="mx-2">Disapprove</span>
                                                </a>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                @if($provider->confirmed_provider == 0)
                                                    <a class="dropdown-item py-3 suspend-item" data-title="Confirm provider ?" data-content="Do you want to confirm this provider ?" href="{{ route('providers_confirm_change',$provider->id) }}">
                                                        <i class="mdi mdi-check-circle text-success"></i>
                                                        <span class="mx-2">Confirm</span>
                                                    </a>
                                                @endif
                                                @if($provider->confirmed_provider == 1)
                                                    <a class="dropdown-item py-3 suspend-item" data-title="Unconfirm provider ?" data-content="Do you want to unconfirm this provider ?" href="{{ route('providers_confirm_change',$provider->id) }}">
                                                        <i class="mdi mdi-close-circle text-warning"></i>
                                                        <span class="mx-2">Disconfirm</span>
                                                    </a>
                                                @endif

                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3" href="{{ route('app_users_send_notification',$provider->user_id) }}">
                                                    <i class="mdi mdi-bell text-warning"></i>
                                                    <span class="mx-2">Notification</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 delete-item" data-title="Delete provider ?" data-content="Do you want to delete this provider ?" href="{{ route('providers_delete',$provider->id) }}">
                                                    <i class="mdi mdi-trash-can text-danger"></i>
                                                    <span class="mx-2">Delete</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td class="px-0" colspan="7">
                                            <div class="alert alert-danger bg-danger text-white py-4">
                                                <i class="mdi mdi-message"></i>
                                                <span class="mx-1">No Providers to display</span>
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
                    {{ $providers->links() }}
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

    });

</script>
@endpush
