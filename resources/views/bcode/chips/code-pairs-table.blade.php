<table class="table table-condensed">
    <tr>
        <th>Code 1</th>
        <th>Code 2</th>
    </tr>
@foreach($codePairs as $code1 => $code2)
    <tr>
        <td>{{bcodeDashes($code1)}}</td>
        <td>{{bcodeDashes($code2)}}</td>
        <td><button class="btn btn-default btn-xs code-copy" ><i class="fa fa-files-o"></i> </button></td>
    </tr>
@endforeach
</table>
<script>
    $(".code-copy").click(function(){
        var code1 = $(this).parent().prev().prev().text();
        var code2 = $(this).parent().prev().text();

        prompt("Copy this: (works in Chrome)", code1 + "\n" + code2);
//        alert();
    });
</script>