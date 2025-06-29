<table>
    <thead>
    <tr>
        @foreach($columns as $columnName => $columnLabel)
        <th>{{$columnLabel}}</th>
         @endforeach

    </tr>
    </thead>
    <tbody>
    @foreach($items as $row)
        <tr>
            @foreach($columns as $columnName => $columnLabel)
                <td>{{$row[$columnName]}}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
