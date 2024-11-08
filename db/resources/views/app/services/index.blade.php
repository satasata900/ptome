@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Services</span>
                </h4>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Services</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{$allServices}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-human-male icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Services</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{$activeServices}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-secondary">
                                                    <span class="mdi mdi-eye-check icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Active Services</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{$unActiveServices}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-eye-off-outline icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Deactivated Services</h6>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                               
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{$approvedServices}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-secondary">
                                                    <span class="mdi mdi-eye-check icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Approved Services</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{$disApprovedServices}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-eye-off-outline icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">DisApproved Services</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">

                            <div class="row d-flex justify-content-end">

                              <!--   <div class="col-lg-3 col-sm-4">
                                    <div class="form-check text-right">
                                        <label class="form-check-label">
                                            <a class="btn btn-outline-info" href="{{ route('services_add') }}" id="addNewService">
                                                <i class="mdi mdi-plus"></i>
                                                <span class="mx-1">
                                                    Add new service
                                                </span>
                                            </a>
                                        </label>
                                    </div>
                                </div>
 -->
                            </div>

                            <div class="row d-flex justify-content-between my-2 px-4">

                             <!--    <div class="col-lg-3 col-sm-4 my-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="checkAll"> Check All <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div> -->

                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center"> ID </th>
                                    <th class="text-center"> Service name ar </th>
                                    <th class="text-center"> Service name en </th>
                                     <th class="text-center"> Active </th>
                                     <th class="text-center"> Approved </th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- <tr>
                                    <form method="GET" action="{{ route('users_wallets_search') }}" class="search-form" id="searchForm">
                                        <th></th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white numeric-input no-paste" name="id" id="id" placeholder="ID Number" value="{{ Request::get('id') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="email" id="email" placeholder="Owner email" value="{{ Request::get('email') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="name" id="name" placeholder="Owner name" value="{{ Request::get('name') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="wallet_name" id="wallet_name" placeholder="Wallet name" value="{{ Request::get('wallet_name') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th></th>
                                        <th class="text-center">
                                            <button class="btn btn-outline-success">
                                                <i class="mdi mdi-search-web"></i>
                                                search
                                            </button>
                                        </th>
                                    </form>
                                </tr> -->
                                @if(count($services) > 0)
                                    @foreach($services as $service)
                                    <tr>
                                        <td class="text-center py-1">
                                            {{ $service->id }}
                                        </td>
                                        <td class="text-center py-1">
                                            {{ $service->service_name_ar }}
                                        </td>
                                        <td class="text-center">
                                            {{ $service->service_name_en }}
                                        </td>

                                        <td class="text-center">
                                           @if($service->active == 1)
                                                <span class="text-success">Yes</span>
                                           @else
                                                <span class="text-danger">No</span>
                                           @endif
                                        </td>

                                        <td class=" text-center">
                                           @if($service->approved == 1)
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
                                                <a class="dropdown-item py-3" href="{{ route('services_edit',$service->id) }}">
                                                    <i class="mdi mdi-account-edit text-info"></i>
                                                    <span class="mx-2">View</span>
                                                </a>

                                                @if($service->active == 1)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Deactivate service ?" data-content="Do you want to deactivate this service ?" href="{{ route('services_deactivate',$service->id) }}">
                                                    <i class="mdi mdi-minus-circle text-danger"></i>
                                                    <span class="mx-2">Deactivate</span>
                                                </a>
                                                @endif
                                                @if($service->active == 0)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Activate service ?" data-content="Do you want to activate this service ?" href="{{ route('services_activate',$service->id) }}">
                                                    <i class="mdi mdi-shield-account-outline text-warning"></i>
                                                    <span class="mx-2">Activate</span>
                                                </a>
                                                @endif


                                                @if($service->approved == 1)
                                                <a class="dropdown-item py-3 suspend-item" data-title="Disapproved service ?" data-content="Do you want to Disapproved this service ?" href="{{ route('services_disapproved',$service->id) }}">
                                                    <i class="mdi mdi-minus-circle text-danger"></i>
                                                    <span class="mx-2">Disapproved</span>
                                                </a>
                                                @endif
                                                @if($service->approved == 0)
                                                <a class="dropdown-item py-3 suspend-item" data-title="approved service ?" data-content="Do you want to approved this service ?" href="{{ route('services_approved',$service->id) }}">
                                                    <i class="mdi mdi-shield-account-outline text-warning"></i>
                                                    <span class="mx-2">Approved</span>
                                                </a>
                                                @endif

                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3" href="{{ route('send_notification_for_all_members_in_service',$service->id) }}">
                                                    <i class="mdi mdi-bell text-warning"></i>
                                                    <span class="mx-2">Send Notifications</span>
                                                </a>

                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 delete-item" data-title="Delete wallet ?" data-content="Do you want to delete this wallet ?" href="{{ route('services_delete',$service->id) }}">
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
                    {{ $services->links() }}
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
