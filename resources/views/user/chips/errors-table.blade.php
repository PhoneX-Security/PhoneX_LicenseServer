{{--
Parameters: $reports, $with_username
--}}
@if($reports)
    <table class="table table-condensed">
        <tr>
            <th>ID</th>
            <th>Date</th>
            @if($with_username)
                <th>Username</th>
            @endif
            <th>App info</th>
            <th>User message</th>
            <th>Filename</th>
        </tr>
        @foreach($reports as $report)
            <tr>
                <td>{{$report->id}}</td>
                <td>{{$report->date_created}}</td>
                @if($with_username)
                    <td>{{$report->userName}}</td>
                @endif
                <td>{{$report->appVersion}}</td>
                <td>{{$report->userMessage}}</td>
                <td>{{$report->filename}}</td>
            </tr>
        @endforeach
    </table>
    <p>Total: {{ $reports->count() }}</p>
@else
    No error reports
@endif
