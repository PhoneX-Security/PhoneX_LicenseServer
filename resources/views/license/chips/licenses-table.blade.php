{{--
Parameters: $licenses, $show_username, $show_issuer
--}}

@if($licenses && count($licenses) > 0)
    <table class="table table-condensed">
        <tr>
            <th>ID</th>
            <th>Expiration</th>
            <th>License type</th>
            <th>Product</th>

            @if(isset($show_username))
                <th>Username</th>
            @endif

            @if(isset($show_issuer))
                <th>Issuer</th>
            @endif

            <th>License code (code/group)</th>

            <th>Start date</th>
            <th>Expiration date</th>
            <th width="300">Comment</th>

            <th>Options</th>
        </tr>

    @foreach($licenses as $license)
        <tr>
            <td>{{ $license->id }}</td>
            <td>{{ ucfirst($license->licenseType->name) }} ({{ $license->licenseType->days }} days)</td>
            <td>{{ ucfirst($license->licenseFuncType->name) }}</td>
            <td>@if($license->product) {{$license->product->uc_name}} @endif</td>

            @if(isset($show_username))
                <td><a href="{{ \URL::route('users.show', $license->user_id)  }}" >{{ $license->user->username }}</a></td>
            @endif

            @if(isset($show_issuer))
                <td>@if ($license->issuer)
                        <a href="{{ \URL::route('users.show', $license->issuer_id)  }}" >{{ $license->issuer->username }}</a>
                    @else - @endif</td>
            @endif

            <td>
                @if($license->business_code_id)
                    {{$license->businessCode->printable_code }} /
                    {{ $license->businessCode->getGroup()->name or 'unknown-group' }}
                @else - @endif
            </td>

            <td>{{ $license->starts_at }}</td>
            <td>{{ $license->expires_at }}</td>

            <td>{{ $license->comment }}</td>
            <td>
                {{--class="text-center"--}}
                <div class="btn-group  btn-group-xs">
                    <a type="button" class="btn btn-info view-btn-edit" href="{{ route('licenses.edit', $license->id) }}" title="Edit"><i class="fa fa-pencil-square-o"></i> Edit</a>

                    <a type="button" class="btn btn-danger" href="#" title="Delete"
                       data-href="{{ route('licenses.delete', $license->id) }}" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i> Delete</a>
                </div>
            </td>
        </tr>
    @endforeach
    </table>

    @include('dialogs.license-delete')

@endif