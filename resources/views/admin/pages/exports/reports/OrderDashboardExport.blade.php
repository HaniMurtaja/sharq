<table>
    <thead>
        <tr>
            <th>Brand Name  </th>
            <th >Total  </th>
            <th >Pending Orders  </th>
            <th >In Progress Orders  </th>
            <th >Cancel Orders  </th>
            <th >Delivered Orders  </th>
            <th >Avg Operator Waiting (H:i:s)  </th>
            <th >Avg Delivered (H:i:s)  </th>

        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
               
                <td>{{ $row['user_name'] }}</td>
                <td>{{ $row['total_orders'] }}</td>

                <td>{{ $row['pending_orders'] }}</td>
                <td>{{ $row['in_progress_orders'] }}</td>
                <td>{{ $row['cancel_orders'] }}</td>
                <td>{{ $row['delivered_orders'] }}</td>
                <td>{{ $row['avg_operator_waiting'] }}</td>
                <td>{{ $row['avg_delivered'] }}</td>

            </tr>
        @endforeach

    </tbody>
</table>
