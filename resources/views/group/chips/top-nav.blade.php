<ul class="nav nav-tabs">
    <li role="presentation" @if(Route::is('groups.show')) class="active" @endif><a href="{{ route('groups.show', [ $group->id ]) }}">Details</a></li>
    <li role="presentation" @if(Route::is('groups.users')) class="active" @endif><a href="{{ route('groups.users', [ $group->id ]) }}">Users</a></li>
    <li role="presentation" @if(Route::is('groups.bcodes')) class="active" @endif><a href="{{ route('groups.bcodes', [ $group->id ]) }}">Business codes</a></li>
</ul>