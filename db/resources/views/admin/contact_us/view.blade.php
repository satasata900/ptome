@extends('layout.master')
<style type="text/css">
    .message
    {
        margin-bottom: 10px;
        padding: 5px 20px;
        border-radius: 6px;
    }
    .message img 
    {
        width: 100%;
        height: 100%;
        margin-right: 4px;
    }
    .message .span
    {
        font-size: 15px;
    }
    .message .h5
    {
        margin-top: 10px;
    }
</style>
@section('content')

    <div class="content-wrapper">

        <div class="row">

            <div class="col-12 grid-margin stretch-card">
                <h4>
                    <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <a class="text-white" href="{{ route('contact_us') }}">Messages</a>
                    <i class="mdi mdi-chevron-double-right mx-1"></i>
                    <span>Message Details</span>
                </h4>
            </div>

          <!--   <div class="col-lg-12 grid-margin stretch-card">

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Message information</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ date('d-m-Y' , strtotime($message->creationTime)) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-success">
                                                    <span class="mdi mdi-calendar icon-lg icon-item"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-success font-weight-normal">Message Date</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">
                                                        <span class="d-inline-block mr-2">{{ date('H : i' , strtotime($message->creationTime)) }}</span>
                                                        <span class="text-danger">{{ date('A' , strtotime($message->creationTime)) }}</span>
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-info">
                                                    <span class="mdi mdi-clock icon-lg icon-item"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="text-info font-weight-normal">Message Time</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-9">
                                                <div class="d-flex align-items-center align-self-start">
                                                    <h3 class="mb-0">{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="icon icon-box-warning">
                                                    <span class="mdi mdi-clock-digital icon-lg icon-item"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h6 class="font-weight-normal text-warning">Since</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Sender information</h4>
                        <div class="row mt-4 mb-2">
                            <div class="col-12 grid-margin stretch-card">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-info">
                                                <i class="mdi mdi-human mr-2"></i>
                                                Full Name :
                                            </div>
                                            <div class="col-sm-8">
                                              <a href="{{ route('app_users_view', $message->user->id) }}">  {{ $message->user->full_name }} </a>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-info">
                                                <i class="mdi mdi-human mr-2"></i>
                                                User Name :
                                            </div>
                                            <div class="col-sm-8">
                                                <a href="{{ route('app_users_view', $message->user->id) }}">{{ $message->user->user_name }}</a>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-info">
                                                <i class="mdi mdi-email mr-2"></i>
                                                Email Address :
                                            </div>
                                            <div class="col-sm-8">
                                                {{ $message->user->email }}
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-info">
                                                <i class="mdi mdi-phone mr-2"></i>
                                                Mobile Number :
                                            </div>
                                            <div class="col-sm-8">
                                                {{ $message->user->phone }}
                                            </div>
                                        </div>


                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Messages

                            @if($message->state == "opened")
                            <a class=" btn btn-danger" style="float:right;" href="{{ route('contact_us_state_change', $message->id) }}">
                                <i class="mdi mdi-eye-circle text-white"></i>
                                <span class="mx-2">close this ticket</span>
                            </a>
                            @endif
                        </h4>
                        <div class="row mt-4 mb-5">
                            
                            @foreach($allMessages as $m)
                            <div class="col-12">
                                <div class="row message" @if($m->sender == 1) style="background: #3971eb;" @else style="background: #ca2e91;" @endif>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-1">
                                                <img src="{{ asset('dashboard/img/user.png') }}" >
                                            </div>
                                            <div class="col-md-11">
                                                <span class="span">
                                                    {{ $m->sender == 1 ? 'admin' : $message->user->user_name }}
                                                </span>
                                                <small class="ml-3 badge badge-dark float-right">{{ $m->created_at }}</small>
                                                <h4 class="h5">{{ $m->message }}</h4>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            @endforeach


                            <div class="col-12 mt-5">
                                <h4 class="card-title">Reply</h4>
                            </div>
                            <div class="col-12">
                                <form class="w-100" action="{{ route('contact_us_reply') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="ticket_id" value="{{$message->id}}">
                                    <textarea name="message" id="message" class="form-control text-white p-4" rows="8" placeholder="reply...." required></textarea>
                                    <button type="submit" class="btn btn-success rounded p-2 mt-3">
                                        Post reply
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection

@push('scripts')

<script>

    $(document).ready(function(){
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
    });

</script>
@endpush
