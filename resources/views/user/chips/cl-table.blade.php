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
                        <a type="button" class="btn btn-danger" href="#" title="Delete"
                           data-href="/users/{{$user->id}}/cl/delete/{{$subscriberContact->user->id}}" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i> Delete</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
    <p>Total: {{ $user->subscriber->subscribersInContactList->count() }}</p>
@else
    Empty contact list
@endif

@include('dialogs.confirm_delete')


<hr />

{{--<h4>Add contact</h4>--}}

{{--<form class="form-inline">--}}
{{--{!! \Form::open(['method' => 'patch', 'route' => ['users.add_user_to_cl', $user->id], 'class'=>'form-inline']) !!}--}}
    {{--<div class="form-group">--}}
        {{--<label class="inline-form-right-margin" for="exampleInputName1">Username</label>--}}
        {{--<input type="text" class="form-control inline-form-side-margin" id="exampleInputName1" name="username">--}}
        {{--<label class="inline-form-side-margin" for="exampleInputName2">Alias</label>--}}
        {{--<input type="text" class="form-control inline-form-side-margin" id="exampleInputName2" name="alias">--}}
        {{--<label>--}}
            {{--<input type="checkbox" name="mutually"> Mutually--}}
        {{--</label>--}}
    {{--</div>--}}
    {{--<button type="submit" class="btn btn-primary">Add</button>--}}
{{--</form>--}}
{{--{!! \Form::close() !!}--}}
