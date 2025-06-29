<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>

  <!-- Include Flatpickr Library -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>
    $(document).ready(function() {
        // Trigger AJAX on input or select changes
        console.log(5656);

        console.log($('#reservation'));
        sendChartData();





        console.log('hiii');

        $(document).on('click', '.applyBtn', function(e) {
            e.preventDefault();
            console.log('Delete button clicked');
            sendChartData();
        });

        $(document).on('click', '.cancelBtn', function(e) {
            e.preventDefault();
            console.log('Delete button clicked');
            sendChartData();
        });


        $('#date, #client').on('change', function() {
            console.log(333);

            sendChartData();
        });

        // Send initial data on page load


        // Function to send data and update all charts
        function sendChartData() {
            console.log('test');

            const dateRange = $('#date').val(); // Get date range value
            const clientId = $('#client').val(); // Get selected client ID

            $.ajax({
                url: '{{ route('get-charts') }}',
                method: 'GET',
                data: {
                    date: dateRange,
                    client: clientId
                },
                success: function(response) {
                    // Update all charts with the response data
                    $('#total-orders-count').html(response.orders_count);
                    $('#total-balance').html(response.totalServiceFees);
                    updateOrdersChart(response);
                    updateOrdersPerHourChart(response);
                    updateArriveInBranchChart(response);
                    updateWaitingTimeChart(response);
                    updateDeliveryTimeChart(response);
                    updateRateOfOrdersPerCityChart(response);
                    updatePreparationTimeChart(response);
                    updateDeliveredInTimeChart(response);
                    updateTotalIncomeChart(response);
                },
                error: function(error) {
                    console.error('Error fetching chart data:', error);
                }
            });
        }

        // Define chart update functions

        // 1. Orders Chart
        function updateOrdersChart(response) {
            // Data from the response
            console.log(response.ordersDataWithLabels);
            $('#order_count_order_city_chart').html(response.ordersDataWithLabels.orders_count)
            $('#order_count_order_chart').html(response.ordersDataWithLabels.orders_count)
            const ordersData = response.ordersDataWithLabels.ordersDataWithLabels; // Expecting an object from the response
            console.log(ordersData);

            const labels = Object.keys(ordersData); // Extract labels (order statuses)
            const data = Object.values(ordersData); // Extract corresponding data (counts)

            const ctx = document.getElementById('ordersChart').getContext('2d');
            const orderChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Order Status Count',
                        data: data,
                        backgroundColor: 'rgba(0, 123, 255, 0.8)', // Blue color for bars
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw;
                                    return `${label}: ${value}`;
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 500,
                            ticks: {
                                stepSize: 100, // Make increments of 100
                                callback: function(value) {
                                    return value; // Display ticks as numbers
                                }
                            }
                        }
                    },
                    responsive: true
                }
            });
        }



        // 2. Orders Per Hour Chart
        function updateOrdersPerHourChart(response) {
            const ctx2 = document.getElementById('ordersPerHourChart').getContext('2d');

            // Get the orders per hour data from the response
            const ordersPerHourData = response.ordersPerHourWithZeros;

            // Create labels for each hour of the day (0:00 to 23:00)
            const labelsPerHour = Array.from({
                length: 24
            }, (_, i) => `${i}:00`);

            // Create the chart
            const chart2 = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: labelsPerHour,
                    datasets: [{
                        label: '',
                        data: ordersPerHourData,
                        fill: true,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }


        // 3. Arrive In Branch Chart
        function updateArriveInBranchChart(response) {


            // Extract labels and data from the response
            const labels = response.dates; // Expecting an array of dates
            const data = response.arriveInValues; // Expecting an array of values corresponding to the dates

            $('#total_average_picked_time').html(response.total_average_picked_time);


            const ctxArrivalTime = document.getElementById('arriveInChart').getContext('2d');
            const avgArrivalTimeChart = new Chart(ctxArrivalTime, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '',
                        data: data,
                        borderColor: 'red',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // 4. Waiting Time Chart
        function updateWaitingTimeChart(response) {
            const ctx = document.getElementById('waitingPickupTimeChart').getContext('2d');

            // Extract labels and data from the response
            $('#totalAverageWaitingTime').html(response.totalAverageWaitingTime);
            const waitingPickupDatesLabels = response.waiting_pickup_dates; // Expecting an array of dates
            const dataAverageWaitingTimes = response.averageWaitingTimes; // Expecting an array of average waiting times

            // Create the chart
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: waitingPickupDatesLabels,
                    datasets: [{
                        label: '',
                        data: dataAverageWaitingTimes,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day' // Adjust the time unit as needed
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Waiting Time (in seconds)' // Adjust this label based on your time unit
                            },
                            beginAtZero: true // Ensure the y-axis starts at zero
                        }
                    }
                }
            });
        }


        // 5. Delivery Time Chart
        function updateDeliveryTimeChart(response) {
            const ctx = document.getElementById('avgDeliveryTime').getContext('2d');
            $('#avg_delivery_time_text').html(response.total_average_time)
            // Extract labels and data from the response
            const avgDeliveryDates = response.avg_delivery_dates; // Expecting an array of dates
            const avgDeliveryTimes = response.avg_deliveryTimes; // Expecting an array of average delivery times

            // Create the chart
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: avgDeliveryDates,
                    datasets: [{
                        label: '',
                        data: avgDeliveryTimes,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day' // Adjust the unit as needed
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Avg Delivery Time (in seconds)' // Adjust this label based on your time unit
                            },
                            beginAtZero: true // Ensure the y-axis starts at zero
                        }
                    }
                }
            });
        }


        // 6. Rate of Orders Per City Chart
        function updateRateOfOrdersPerCityChart(response) {


            // Extract data from the response
            const cities = response.cities; // Expecting an array of city names
            const orderCounts = response.orderCounts; // Expecting an array of order counts

            console.log('statr');

            console.log(orderCounts);



            const ctxCityOrders = document.getElementById('ordersPerCityChart').getContext('2d');

            const cityOrdersChart = new Chart(ctxCityOrders, {
                type: 'pie',
                data: {
                    labels: cities,
                    datasets: [{
                        data: orderCounts, // Percentages for each city
                        backgroundColor: [
                            '#ff4c4c', // City 1 (Red)
                            '#ff8c1a', // City 2 (Orange)
                            '#ff6e1a', // City 3 (Darker Orange)
                            '#ffcc66', // City 4 (Yellow)
                            '#ffb84d', // City 5 (Lighter Yellow)
                            '#ffdb4d', // City 6 (Even lighter yellow)
                            '#ffaa00', // City 7
                            '#ffbf00', // City 8
                            '#ffcc00', // City 9
                            '#ffd700', // City 10
                            '#e60000', // City 11 (Darker Red)
                            '#ff6600', // City 12
                        ],
                        borderWidth: 0,
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 100,
                                padding: 35,
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ": " + tooltipItem.raw + "%";
                                }
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }


        // 7. Preparation Time Chart
        function updatePreparationTimeChart(response) {
            const ctx = document.getElementById('ordersAcceptanceRate').getContext('2d');

            // Data from the response
            const acceptedTime = response
                .acceptanceRate; // Assuming response contains accepted and rejected times
            const rejectedTime = response.rejectionRate;
            var test = acceptedTime + '%'
            $('#order_count_acceptance_rate_chart').html(test)
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Acceptance rate', 'Rejection rate'],
                    datasets: [{
                        label: 'Preparation Time',
                        data: [acceptedTime, rejectedTime],
                        backgroundColor: [
                            'rgba(0, 128, 0, 0.2)', // Green with 0.2 opacity
                            'rgba(0, 0, 0, 0.2)' // Black with 0.2 opacity
                        ],
                        borderColor: [
                            'rgba(0, 128, 0, 1)', // Green with 1 opacity
                            'rgba(0, 0, 0, 1)' // Black with 1 opacity
                        ],


                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw.toFixed(2) +
                                        ' seconds'; // Assuming you want to show it in seconds
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }


        // 8. Delivered In Time Chart
        function updateDeliveredInTimeChart(response) {
            const ctx = document.getElementById('driverOfflineChart').getContext('2d');

            const labels = response.driverOfflineAvgDates;
            const data = response.averageOfflineDurations;
            $('#totalAverageWaitingTime2').html(response.totalAverageWaitingTime2);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '',
                        data: data,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Average Time (minutes)'
                            }
                        }
                    }
                }
            });
        }


        // 9. Total Income Chart
        function updateTotalIncomeChart(response) {
            const ctx = document.getElementById('ordersAssignedPerHourChart').getContext('2d');

            const labels = response.hours;
            const data = response.orderCountsAssignedToDriver;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '',
                        data: data,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                        }
                    }
                }
            });
        }
    });
</script>


