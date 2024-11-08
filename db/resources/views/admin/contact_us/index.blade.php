@extends('layout.master')

@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Tickets</span>
                </h4>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Clients Tickets</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($messages) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-message icon-lg icon-item"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Total Tickets</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($readMessages) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-info">
                                                    <span class="mdi mdi-trash-can-outline icon-lg icon-item"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-info font-weight-normal">Open Tickets</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ count($unreadMessages) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-alert-box-outline icon-lg icon-item"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Closed Tickets</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-action-row mb-5">
                            <div class="row">
                                <!-- @if(count($messages)>0)
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" id="checkAll"> Check All <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <a class="text-danger" href="#" id="deleteSelected">
                                                <i class="mdi mdi-trash-can"></i>
                                                <span class="mx-1">Delete selected</span>
                                            </a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <a class="text-danger" href="#" id="deleteAll">
                                                <i class="mdi mdi-trash-can"></i>
                                                <span class="mx-1">Delete All</span>
                                            </a>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <div class="col-lg-3 col-sm-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <a class="text-success" href="#" id="markSelectedAsRead">
                                                <i class="mdi mdi-message-bulleted"></i>
                                                <span class="mx-1">Mark selected as read</span>
                                            </a>
                                        </label>
                                    </div>
                                </div> -->
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th> Username </th>
                                    <th> State </th>
                                    <th> Creation Time </th>
                                    <th> Action </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <form method="GET" action="{{ route('contact_us_search') }}" class="search-form" id="searchForm">
                                        <th>
                                            <div class="form-group mb-0">
                                                <input type="text" class="form-control text-white" name="username" id="username" placeholder="username" value="{{ Request::get('username') }}">
                                                <i class="mdi mdi-close clear-input-btn"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <!-- <select name="state" id="state" class="form-select form-control text-white">
                                                <option class="py-2" value="" @if(Request::get('state') == '') selected @endif>All</option>
                                                <option class="py-2" value="opened" @if(Request::get('state') == "opened") selected @endif>Open</option>
                                                <option class="py-2" value="closed" @if(Request::get('state') == "closed") selected @endif>Closed</option>
                                            </select> -->
                                        </th>
                                        <th></th>
                                        <th class="text-center">
                                            <button class="btn btn-outline-success">
                                                search
                                            </button>
                                        </th>
                                    </form>
                                </tr>
                                @if(count($messages) > 0)
                                    @foreach($messages as $message)
                                    <tr>
                                        <td> {{ $message->user->user_name }} </td>
                                        <td class="{{ $message->state == 'opened' ? 'text-success' : 'text-warning' }} "> {{ $message->state }} </td>
                                        <td > {{ $message->creationTime }} </td>
                                        <td >

                                            <a class="btn btn-outline-success dropdown-toggle" id="actionDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu py-0" aria-labelledby="actionDropdown">
                                                <a class="dropdown-item py-3" href="{{ route('contact_us_view',$message->id) }}">
                                                    <i class="mdi mdi-eye-circle text-info"></i>
                                                    <span class="mx-2">View</span>
                                                </a>
                                                @if($message->state == "opened")
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 change-state-item" data-title="Change state" data-content="Do you want to change message state ?" href="{{ route('contact_us_state_change', $message->id) }}" data-id="{{ $message->id }}">
                                                    <i class="mdi mdi-eye-circle text-warning"></i>
                                                    <span class="mx-2">closed</span>
                                                </a>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item py-3 delete-item" data-title="Delete Message ?" data-content="Do you want to delete this message ?" href="{{ route('contact_us_delete',$message->id) }}">
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
                                                <span class="mx-1">No Messages to display</span>
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
                    {{ $messages->links() }}
                </div>
            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){

        const loader =  $("#loader");

        $(".change-state-item").confirm(function(){return true}, {
            title: 'Confirm!',
            content: 'Are you sure to change the state ?',
            type: 'green',
            buttons: {
                confirm: function () {return true},
                cancel: function () {}
            }
        });

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

        $("#deleteAll").confirm({
            title: 'Delete Data!',
            content: 'Do you want to delete messages ?',
            type: 'red',
            buttons: {
                confirm: function () {
                    window.location.href = "{{ route('contact_us_delete_all') }}";
                },
                cancel: function () {
                }
            }
        });

        $("#markSelectedAsRead").click(function (e){
            let inputs = $("input").filter(function(el){
                return $(this).attr("data-toggler") === "#checkAll" && $(this).is(":checked");
            });
            console.log(inputs);
            if(inputs.length && inputs.length > 0){
                $.confirm({
                    title: 'Change Data state!',
                    content: 'Do you want to change selected messages state ?',
                    type: 'yellow',
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
                                url : "{{ route('contact_us_change_state_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('contact_us') }}";
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
                    content: 'You should select one or more items',
                });
            }

        });

        $("#deleteSelected").click(function (e){
            let inputs = $("input").filter(function(el){
                return $(this).attr("data-toggler") === "#checkAll" && $(this).is(":checked");
            });
            if(inputs.length && inputs.length > 0){
                $.confirm({
                    title: 'Delete Data!',
                    content: 'Are you want to delete selected messages ?',
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
                                url : "{{ route('contact_us_delete_selected') }}",
                                data : {
                                    "ids" : data_ids_array
                                },
                                success : function(msg){
                                    loader.css('display', 'none');
                                    window.location.href = "{{ route('contact_us') }}";
                                }
                            });
                        },
                        cancel: function () {
                        }
                    }
                });
            }

            else{
                console.log($(this))
                e.preventDefault();
                $.alert({
                    title: 'Alert!',
                    content: 'You should select one or more items',
                });
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
