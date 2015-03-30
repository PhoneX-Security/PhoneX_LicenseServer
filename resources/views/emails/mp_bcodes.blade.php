<p>Generated code pairs:</p>
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

