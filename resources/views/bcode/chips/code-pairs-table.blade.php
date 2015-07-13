<table class="table table-condensed">
    <tr>
        <th>Code 1</th>
        <th>Code 2</th>
    </tr>
@foreach($codePairs as $code1 => $code2)
    <tr>
        <td>{{$code1}}</td>
        <td>{{$code2}}</td>
    </tr>
@endforeach
</table>