@extends('admin.layouts.app')
<!-- <link rel="stylesheet" href="{{ asset('new/src/css/clients.css') }}" /> -->
<link rel="stylesheet" href="{{ asset('new/src/css/globalLayout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/onePageLayout.css') }}" />
@section('content')
    <div class="w-full h-full">

        <div class="flex  flex-col p-2 h-full sideSectionMapContainer">
            <div class="bg-white  rounded-lg h-full">


                <div class="w-full h-full md:flex-row rounded-4 onePageLayout">

                    <div class="pageHeader">
                        <h1>
                            Online orders
                        </h1>
                    </div>
                    <div class="sectionGlobalForm clientOrders">
                        <div class="mt-4">


                        </div>

                        <!-- Orders Table -->

                        <div class="scrollable-table-custom">
                            <div id="orders-table_wrapper" class="dt-container dt-empty-footer">
                                <div class="dt-layout-row">
                                    <div class="dt-layout-cell dt-layout-start"></div>

                                </div>
                                <div class="dt-layout-row dt-layout-table">
                                    <div class="dt-layout-cell  dt-layout-full">
                                        <div id="orders-table_processing" class="dt-processing" role="status"
                                            style="display: none;">
                                            <div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                        </div>
                                        <table id="online-orders-table" style="padding-left: 30px; width: 100%;"
                                            class="table dataTable" aria-describedby="orders-table_info">
                                            <thead>



                                                <tr role="row">
                                                    <th data-dt-column="0" rowspan="1" colspan="1"
                                                        class="dt-orderable-asc dt-orderable-desc dt-ordering-asc"
                                                        aria-sort="ascending" aria-label="ID: Activate to invert sorting"
                                                        tabindex="0"><span class="dt-column-title" role="button">Driver
                                                            ID</span><span class="dt-column-order"></span>
                                                    </th>
                                                    <th data-dt-column="1" rowspan="1" colspan="1"
                                                        class="dt-orderable-asc dt-orderable-desc"
                                                        aria-label="Order time: Activate to sort" tabindex="0"><span
                                                            class="dt-column-title" role="button">Driver name</span><span
                                                            class="dt-column-order"></span></th>
                                                    <th data-dt-column="2" rowspan="1" colspan="1"
                                                        class="dt-orderable-asc dt-orderable-desc"
                                                        aria-label="Branch: Activate to sort" tabindex="0"><span
                                                            class="dt-column-title" role="button">Orders</span><span
                                                            class="dt-column-order"></span></th>



                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="sorting_1">385</td>
                                                    <td>Mariam Mohamed</td>
                                                    <td class="order-details">

                                                        <div
                                                            class="d-flex justify-content-between align-items-center order-data">
                                                            <div>
                                                                <span>Branch Name</span> - [<span>55</span>]
                                                            </div>
                                                            <div>
                                                                <button data-id="70"
                                                                    class="online-orders-actions flex items-center justify-center w-3 h-3 ">
                                                                    <img src="/new/src/assets/icons/delete.svg"
                                                                        alt="">
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center order-data">
                                                            <div>
                                                                <span>Branch Name</span> - [<span>55</span>]
                                                            </div>
                                                            <div>
                                                                <button data-id="70"
                                                                    class="online-orders-actions flex items-center justify-center w-3 h-3 ">
                                                                    <img src="/new/src/assets/icons/delete.svg"
                                                                        alt="">
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center order-data">
                                                            <div>
                                                                <span>Branch Name</span> - [<span>55</span>]
                                                            </div>
                                                            <div>
                                                                <button data-id="70"
                                                                    class="online-orders-actions flex items-center justify-center w-3 h-3 ">
                                                                    <img src="/new/src/assets/icons/delete.svg"
                                                                        alt="">
                                                                </button>
                                                            </div>
                                                        </div>



                                                    </td>



                                                </tr>

                                            </tbody>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="dt-layout-row">

                                    <div class="dt-layout-cell dt-layout-end">

                                    </div>
                                </div>
                            </div>

                        </div>




                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        renderOnlineOrdersDataTable();

        function renderOnlineOrdersDataTable() {
            if ($.fn.DataTable.isDataTable('#online-orders-table')) {
                $('#online-orders-table').DataTable().destroy();
            }

            var branchesTable = $('#online-orders-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-online-orders') }}",
                    "type": "GET",
                },
                "columns": [{
                        "data": "id"
                    }, // Driver ID
                    {
                        "data": "name"
                    }, // Driver Name
                    {
                        "data": null
                    }, // Orders

                ],
                "createdRow": function(row, data, dataIndex) {
                    // Initialize the orders HTML
                    let ordersHtml = '';
                    console.log(data.details, data.details.length);

                    if (data.details && Object.keys(data.details).length > 0) {
                        // Loop through the order details to build the order list
                        Object.values(data.details).forEach(order => {
                            ordersHtml += `
    <div class="order-details">
        <div class="d-flex justify-content-between align-items-center order-data mb-2">
            <div>
                <span>${order.branch?.name || 'N/A'}</span> - [<span>${order.order_id}</span>]
            </div>
            <div>
                <button data-id="${order.order_id}" data-driver_id="${data.id}" class="online-orders-actions flex items-center justify-center w-3 h-3">
                    <img src="/new/src/assets/icons/delete.svg" alt="Delete">
                </button>
            </div>
        </div>
    </div>
`;
                        });
                    } else {
                        ordersHtml = '<div class="text-start">No orders available</div>';
                    }



                    $('td:eq(0)', row).text(data.id);
                    $('td:eq(1)', row).text(data.name);
                    $('td:eq(2)', row).html(ordersHtml); // Orders column
                  ; // Action Button
                },
                "paging": true,
                "pageLength": 10,
                "lengthChange": false,
                "ordering": false,
                "searching": true,
                "info": false
            });

            $('.dt-input').attr('placeholder', 'Search here...');
        }




        $(document).on("click", ".online-orders-actions", function() {
            let orderId = $(this).data("id");
            let driverId = $(this).data("driver_id");

            if (!orderId || !driverId) {
                alert("Invalid order or driver ID");
                return;
            }

            if (confirm("Are you sure you want to delete this order?")) {
                $.ajax({
                    url: "/admin/delete-driver-order",
                    type: "POST",
                    data: {
                        driver_id: driverId,
                        order_id: orderId,
                        _token: $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function(response) {
                        alert(response.message);
                        renderOnlineOrdersDataTable();
                    },
                    error: function(xhr) {
                        alert("Failed to delete order: " + xhr.responseJSON.message);
                    }
                });
            }
        });



    });
</script>
