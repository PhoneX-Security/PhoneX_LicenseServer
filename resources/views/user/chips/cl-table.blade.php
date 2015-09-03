{{--
Parameters: $user
--}}

@if($user->subscriber && count($user->subscriber->subscribersInContactList) > 0)
    <table class="table table-condensed">
        <tr>
            <th>Username</th>
            <th>Alias</th>
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
                <td>{{ $subscriberContact->pivot->displayName }}</td>
                <td>
                    <div class="btn-group  btn-group-xs">
                        @if($subscriberContact->user)
                        <a type="button" class="btn btn-danger" href="#" title="Delete"
                           data-href="/users/{{$user->id}}/cl/delete/{{$subscriberContact->user->id}}" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i> Delete</a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
    <p>Total: {{ $user->subscriber->subscribersInContactList->count() }}</p>
@else
    Empty contact list
@endif

@include('dialogs.contact-delete')

<hr />
