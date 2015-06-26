<ul class="nav nav-tabs">
    <li role="presentation" class="active"><a href="{{ \URL::route('users.show', [ $user->id ]) }}">Details</a></li>
    <li role="presentation"><a href="{{ \URL::route('users.licenses', [ $user->id ]) }}">Licenses</a></li>
    <li role="presentation"><a href="{{ \URL::route('users.cl', [ $user->id ]) }}">Contact List</a></li>
</ul>