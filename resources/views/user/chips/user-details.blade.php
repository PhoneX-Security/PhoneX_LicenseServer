{{--
Parameters: $user
--}}

<h4>User details</h4>

<div class="row">
    <div class="col-md-4 ">
        <dl>
            <dt>Username</dt>
            <dd>{{ $user->username }}</dd>
        </dl>
    </div>
    <div class="col-md-4 ">
        <dl>
            <dt>Email Address</dt>
            <dd>{{ $user->email or '-'}}</dd>
        </dl>
    </div>
</div>

<div class="row">
    <div class="col-md-4 ">
        <dl>
            <dt>Has access</dt>
            <dd>@if ($user->has_access) Yes @else No @endif</dd>
        </dl>
    </div>
    <div class="col-md-4 ">
        </dl>
            <dt>Date created</dt>
            <dd>{{ date_simple($user->dateCreated) }}</dd>
        </dl>
    </div>
    <div class="col-md-4 ">
        <dl>
            <dt>Date updated</dt>
            <dd>{{ date_simple($user->dateUpdated) }}</dd>
        </dl>
    </div>
</div>

<div class="row">
    <div class="col-md-4 ">
        </dl>
        <dt><abbr title="Account creation is limited by IMEI">Successful trial account request</abbr></dt>
        <dd>@if($user->successful_trial_request) Yes @else No @endif</dd>
        </dl>
    </div>
</div>



@if($user->comment)
    <div class="row">
        <div class="col-md-4 ">
            </dl>
                <dt>Comment</dt>
                <dd>{{ $user->comment }}</dd>
            </dl>
        </div>
    </div>
@endif

<h4>Login details</h4>

@if($user->subscriber)
<div class="row">
    @if($user->subscriber->location)
        <div class="col-md-4 ">
            <dl>
            <dt>Location</dt>
            <dd>{!! $user->subscriber->formatted_location !!}</dd>
            </dl>
        </div>
    @endif
    @if($user->subscriber->app_version)
    <div class="col-md-4 ">
        </dl>
        <dt>Phone</dt>
        <dd>{{ $user->subscriber->app_version_obj->platformDesc() }}</dd>
        </dl>
    </div>
    <div class="col-md-4 ">
        <dl>
            <dt>Application</dt>
            <dd>{{ $user->subscriber->app_version_obj->versionDesc() }}</dd>
        </dl>
    </div>
    @endif

</div>


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