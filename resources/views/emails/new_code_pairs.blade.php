<h2>Generated code pairs</h2>

<p>
    Parent user: @if($parent) {{ $parent->username  }} @else -non- @endif <br />
    Group: @if($group) {{ $group->name  }} @else -non- @endif
</p>

<table>
    <tr>
        <td>Code 1</td>
        <td>Code 2</td>
    </tr>
    @foreach($bcodes as $k=>$bcode)
        @if($k % 2 == 0) <tr><td>{{ $bcode->code }}</td>
        @else <td>{{ $bcode->code }}</td></tr>
        @endif
    @endforeach
</table>

