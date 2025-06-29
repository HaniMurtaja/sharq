<table>
    <thead>
        <tr>

            <th>ID </th>
            <th >User Name  </th>
            <th> Email  </th>

            <th >Role  </th>
            

        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
               
                <td>{{ $row['id'] }}</td>

                <td>{{ $row['user_name'] }}</td>

                <td>{{ $row['email'] }}</td>
                <td>{{ $row['role'] }}</td>
               

                <td>{{ $row['full_name'] }}</td>

                <td>{{ $row['email'] }}</td>
                <td>{{ $row['user_role'] }}</td>
               


            </tr>
        @endforeach

    </tbody>
</table>
