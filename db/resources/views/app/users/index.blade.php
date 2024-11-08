@extends('layout.master')

<style type="text/css">
    .mySelect
    {
        width: 100%;
        padding: 8px;
    }
</style>
@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-9 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>App Users</span>
                </h4>
            </div>
            <div class="col-3" >
                <form method="GET" action="{{ route('app_users') }}" id="filter_by_date">
                    <select class="mb-3 mySelect" name="filter_by_date" >
                        <option value="all">filter by register date ...</option>
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
                        <h4 class="card-title">Users Information
                            <a class="btn btn-info" style="float: right;" href="{{ route('send_notification_for_all_users') }}"> <i class="mdi mdi-bell text-warning"></i> Send Notifications for all users</a>
                        </h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($users) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-human-male icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Users</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($activeUsers) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-secondary">
                                                    <span class="mdi mdi-eye-check icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Active Users</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($inActiveUsers) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-eye-off-outline icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Deactivated Users</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">

                            <div class="row d-flex justify-content-end">

                                

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
                                    <div class="form-check text-center">
                                        <label class="form-check-label">
                                            <a class="text-danger" href="#" id="deactivateSelected">
                                                <i class="mdi mdi-minus-circle"></i>
                                                <span class="mx-1">
                                                    Deactivate Selected
                                                </span>
                                            </a>
                                        </label>
                                    </div>
                                </div>

                              

                            </div> -->

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <!-- <th> # </th> -->
                                    <th> Full name </th>
                                    <th> Username </th>
                                    <th> Email </th>
                                    <th> Registered Date </th>
                                    <th> Active </th>
                                    <th> Action </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('app_users_search') }}" class="search-form" id="searchForm">
                                        <!-- <th></th> -->
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white " name="fullname" id="fullname" placeholder="Full name" value="{{ Request::get('fullname') }}">
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
                                                <input type="text" class="form-control text-white" name="email" id="email" placeholder="Email Address" value="{{ Request::get('email') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                           
                                        </th>
                                        <th>
                                            <div class="select-group mb-0">
                                                <select name="state" id="active" class="form-control form-select text-white">
                                                    <option class="py-2" value="" @if(!Request::get('state')) selected @endif>All</option>
                                                    <option class="py-2"  value="active" @if(Request::get('state') == "active") selected @endif>Active</option>
                                                    <option class="py-2"  value="inactive" @if(Request::get('state') == "inactive") selected @endif>Not Active</option>
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
                                @if(count($users) > 0)
                                    @foreach($users as $user)
                                    <tr>
                                        <!-- <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" data-toggler="#checkAll" data-id="{{ $user->id }}">
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </td> -->
                                        <td class="text-center py-1">
                                            {{ $user->full_name }}
                                        </td>
                                        <td>
                                            {{ $user->user_name }}
                                        </td>
                                        <td>
                                            {{ $user->email }}
                                        </td>
                                        <td class="text-center py-1">
                                            {{ $user->registration_Time }}
                                        </td>
                                        <td class="text-center">
                                           @if($user->active == 1)
                                                <span class="text-success">Yes</span>
                                           @else
                                                <span class="text-danger">No</span>
                                           @endif
                                        </td>
                                        <td class="text-center">

                                            <a class="btn btn-outline-success dropdown-toggle" id="actionDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu py-0" aria-labelledby="actionDropdown">
                                                <a class="dropdown-item py-3" href="{{ route('app_users_view',$user->id) }}">
                                                    <i class="mdi mdi-eye-check text-info"></i>
                                                    <span class="mx-2">View</span>
                                                </a>
                                                
                                                <div class="dropdown-divider"></div>
                                                @if($user->active == 1)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Deactivate user ?" data-content="Do you want to deactivate this user ?" href="{{ route('app_users_deactivate',$user->id) }}">
                                                    <i class="mdi mdi-minus-circle text-danger"></i>
                                                    <span class="mx-2">Deactivate</span>
                                                </a>
                                                @endif
                                                @if($user->active == 0)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Activate user ?" data-content="Do you want to activate this user ?" href="{{ route('app_users_activate',$user->id) }}">
                                                    <i class="mdi mdi-shield-account-outline text-warning"></i>
                                                    <span class="mx-2">Activate</span>
                                                </a>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 suspend-item" data-title="Delete user ?" data-content="Do you want to delete this user ?" href="{{ route('app_users_delete',$user->id) }}">
                                                    <i class="mdi mdi-trash-can text-danger"></i>
                                                    <span class="mx-2">Delete</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3" href="{{ route('app_users_send_notification',$user->id) }}">
                                                    <i class="mdi mdi-bell text-warning"></i>
                                                    <span class="mx-2">Notification</span>
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
                                                <span class="mx-1">No Users to display</span>
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
                   {{ $users->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){

        $("#filter_by_date .mySelect").on('change',function(){
            $("#filter_by_date").submit();
        });

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
