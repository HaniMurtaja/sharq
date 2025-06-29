<table>
    <thead>
        <tr>
            <th>ID</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
                <td>{{ $row->id }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
