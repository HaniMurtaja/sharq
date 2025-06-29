<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">

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
<link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">
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
<script>
    $(document).ready(function() {
        var myChart = echarts.init(document.getElementById('bar-chart'));


        var option = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['Orders']
            },
            toolbox: {
                show: true,
                feature: {
                    magicType: {
                        show: true,
                        type: ['line', 'bar']
                    },
                    restore: {
                        show: true
                    },
                    saveAsImage: {
                        show: true
                    }
                }
            },
            color: ["#55ce63", "#009efb"],
            xAxis: [{
                type: 'category',
                data: [],
                axisLabel: {
                    rotate: 60,
                    interval: 0,
                    fontSize: 12
                }
            }],
            yAxis: [{
                type: 'value'
            }],
            series: [{
                name: 'Orders',
                type: 'bar',
                data: []
            }]
        };

        myChart.setOption(option);

        function loadChartData(fromDate, toDate) {
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();

            $.ajax({
                url: '{{ route('OrderDashboard.ordersChartData') }}',

                type: 'GET',
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    clientFilter: clientFilter,
                    city_id: city_id
                },
                dataType: 'json',
                success: function(response) {
                    console.log("AJAX Response:", response);

                    if (response.dates && response.counts && Array.isArray(response.dates) && Array
                        .isArray(response.counts)) {
                        option.xAxis[0].data = response.dates;
                        option.series[0].data = response.counts;
                    } else {
                        console.warn("Invalid or empty data received");
                        option.xAxis[0].data = ["No Data"];
                        option.series[0].data = [0];
                    }

                    myChart.setOption(option, true);

                    $('#total_orders_count').text(response.counts_orders);
                    $('#countordercity').text(response.counts_orders);
                    $('#countorderclient').text(response.counts_orders);

                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    alert("Failed to load data. Please check the console for details.");
                }
            });
        }

        // ✅ عند النقر على زر البحث
        $('#filter').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();
            if (!fromDate || !toDate) {
                alert("يرجى تحديد تاريخ البداية والنهاية.");
                return;
            }
            // Construct the URL manually to avoid HTML encoding issues
            let baseUrl = "{{ route('OrderDashboard') }}";
            let finalUrl =
                `${baseUrl}?fromtime=${encodeURIComponent(fromDate)}&totime=${encodeURIComponent(toDate)}&client_id=${encodeURIComponent(clientFilter)}&city_id=${encodeURIComponent(city_id)}&status_ids%5B%5D=9`;

            // Update the link dynamically
            $(".moreDetailsBtn").attr("href", finalUrl);
            loadChartData(fromDate, toDate);
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#reportBtn').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();
            if (!fromDate || !toDate) {
                alert("يرجى تحديد تاريخ البداية والنهاية.");
                return;
            }
            // Clear previous data
            $('#reportTable tbody').empty();
            $('#totalOrders').empty();

            // Call the report API via AJAX.
            $.ajax({
                url: '{{ route('OrderDashboard.OrdersPerClientstData') }}', // Replace with your actual route URL
                type: 'GET',
                data: {

                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    clientFilter: $('#clientFilter').val(),
                    city_id: $('#city_id').val()
                },
                success: function(response) {
                    // Loop through each user object from the API response
                    $.each(response.data, function(index, user) {
                        var row = '<tr>' +

                            '<td>' + user.user_name + '</td>' +
                            '<td>' + user.total_orders + '</td>' +
                            '<td>' + user.pending_orders + '</td>' +
                            '<td>' + user.in_progress_orders + '</td>' +
                            '<td>' + user.cancel_orders + '</td>' +
                            '<td>' + user.delivered_orders + '</td>' +
                            '<td>' + user.avg_operator_waiting + '</td>' +
                            '<td>' + user.avg_delivered + '</td>' +
                            '</tr>';
                        $('#reportTable tbody').append(row);
                    });
                    // Display the total orders count
                    $('#totalOrders').text('Total Orders: ' + response.counts_orders);
                    $('#counts_delivered_orders').text('Total Delivered: ' + response
                        .counts_delivered_orders);
                    $('#counts_cancel_orders').text('Total Cancel: ' + response
                        .counts_cancel_orders);

                    // Open the Bootstrap modal
                    
                    var modal = new bootstrap.Modal(document.getElementById('reportModal'));
                    modal.show();
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching report data: " + error);
                }
            });
        });
    });
</script>
