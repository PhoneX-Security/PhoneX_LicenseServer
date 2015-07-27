<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th><abbr data-toggle="tooltip" data-placement="top" title="Users who are created with associated business codes are automatically paired as contacts in their contact lists. They are also known as 'code pairs'.">Associated code(s)</abbr></th>
        <th>Expires at</th>
        <th>Group</th>
        <th>Parent user</th>
        <th>Users limit</th>
        <th>Users created</th>
        <th class="text-center">Options</th>
    </tr>
    </thead>
    <tbody>
    @foreach($bcodes as $bcode)
        <tr>
            <td>{{ $bcode->id }}</td>
            <td>{{ $bcode->printable_code }}</td>
            <td>
                @if($bcode->clMappings)
                    @foreach($bcode->clMappings as $k => $assocCode)
                        {{ $assocCode->printable_code }}
                        @if($k != 0), @endif
                    @endforeach
                @else -
                @endif </td>
            </td>
            <td>@if($bcode->getExpiresAt()) {{ $bcode->getExpiresAt() }} @endif</td>
            <td>{{ $bcode->getGroup()->name or ''}}</td>
            <td>{{ $bcode->getParent()->username or ''}}</td>
            <td>{{ $bcode->users->count() . '/' . $bcode->users_limit  }}</td>
            <td>@if($bcode->users)
                    @foreach($bcode->users as $k => $user)
                        <a href="{{route('users.show',$user->id)}}">{{ $user->username }}</a>
                        @if($k != 0), @endif
                    @endforeach
                @else -
                @endif </td>
            <td class="text-center">
                <div class="btn-group  btn-group-xs">
                    {{--<a type="button" class="btn btn-info view-btn-edit" href="{{ \URL::route('users.edit', $user->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>--}}
                    {{--<a type="button" class="btn btn-danger action_confirm   view-btn-delete" data-method="delete" href="#" title="Delete user"><i class="fa fa-times-circle-o"></i></a>--}}
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>