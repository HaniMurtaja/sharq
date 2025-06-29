<!-- HTML container for the chart -->

<script>
    $(document).ready(function() {
        // Initialize the pie chart instance
        var pieChart = echarts.init(document.getElementById('pie-chart'));

        // Function to load and update chart data
        function loadPerCityChartData() {
            // Show loading indicator while fetching data
            pieChart.showLoading();

            // Prepare data from filter inputs
            var requestData = {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                clientFilter: $('#clientFilter').val(),
                city_id: $('#city_id').val()
            };

            // Call the Laravel API using AJAX
            $.ajax({
                url: '{{ route('OrderDashboard.OrdersPerCityChartData') }}',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    // Process the API response:
                    // response.data is an array of objects with {city, total_orders}
                    var chartData = [];
                    var legendData = [];

                    $.each(response.data, function(index, item) {
                        chartData.push({
                            value: item.total_orders,
                            name: item.city
                        });
                        legendData.push(item.city);
                    });

                    // Define the new chart option using the data from the API
                    var option = {
                        tooltip: {
                            // trigger: 'item',
                            // formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                //dataView: { show: true, readOnly: false },
                                //  magicType: { show: true, type: ['pie', 'funnel'] },
                                // restore: { show: true },
                                // saveAsImage: { show: true }
                            }
                        },
                        calculable: true,
                        series: [{
                            name: 'City',
                            type: 'pie',
                            // roseType: 'area',
                            max: 10, // for funnel
                            sort: 'ascending', // for funnel
                            data: chartData
                        }]
                    };

                    // Hide the loading indicator and update the chart
                    pieChart.hideLoading();
                    pieChart.setOption(option, true);

                    // $('#countordercity').text(response.counts_orders);

                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data: " + error);
                    pieChart.hideLoading();
                }
            });
        }

        // Attach click event on #filter to trigger the AJAX call and update the chart
        // ✅ عند النقر على زر البحث
        $('#filter').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();
            if (!fromDate || !toDate) {
                // alert("يرجى تحديد تاريخ البداية والنهاية.");
                return;
            }
            loadPerCityChartData();
        });

        // Optionally, adjust the chart when the window is resized
        $(window).on("resize", function() {
            setTimeout(function() {
                pieChart.resize();
            }, 100);
        });
        $(".sidebartoggler").on("click", function() {
            pieChart.resize();
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#cityreportBtn').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();
            if (!fromDate || !toDate) {
                alert("يرجى تحديد تاريخ البداية والنهاية.");
                return;
            }
            // Clear previous data
            $('#cityreportTable tbody').empty();
            $('#totalOrders').empty();

            // Call the report API via AJAX.
            $.ajax({
                url: '{{ route('OrderDashboard.OrdersPerCityData') }}', // Replace with your actual route URL
                type: 'GET',
                data: {

                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    clientFilter: $('#clientFilter').val(),
                    city_id: $('#city_id').val()
                },
                success: function(response) {
                    $('#cityreportTable tbody').empty(); // Clear previous data
                    $.each(response.data, function(index, user) {
                        var row = '<tr>' +
                            '<td>' + user.city + '</td>' +
                            '<td>' + user.total_orders + '</td>' +
                            '<td>' + user.total_drivers + '</td>' +
                            '<td>' + user.total_dispatchers + '</td>' +

                            
                            '<td>' + user.pending_orders + '</td>' +
                            '<td>' + user.in_progress_orders + '</td>' +
                            '<td>' + user.cancel_orders + '</td>' +
                            '<td>' + user.delivered_orders + '</td>' +
                            '<td>' + user.avg_operator_waiting + '</td>' +
                            '<td>' + user.avg_delivered + '</td>' +
                           
                            
                            '</tr>';
                        $('#cityreportTable tbody').append(row);
                    });

                    // Update order counts
                    $('#totalOrdersPerCity').text('Total Orders: ' + response.counts_orders);
                    $('#counts_delivered_ordersPerCity').text('Total Delivered: ' + response
                        .counts_delivered_orders);
                    $('#counts_cancel_ordersPerCity').text('Total Cancel: ' + response
                        .counts_cancel_orders);

                    
                    var modalElement = document.getElementById('cityreportModal');
                    if (modalElement) {
                        var modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    } else {
                        console.error("Modal element not found!");
                    }
                }
                ,
                error: function(xhr, status, error) {
                    console.error("Error fetching report data: " + error);
                }
            });
        });
    });
</script>
