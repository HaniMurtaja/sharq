@extends('admin.layouts.app')


<style>
    .modal.show {
    opacity: 1 !important;
}
</style>


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

@section('title')
    Show Report City
@endsection
@section('content')
    <div class="flex flex-col h-screen p-6">
        <!-- Filter -->
        @include('admin.pages.reports.citys.search')
        <!-- Table -->
        <div class="p-4 bg-white border rounded-lg border-gray1">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-4 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-3">
                    <h3 class="mb-2 text-base font-medium text-black">Show Report City</h3>
                </div>

            </div>

            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">

            <div class="table-responsive w-full overflow-x-auto">
                <table id="order-list" class="table datatables table-boreder table-hover table-responsive" >
                    <thead class="bg-gray-50">
                        <tr>

                            <th >City</th>
                            <th >Driver</th>
                            <th >Orders</th>
                            <th >assign-time</th>
                            <th >accept-time</th>
                            <th >arrive-time</th>
                            <th >waiting-time</th>
                            <th >deliver-time</th>
                            <th >UTR</th>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCountDriver = 0;
                            $totalCountOrders = 0;
                            $totalDriverAssigned = 0;
                            $totalDriverAcceptance = 0;
                            $totalDriverArrival = 0;
                            $totalDriverWaiting = 0;
                            $totalDriverDeliver = 0;
                            $totalSumUTR = 0;
                            $temp = 0;
                        @endphp
                        @forelse ($citys as $order)
                            @php
                                $totalCountDriver +=  ($order->number_of_drivers ?? 0);
                                $totalCountOrders +=  ($order->daily_orders ?? 0);
                                $totalSumUTR = (  $totalCountDriver  != 0) ?( $totalCountOrders / $totalCountDriver):0;
                            @endphp
                            <tr>



                                <td>{{ $order->city }}</td>
                                <td>{{ @$order->number_of_drivers }}</td>
                                <td>{{ $order->daily_orders }}</td>
                                <td style="color:fff;background: {{ $order->color_DriverAssigned }}">{{ $order->daily_DriverAssigned }}</td>
                                <td style="color:fff;background: {{ $order->color_DriverAcceptance }}">{{ $order->daily_DriverAcceptance }}</td>
                                <td style="color:fff;background: {{ $order->color_DriverArrival }}">{{ $order->daily_DriverArrival }}</td>
                                <td style="color:fff;background: {{ $order->color_DriverWaiting }}">{{ $order->daily_DriverWaiting }}</td>
                                <td style="color:fff;background: {{ $order->color_DriverDeliver }}">{{ $order->daily_DriverDeliver }}</td>
                                <td style="color:fff;background: {{ $order->color_utr }}">{{ $order->utr }}</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>

                            <th>Grand Total</th>
                            <th>{{ $totalCountDriver }}</th>
                            <th>

                                @if ($numberOfDays > 0)
                                {{-- {{ ($totalCountOrders/$numberOfDays) }} --}}
                                {{ ($totalCountOrders) }}
                            @else
                               {{$totalCountOrders}}
                            @endif
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                            <th>{{ round($totalSumUTR,2) }}</th>
                        </tr>
                    </tfoot>

                </table>

            </div>




        <!-- Pagination -->

    </div>
    <script src="{{ asset('maps/datepickerf/jquery.datetimepicker.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(function() {

                $('.datetimepicker1').datetimepicker({
                    locale: 'ru'
                });
            });
        });
    </script>
@endsection
@include('livewire.order-history')
<script>
    $(document).ready(function() {
        $('.status').select2({
            placeholder: "Status",
            allowClear: true
        });

        $('#type').select2({
            allowClear: true,
            placeholder: 'Type',
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            }
        });


        $('#client_id').select2({
            allowClear: true,
            placeholder: 'Client',
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            }
        });

        $('#driver_id').select2({
            allowClear: true,
            placeholder: 'Driver',
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            }
        });






















    });





    $(function() {
        $('input[name="date"]').daterangepicker({
            opens: 'left',
            autoUpdateInput: false // Prevent automatic update to preserve your custom logic
        }, function(start, end) {
            // Manually set the value in the format you want
            $('input[name="date"]').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });


        $('.select2all').select2({
            allowClear: true,
            placeholder: 'Please choose',
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            }
        });



    });
</script>
