<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>
                Name
            </th>

      
            <th>City</th>
            <th>Total Orders</th>
            
            <th>AVG. Acceptance</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
               
                <td>{{ $row['id'] }}</td>
                <td>{{ $row['name'] }}</td>
              
                <td>{{ $row['city'] }}</td>
                <td>{{ $row['orders_count'] }}</td>
                <td>{{ $row['avg_accept_time'] }}</td>
            
            </tr>
        @endforeach

    </tbody>
</table>
