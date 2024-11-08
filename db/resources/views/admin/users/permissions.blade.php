@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('system_users') }}">Users</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>{{ $user->fullName }}</span>
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
                        <h4 class="card-title text-warning">User Roles</h4>
                        <p class="card-description"> Select user roles , then save changes </p>
                        @if(count($roles) > 0)
                        <form class="forms-sample mt-2" id="updateRolesForm" action="{{ route('system_users_roles_update',$user->id) }}" method="POST">
                            @csrf
                            <div class="row my-5">
                                <div class="col-12 mb-3">
                                    <div class="form-check form-check-flat form-check-info mt-0">
                                        <label class="form-check-label">
                                            <input type="checkbox" id="checkAllRoles" class="form-check-input check-all"> Check All </label>
                                    </div>
                                </div>
                                @foreach($roles as $role)
                                <div class="col-12">
                                    <div class="form-check form-check-flat form-check-info mt-0">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input roles-checkbox" name="{{ 'role-' . $role->id }}" value="{{ $role->id }}" @if(in_array($role->name,$userRoles)) checked @endif> {{ $role->name }} </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-success">Update Roles</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-warning">User Permissions</h4>
                        <p class="card-description"> Select user Permissions , then save changes </p>
                        @if(count($permissions) > 0)
                            <form class="forms-sample mt-2" id="updatePermissionsForm" action="{{ route('system_users_permissions_update',$user->id) }}" method="POST">
                                @csrf
                                <div class="row my-5">
                                    <div class="col-12 mb-3">
                                        <div class="form-check form-check-flat form-check-info mt-0">
                                            <label class="form-check-label">
                                                <input type="checkbox" id="checkAllPermissions" class="form-check-input check-all"> Check All </label>
                                        </div>
                                    </div>
                                    @foreach($permissions as $permission)
                                        <div class="col-sm-6 col-12">
                                            <div class="form-check form-check-flat form-check-info mt-0">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input permission-checkbox" name="{{ 'permission-' . $permission->id }}" value="{{ $permission->id }}" @if(in_array($permission->name,$userPermissions)) checked @endif> {{ $permission->name }} </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-success">Update Permissions</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){

        $("#checkAllRoles").change(function (){

            let inputs = $(".roles-checkbox");
            if($(this).is(":checked")){
                inputs.prop('checked',true);
            }
            else{
                inputs.prop('checked',false);
            }

        })

        $("#checkAllPermissions").change(function (){

            let inputs = $(".permission-checkbox");
            if($(this).is(":checked")){
                inputs.prop('checked',true);
            }
            else{
                inputs.prop('checked',false);
            }

        })

    });

</script>
@endpush
