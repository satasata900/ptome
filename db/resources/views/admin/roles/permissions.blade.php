@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('roles') }}">Roles</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Role permissions</span>
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
                        <h4 class="card-title">Role Name : <span class="text-warning mx-1">{{ $role->name }}</span></h4>
                        <p class="card-description"> Select role permissions , then save changes </p>
                        @if(count($permissions) > 0)
                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('roles_update_permissions',$role->id) }}" method="POST">
                            @csrf
                            <div class="row my-5">
                                <div class="col-12 mb-3">
                                    <div class="form-check form-check-flat form-check-info mt-0">
                                        <label class="form-check-label">
                                            <input type="checkbox" id="checkAll" class="form-check-input check-all"> Check All </label>
                                    </div>
                                </div>
                                @foreach($permissions as $permission)
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-check form-check-flat form-check-info mt-0">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input permission-checkbox check-all-controlled" data-parent="checkAll" name="{{ 'permission-' . $permission->id }}" value="{{ $permission->id }}" @if(in_array($permission->name,$rolePermissions)) checked @endif> {{ $permission->name }} </label>
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

        $("#checkAll").change(function (){

            let inputs = $(".check-all-controlled");
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
