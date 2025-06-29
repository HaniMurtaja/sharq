<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>
                Name
            </th>

            <th>Phone</th>
            <th>Email</th>
            <th>Account Number</th>

            <th>Country</th>
            <th>City</th>
            <th>Currency</th>
            <th>Parial Pay</th>
            <th>Group</th>
            <th>Note</th>
            <th>Integration Company</th>
            <th>Default Prepration Time</th>
            <th>Min. Prepration Time</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($items as $row)
            <tr>
               
                <td>{{ $row['id'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['phone'] }}</td>
                <td>{{ $row['email'] }}</td>
                <td>{{ $row['account_no'] }}</td>
                <td>{{ $row['country'] }}</td>
                <td>{{ $row['city'] }}</td>
                <td>{{ $row['currency'] }}</td>
                <td>{{ $row['parial_pay'] }}</td>
                <td>{{ $row['group'] }}</td>
                <td>{{ $row['note'] }}</td>
                <td>{{ $row['integration_company'] }}</td>
                <td>{{ $row['defualt_prepration_time'] }}</td>
                <td>{{ $row['min_prepration_time'] }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
