@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Cities</span>
                </h4>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">App Cities</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($cities) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-map-marker icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Cities</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($activeCities) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-secondary">
                                                    <span class="mdi mdi-eye-check icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-secondary font-weight-normal">Active Cities</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($inactiveCities) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-eye-off-outline icon-lg icon-item" style="font-size:3rem"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Deactivated Cities</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-2">

                            <div class="row d-flex justify-content-end">

                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check text-right">
                                        <label class="form-check-label">
                                            <a class="btn btn-outline-info" href="{{ route('cities_add') }}" id="addNewService">
                                                <i class="mdi mdi-plus"></i>
                                                <span class="mx-1">
                                                    Add new city
                                                </span>
                                            </a>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="row d-flex justify-content-between my-2 px-4">

                                <div class="col-lg-3 col-sm-4 my-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="checkAll"> Check All <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-sm-4 my-4">
                                    <a id="deactivateSelected" href="{{ route('cities_deactivate_selected') }}" class="text-warning">
                                        <i class="mdi mdi-minus-circle"></i>
                                        <span class="mx-1">Deactivate selected</span>
                                    </a>
                                </div>

                                <div class="col-lg-3 col-sm-4 my-4">
                                    <a id="deleteSelected" href="{{ route('cities_deactivate_selected') }}" class="text-danger">
                                        <i class="mdi mdi-trash-can"></i>
                                        <span class="mx-1">Delete selected</span>
                                    </a>
                                </div>

                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center"> # </th>
                                    <th class="text-center"> ID </th>
                                    <th class="text-center"> Name AR </th>
                                    <th class="text-center"> Name EN </th>
                                    <th class="text-center"> Active </th>
                                    <th class="text-center"> Action </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('cities_search') }}" class="search-form" id="searchForm">
                                        <th></th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white numeric-input no-paste" name="id" id="id" placeholder="ID Number" value="{{ Request::get('id') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="name_ar" id="name_ar" placeholder="Arabic name" value="{{ Request::get('name_ar') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="name_en" id="name_en" placeholder="English name" value="{{ Request::get('name_en') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group form-select mb-0">
                                                <select name="active" id="active" class="form-control form-select text-white">
                                                    <option value="" @if(Request::get('active')=='') selected @endif>All</option>
                                                    <option value="active" @if(Request::get('active')=='active') selected @endif>Active</option>
                                                    <option value="inactive" @if(Request::get('active')=='inactive') selected @endif>Not Active</option>
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
                                @if(count($cities) > 0)
                                    @foreach($cities as $city)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" data-toggler="#checkAll" data-id="{{ $city->id }}">
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center py-1">
                                            {{ $city->id }}
                                        </td>
                                        <td class="text-center py-1">
                                            {{ $city->city_ar_name }}
                                        </td>
                                        <td class="text-center">
                                            {{ $city->city_en_name }}
                                        </td>
                                        <td class="text-center @if($city->active == 1) text-success @else text-danger @endif">
                                            @if($city->active == 1) yes @else No @endif
                                        </td>
                                        <td class="text-center">

                                            <a class="btn btn-outline-success dropdown-toggle" id="actionDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu py-0" aria-labelledby="actionDropdown">
                                                <a class="dropdown-item py-3" href="{{ route('cities_edit',$city->id) }}">
                                                    <i class="mdi mdi-account-edit text-info"></i>
                                                    <span class="mx-2">Edit</span>
                                                </a>
                                                <a class="dropdown-item py-3" href="{{ route('cities_state',$city->id) }}">
                                                    @if($city->active == 1)
                                                        <i class="mdi mdi-minus-circle text-warning"></i>
                                                        <span class="mx-2">Deactivate</span>
                                                    @elseif($city->active == 0)
                                                        <i class="mdi mdi-play-pause text-success"></i>
                                                        <span class="mx-2">Activate</span>
                                                    @endif
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 delete-item" data-title="Delete city ?" data-content="Do you want to delete this city ?" href="{{ route('cities_delete',$city->id) }}">
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
                                                <span class="mx-1">No Cities to display</span>
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
                    {{ $cities->links() }}
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
            content: 'Do you want to delete this City ?',
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
                    title: 'Deactivate cities!',
                    content: 'Are you want to deactivate selected cities ?',
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
                                url : "{{ route('cities_deactivate_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('cities') }}";
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
                    content: 'You should select at least one city',
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
                    title: 'Delete City!',
                    content: 'Are you want to delete selected cities ?',
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
                                url : "{{ route('cities_delete_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('cities') }}";
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
                    content: 'You should select at least one city',
                });
            }

        });

    });

</script>
@endpush
