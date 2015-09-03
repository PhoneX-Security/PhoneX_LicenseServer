@extends('content-with-header')

@section('title', 'Trial requests')

@section('content')
    @parent

    <section class="content">

        @include('errors.notifications')

        <div class="box">
            <div class="box-header">
                <div class="box-title">

                </div>
                <div class="box-tools">
                    <a class="btn btn-sm btn-default" data-href="#" data-toggle="modal" data-target="#modal-users-reset-trial-counter-by-imei" href="#">Reset trial counter</a>
                </div>

            </div>

            <div class="box-body">

                @if($requests)
                    <table class="table table-condensed">
                        <tr>
                            <th>ID</th>
                            <th>Date created</th>
                            <th>Username</th>
                            <th>Captcha</th>
                            <th>Hashed IMEI</th>
                            <th>IP</th>
                            <th>Is approved</th>
                        </tr>
                        @foreach($requests as $req)
                            <tr>
                                <td>{{$req->id}}</td>
                                <td>{{$req->dateCreated}}</td>
                                <td>{{$req->username}}</td>
                                <td>{{$req->captcha}}</td>
                                <td>{{$req->imei}}</td>
                                <td>{{$req->ip}}</td>
                                <td>@if($req->isApproved==1) Yes @else No @endif</td>
                            </tr>
                        @endforeach
                    </table>
                    <p>Total: {{ $requests->count() }}</p>
                @else
                    No trial requests
                @endif

            </div>

            @include('dialogs.users-reset-trial-counter-by-imei')
        </div>
    </section>
@endsection