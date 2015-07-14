{{--
Parameters: $users
--}}

@if($users && $users->count() > 0)
    <table class="table table-condensed  table-striped">

        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>E-mail</th>
            <th>SIP - Last activity</th>
            <th>Phone / Version</th>
            <th>Location</th>
            <th>Roles</th>
            <th class="text-center">Options</th>
        </tr>
        </thead>

        <tbody>
        @foreach($users as $user)
            <tr>

                <td>{{ $user->id }}</td>
                <td>
                    <a href="{{ \URL::route('users.show', [ $user->id ]) }}">{{ $user->username }}</a>
                </td>
                <td>{{ $user->email or '' }}</td>
                <td>
                    @foreach($user->groups as $k => $group)
                        @if($k > 0), @endif
                        {{ $group->name }}
                    @endforeach
                </td>

                <td>@if($user->subscriber) {{ $user->subscriber->date_last_activity }} @endif</td>
                <td>
                    @if($user->subscriber && $user->subscriber->app_version)
                        {{$user->subscriber->app_version_obj->platformDesc() . " / " . $user->subscriber->app_version_obj->versionDesc()}}
                    @endif
                </td>
                <td>
                    @if($user->subscriber && $user->subscriber->location)
                        {{$user->subscriber->location['country']}}
                        @if($user->subscriber->location['city'])
                            {{ ", " . $user->subscriber->location['city'] }}
                        @endif
                    @endif
                </td>

                <td class="text-center">
                    <div class="btn-group  btn-group-xs">
                        <a class="btn btn-info view-btn-edit" href="{{ route('users.show', $user->id) }}" title="Details"><i class="fa fa-pencil-square-o"></i> Details</a>
                        <a class="btn btn-info view-btn-edit" href="{{ route('users.licenses', $user->id) }}" title="Licenses"><i class="fa fa-book"></i> Licenses</a>
                        <a class="btn btn-info view-btn-edit" href="{{ route('users.cl', $user->id) }}" title="Contact List"><i class="fa fa-list-alt"></i> Contact List</a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p>Total: {{ $user->count() }}</p>
@else
    No users
@endif