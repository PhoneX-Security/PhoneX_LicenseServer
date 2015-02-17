@if($licenses && count($licenses) > 0)
    <table class="table table-condensed">
        <tr>
            <th>License type</th>
            <th>Issuer</th>

            <th>Trial</th>
            <th>Active</th>
            <th>Start date</th>
            <th>Expiration date</th>
            <th width="300">Comment</th>

            <th>Options</th>
        </tr>
        @foreach($licenses as $license)
            <tr>
                <td>{{ ucfirst($license->licenseType->name) }} ({{ $license->licenseType->days }} days)</td>
                <td>@if ($license->issuer)
                        <a href="{{ \URL::route('users.show', $license->issuer_id)  }}" >{{ $license->issuer->username }}</a>
                    @else - @endif</td>
                <td>@if($license->licenseType->is_trial) Yes @else No @endif</td>
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