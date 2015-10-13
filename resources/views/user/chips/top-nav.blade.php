<ul class="nav nav-tabs">
    <li role="presentation" @if(Route::is('users.show')) class="active" @endif ><a href="{{ route('users.show', [ $user->id ]) }}">Details</a></li>
    <li role="presentation" @if(Route::is('users.licenses')) class="active" @endif><a href="{{ route('users.licenses', [ $user->id ]) }}">Licenses</a></li>
    <li role="presentation" @if(Route::is('users.cl')) class="active" @endif><a href="{{ route('users.cl', [ $user->id ]) }}">Contact List</a></li>
    <li role="presentation" @if(Route::is('users.stats')) class="active" @endif><a href="{{ route('users.stats', [ $user->id ]) }}">Activity statistics</a></li>
    <li role="presentation" @if(Route::is('users.error-reports')) class="active" @endif><a href="{{ route('users.error-reports', [ $user->id ]) }}">Error reports</a></li>
    <li role="presentation" @if(Route::is('users.reg-stats')) class="active" @endif><a href="{{ route('users.reg-stats', [ $user->id ]) }}">Registration monitor</a></li>
</ul>