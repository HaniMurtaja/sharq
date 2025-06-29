<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>
                Name
            </th>

      
         
            <th>Total Orders</th>
            
            <th>AVG. Assign</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
               
                <td>{{ $row['id'] }}</td>
                <td>{{ $row['name'] }}</td>
              
  
                <td>{{ $row['orders_count'] }}</td>
                <td>{{ $row['test'] }}</td>
            
            </tr>
        @endforeach

    </tbody>
</table>
