@if($withUsers)
    [<span class="link-expand clickable">+</span>]
    <span style="display:none" class="expandable">
        <br />
        @foreach($users as $user)
            <a href="{{route('users.show', $user->id)}}">{{$user->username}}</a>
            @if($user !== end($users)), @endif
        @endforeach
        <br />
    </span>
@endif