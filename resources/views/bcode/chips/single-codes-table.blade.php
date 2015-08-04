<table class="table table-condensed">
    <tr>
        <th>Code</th>
    </tr>
@foreach($codes as $code)
    <tr>
        <td>{{$code->printable_code}}</td>
    </tr>
@endforeach
</table>
