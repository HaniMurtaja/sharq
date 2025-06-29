<table>
    <thead>
        <tr>
            <td>ID</td>
            <td>Customer name</td>
            <td>Customer phone</td>
            <td>Shope</td>
            <td>Branch</td>
            <td>Status</td>
            <td>Created at</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->customer_phone }}</td>
                <td>{{$order->shop->name}}</td>
                <td> {{$order->branch->name}} </td>
                <td> {{$order->status->getLabel()}} </td>
                <td> {{$order->created_at->format('Y:m:d')}} </td>
            </tr>
        @endforeach
    </tbody>
</table>
