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
                    <span>Edit Role</span>
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
                        <h4 class="card-title">Edit Role</h4>
                        <p class="card-description"> Complete role information and click  "Update" </p>
                        <form class="forms-sample mt-4" id="updateForm" action="{{ route('roles_update',$role->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="roleName">
                                    Role Name
                                    <span class="text-danger mx-1">*</span>
                                </label>
                                <input type="text" name="name" id="roleName" value="{{ $role->name }}" class="form-control" placeholder="Enter Role Name">
                            </div>
                            <div class="form-group mb-4">
                                <label for="guardName">
                                    Guard Name
                                    <span class="text-danger mx-1">*</span>
                                </label>
                                <select class="form-control" name="guard_name" id="guardName" value="{{ $role->name }}">
                                    <option value="web">Web</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Update Role</button>
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
                name: {
                    required: true,
                },
                guard_name: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Role Name is required",
                },
                guard_name: {
                    required: "Guard Name is required"
                },
            },
            errorPlacement: function(error, element){
                error.insertAfter(element).fadeIn(500);
            }

        });

    });

</script>
@endpush
