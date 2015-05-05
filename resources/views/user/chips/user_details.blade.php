{{--
Parameters: $user
--}}

<div class="row">
    <div class="col-md-6 ">
        <dl>
            <dt>Username</dt>
            <dd>{{ $user->username }}</dd>
        </dl>
    </div>
    <div class="col-md-6 ">
        <dl>
            <dt>Email Address</dt>
            <dd>{{ $user->email or '-'}}</dd>
        </dl>
    </div>
</div>

<div class="row">
    <div class="col-md-6 ">
        <dl>
            <dt>Has access</dt>
            <dd>@if ($user->has_access) Yes @else No @endif</dd>
        </dl>
    </div>
</div>

<div class="row">
    <div class="col-md-6 ">
        </dl>
        <dt>Date created</dt>
        <dd>{{ date_simple($user->created_at) }}</dd>
        </dl>
    </div>
    <div class="col-md-6 ">
        <dl>
            <dt>Date updated</dt>
            <dd>{{ date_simple($user->updated_at) }}</dd>
        </dl>
    </div>
</div>

<h4>SIP details</h4>
@if($user->subscriber)
    <div class="row">
        <div class="col-md-4 ">
            <dl>
                <dt>First authentication</dt>
                <dd>{{ $user->subscriber->date_first_authCheck or '-'}}</dd>
            </dl>
        </div>

        <div class="col-md-4 ">
            <dl>
                <dt>First cert generation</dt>
                <dd>{{ $user->subscriber->date_first_login or '-'}}</dd>
            </dl>
        </div>

        <div class="col-md-4 ">
            <dl>
                <dt>First user added</dt>
                <dd>{{ $user->subscriber->date_first_user_added or '-'}}</dd>
            </dl>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 ">
            <dl>
                <dt>Last activity</dt>
                <dd>{{ $user->subscriber->date_last_activity or '-'}}</dd>
            </dl>
        </div>

        <div class="col-md-4 ">
            <dl>
                <dt>Last authentication</dt>
                <dd>{{ $user->subscriber->date_last_authCheck or '-'}}</dd>
            </dl>
        </div>

        <div class="col-md-4 ">
            <dl>
                <dt>Last password changed</dt>
                <dd>{{ $user->subscriber->date_last_pass_change or '-'}}</dd>
            </dl>
        </div>
    </div>

    {{--Feature especially for Dusan--}}
    @if(strlen($user->subscriber->ha1)<32)
        <div class="alert alert-danger" role="alert">
            <span class="fa fa-exclamation-triangle" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            User has corrupted password! Workaround: reset password.
        </div>
    @endif

@else
    User has no SIP license issued yet, therefore no details are available
@endif