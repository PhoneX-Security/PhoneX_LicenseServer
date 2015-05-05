{{--
Parameters: $user
--}}

@if($user->subscriber && count($user->subscriber->subscribersInContactList) > 0)
    <table class="table table-condensed">
        <tr>
            <th>Username</th>
            <th>Options</th>
        </tr>
        @foreach($user->subscriber->subscribersInContactList as $subscriberContact)
            <tr>
                <td>
                    @if($subscriberContact->user)
                        <a href="{{ \URL::route('users.show', [ $subscriberContact->user->id ]) }}">{{ $subscriberContact->user->username }}</a>
                    @else
                        {{ $subscriberContact->username }} (corrupted! - missing record in License Server DB)
                    @endif
                </td>

                <td>
                    <div class="btn-group  btn-group-xs">
                        {{--<a type="button" class="btn btn-info   view-btn-edit" href="{{ \URL::route('licenses.edit', $license->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>--}}
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
@else
    Empty contact list
@endif