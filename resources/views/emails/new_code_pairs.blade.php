<h2>Generated code pairs</h2>

<p>
    Parent user: @if($parent) {{ $parent->username  }} @else - @endif <br />
    Group: @if($group) {{ $group->name  }} @else - @endif
</p>

<table>
    <tr>
        <td>Code 1</td>
        <td>Code 2</td>
    </tr>
    @foreach($codePairs as $codePair)
        <tr>
            <td>{{ $codePair[0]->code }}</td>
            <td>{{ $codePair[1]->code }}</td>
        </tr>
    @endforeach
</table>

