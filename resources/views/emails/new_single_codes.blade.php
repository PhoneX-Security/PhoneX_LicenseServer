<h2>Generated codes</h2>

<p>
    Parent user: @if($parent) {{ $parent->username  }} @else - @endif <br />
    Group: @if($group) {{ $group->name  }} @else - @endif
</p>

<table>
    <tr>
        <td>Code</td>
    </tr>
    @foreach($codes as $code)
        <tr>
            <td>{{ $code->printable_code }}</td>
        </tr>
    @endforeach
</table>

