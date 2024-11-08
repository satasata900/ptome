@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Roles</span>
                </h4>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Roles</h4>
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
                                        <h6 class="text-success font-weight-normal">System Users</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($roles) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-secondary">
                                                    <span class="mdi mdi-database icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Roles</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($permissions) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-database-export icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Permissions</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">
                            <div class="row d-flex justify-content-between">
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="checkAll"> Check All <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check text-right">
                                        <label class="form-check-label">
                                            <a class="btn btn-outline-info" href="{{ route('roles_add') }}" id="addNewRole">
                                                <i class="mdi mdi-plus"></i>
                                                <span class="mx-1">
                                                    Add New Role
                                                </span>
                                            </a>
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
                                    <th> ID </th>
                                    <th> Name </th>
                                    <th> Guard </th>
                                    <th> Creation Time </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('roles_search') }}" class="search-form" id="searchForm">
                                        <th></th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white numeric-input no-paste" name="id" id="id" placeholder="ID Number" value="{{ Request::get('id') }}">
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
                                        </th>
                                        <th></th>
                                        <th class="text-center">
                                            <button class="btn btn-outline-success">
                                                <i class="mdi mdi-search-web"></i>
                                                search
                                            </button>
                                        </th>
                                    </form>
                                </tr>
                                @if(count($roles) > 0)
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" data-toggler="#checkAll" data-id="{{ $role->id }}">
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="py-1">
                                            {{ $role->id }}
                                        </td>
                                        <td> {{ $role->name }} </td>
                                        <td>
                                            {{ $role->guard_name }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($role->created_at)->diffForHumans() }}
                                        </td>
                                        <td class="text-center">

                                            <a class="btn btn-outline-success dropdown-toggle" id="actionDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu py-0" aria-labelledby="actionDropdown">
                                                <a class="dropdown-item py-3" href="{{ route('roles_edit',$role->id) }}">
                                                    <i class="mdi mdi-account-edit text-info"></i>
                                                    <span class="mx-2">Edit</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3" href="{{ route('roles_edit_permissions',$role->id) }}">
                                                    <i class="mdi mdi-database-check text-info"></i>
                                                    <span class="mx-2">Permissions</span>
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 delete-item" data-title="Delete Role ?" data-content="Do you want to delete this role ?" href="{{ route('roles_delete',$role->id) }}">
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
                                                <span class="mx-1">No Roles to display</span>
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
                    {{ $roles->links() }}
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
