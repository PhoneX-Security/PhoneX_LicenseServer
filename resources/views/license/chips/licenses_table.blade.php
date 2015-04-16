{{--
Parameters: $licenses, $show_username, $show_issuer
--}}

@if($licenses && count($licenses) > 0)
    <table class="table table-condensed">
        <tr>
            <th>Expiration</th>
            <th>License type</th>

            @if(isset($show_username))
                <th>Username</th>
            @endif

            @if(isset($show_issuer))
                <th>Issuer</th>
            @endif

            <th>Active</th>
            <th>Start date</th>
            <th>Expiration date</th>
            <th width="300">Comment</th>

            <th>Options</th>
        </tr>
        @foreach($licenses as $license)
            <tr>
                <td>{{ ucfirst($license->licenseType->name) }} ({{ $license->licenseType->days }} days)</td>
                <td>{{ ucfirst($license->licenseFuncType->name) }}</td>

                @if(isset($show_username))
                    <td><a href="{{ \URL::route('users.show', $license->user_id)  }}" >{{ $license->user->username }}</a></td>
                @endif

                @if(isset($show_issuer))
                <td>@if ($license->issuer)
                        <a href="{{ \URL::route('users.show', $license->issuer_id)  }}" >{{ $license->issuer->username }}</a>
                    @else - @endif</td>
                @endif

                <td>@if($license->active) Yes @else No @endif</td>
                <td>{{ date_simple($license->starts_at) }}</td>
                <td>{{ date_simple($license->expires_at) }}</td>
                <td>{{ $license->comment }}</td>
                <td>
                    {{--class="text-center"--}}
                    <div class="btn-group  btn-group-xs">
                        <a type="button" class="btn btn-info   view-btn-edit" href="{{ \URL::route('licenses.edit', $license->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
@else
    No licenses
@endif