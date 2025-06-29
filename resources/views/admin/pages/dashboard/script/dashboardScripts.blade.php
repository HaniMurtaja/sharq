
<script>
    $(document).ready(function() {

        let driverCurrentRequest = null;
        let markersDriversAssign = [];
        const defaultUserIcon2 = "{{ asset('user.png') }}";


        // 1. Select2 Configuration
        $('#clientFilter').select2({
            placeholder: "Select...",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        });
        $('.seelct22').select2({
            placeholder: "Select...",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        });

        $('#clientFilter').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search client here...');
        });

        $('#shiftFilter').select2({
            dropdownParent: "#OperatorsDetailsDashBoard",
            placeholder: "Select...",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        });

        $('#shiftFilter').on('select2:open', function() {
            $('.select2-search__field').attr('placeholder', 'Search shift here...');
        });

        // 2. Chart.js Configuration
       // const barCtx = $('#stackedBarChart')[0].getContext('2d');
        const OperatorsBarCtx = $('#operators')[0].getContext('2d');

        const donutCtx = $('#donutChart')[0].getContext('2d');
        let barChart, donutChart, operatorsChart;

        Chart.defaults.global.legend = false;


        // Function to create or update the Stacked Bar Chart

        // Function to create or update the Stacked Bar Chart
        function createOperatorBarChart(data) {
            if (operatorsChart) operatorsChart.destroy();
            operatorsChart = new Chart(OperatorsBarCtx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 50
                            },
                            grid: {
                                color: '#e0e0e0'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Function to create or update the Donut Chart
        function createDonutChart(data) {
            if (donutChart) donutChart.destroy();
            donutChart = new Chart(donutCtx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}`;
                                }
                            }
                        },
                        legend: {
                            display: donutCtx.canvas.id === "donutChart",
                            position: 'right'
                        }

                    }
                }
            });

            // Generate custom legend HTML after the chart is rendered
            // Generate custom legend HTML after the chart is rendered
            const customLegendHTML = donutChart.generateLegend();

            // Log the custom legend to debug
            console.log(customLegendHTML);

            // Append the custom legend into the external div
            $("#chartLegend").html(customLegendHTML);

            // Add click event to toggle visibility of chart segments
            $("#chartLegend").on("click", "li", function() {
                const index = $(this).index(); // Get the index of the clicked legend item
                const meta = donutChart.getDatasetMeta(0); // Get the dataset metadata

                // Toggle the hidden state of the clicked segment
                meta.data[index].hidden = !meta.data[index].hidden;

                // Update the chart with the new visibility state
                donutChart.update();
            });
        }


        function renderDonutChart(accepted, rejected) {
            console.log(accepted, rejected);

            const ctx = document.getElementById('acceptanceChart').getContext('2d');

            // Destroy existing chart instance if it exists
            if (window.myDonutChart) {
                window.myDonutChart.destroy();
            }

            // Create a simple donut chart
            window.myDonutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [accepted, rejected],
                        backgroundColor: ['#4caf50',
                            '#795548'
                        ], // Green for accepted, brown for rejected
                        borderWidth: 0,
                    }, ],
                },
                options: {
                    cutout: '80%',
                    borderWidth: 1, // Creates the donut shape
                    plugins: {
                        legend: {
                            display: false
                        }, // Remove legend
                        tooltip: {
                            enabled: false
                        }, // Remove tooltips
                        centerText: {

                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                },
                plugins: [{
                    id: 'centerText',
                    beforeDraw(chart) {
                        const ctx = chart.ctx;
                        const {
                            width,
                            height
                        } = chart;
                        ctx.save();

                        // Add your static text here
                        ctx.font = '12px Arial';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#000';
                        ctx.fillText(`${accepted}% Acceptance`, width / 2, height / 2 -
                            10); // First line
                        ctx.fillText(`${rejected}% Rejected`, width / 2, height / 2 +
                            10); // Second line
                        ctx.restore();
                    }
                }]
            });
        }


        sendChartData();
        $('#clientFilter').on('change', function() {
            console.log(333);

            sendChartData();
        });
        $('#filter').on('click', function() {
            console.log(888);

            sendChartData();
        });

        function sendChartData() {
            console.log('test');

            const dateRangefrom_date = $('#from_date').val();

            const dateRangeto_date = $('#to_date').val();
           // const dateRange = $('#datePicker').val();
            const dateRange = dateRangefrom_date + ' to ' + dateRangeto_date ;

            const clientId = $('#clientFilter').val();

            $.ajax({
                url: '{{ route('get-charts-new') }}',
                method: 'GET',
                data: {
                    date: dateRange,
                    client: clientId
                },
                success: function(response) {
                    const defaultDonutData = {
                        labels: response.cities,
                        datasets: [{
                            label: 'Donut Data',
                            data: response.orderCounts, // Example data
                            backgroundColor: response.colors
                        }]
                    };
                    createDonutChart(defaultDonutData);

                    renderDonutChart(response.acceptanceRate, response.rejectionRate);
                    renderAreaLineChart(response);
                    renderAvgDeliveryTimeChart(response);
                    renderAvgWaitingPickupChart(response);
                    renderAvgDriverWaitingDropOffChart(response);
                    renderAvgOrderPerHourChart(response);




                    const singleDayData = {
                        labels: response.hours,
                        datasets: [{
                                label: 'Attendant',
                                backgroundColor: '#4caf50',
                                data: response.activeCounts,
                                stack: 'combined'
                            },
                            {
                                label: 'Absent',
                                backgroundColor: '#795548',
                                data: response.inactiveCounts,
                                stack: 'combined'
                            }
                        ]
                    };
                    // createBarChart(singleDayData);
                    createOperatorBarChart(singleDayData);




                    const singleDayDataOrder = {
                        labels: response.ordersHours,
                        datasets: [{
                                label: 'Delivered',
                                backgroundColor: '#4caf50',
                                data: response.deliveredCounts,
                                stack: 'combined'
                            },
                            {
                                label: 'Cancelled',
                                backgroundColor: '#795548',
                                data: response.cancelledCounts,
                                stack: 'combined'
                            }
                        ]
                    };
                    createBarChart(singleDayDataOrder);

                    $('#total_orders_count').html(response.orders_count);



                },
                error: function(error) {
                    console.error('Error fetching chart data:', error);
                }
            });
        }






        function renderAreaLineChart(response) {
            const ctx = document.getElementById('avgArrivalPickup').getContext('2d');
            const labels = response.dates;
            const data = response.arriveInValues;

            $('#total_average_picked_time').html(response.total_average_picked_time);
            // Destroy existing chart instance if it exists
            if (window.myAreaLineChart) {
                window.myAreaLineChart.destroy();
            }

            // Create the line chart with filled area
            window.myAreaLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // X-axis labels
                    datasets: [{
                        label: 'Data', // Optional label
                        data: data, // Y-axis values
                        borderColor: '#e64a19', // Line color (orange)
                        backgroundColor: 'rgba(230, 74, 25, 0.3)', // Filled area color with opacity
                        borderWidth: 3, // Thickness of the line
                        fill: true, // Fill the area under the line
                        pointRadius: 5, // Size of data points
                        pointBackgroundColor: '#e64a19', // Color of data points
                        pointBorderColor: '#fff', // Border color of data points
                        pointBorderWidth: 2, // Border width of data points
                        lineTension: 0
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }, // Hide X-axis grid lines
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }, // Customize X-axis font size
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e0e0e0'
                            }, // Y-axis grid lines color
                            ticks: {
                                stepSize: 1, // Y-axis step interval
                                font: {
                                    size: 12
                                }, // Customize Y-axis font size
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        }, // Hide the legend
                        tooltip: {
                            enabled: true
                        }, // Enable tooltips on hover
                    },
                },
            });
        }




        function renderAvgDeliveryTimeChart(response) {
            const ctx = document.getElementById('avgDeliveryTime').getContext('2d');
            $('#avg_delivery_time_text').html(response.total_average_time)

            const avgDeliveryDates = response.avg_delivery_dates;
            const avgDeliveryTimes = response.avg_deliveryTimes;

            if (window.avgDeliveryTimeChart) {
                window.avgDeliveryTimeChart.destroy();
            }


            window.avgDeliveryTimeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: avgDeliveryDates, // X-axis labels
                    datasets: [{
                        label: 'Data', // Optional label
                        data: avgDeliveryTimes, // Y-axis values
                        borderColor: '#e64a19', // Line color (orange)
                        backgroundColor: 'rgba(230, 74, 25, 0.3)', // Filled area color with opacity
                        borderWidth: 3, // Thickness of the line
                        fill: true, // Fill the area under the line
                        pointRadius: 5, // Size of data points
                        pointBackgroundColor: '#e64a19', // Color of data points
                        pointBorderColor: '#fff', // Border color of data points
                        pointBorderWidth: 2, // Border width of data points
                        lineTension: 0
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }, // Hide X-axis grid lines
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }, // Customize X-axis font size
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e0e0e0'
                            }, // Y-axis grid lines color
                            ticks: {
                                stepSize: 1, // Y-axis step interval
                                font: {
                                    size: 12
                                }, // Customize Y-axis font size
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        }, // Hide the legend
                        tooltip: {
                            enabled: true
                        }, // Enable tooltips on hover
                    },
                },
            });
        }





        function renderAvgWaitingPickupChart(response) {
            const ctx = document.getElementById('avgWaitingPickup').getContext('2d');

            $('#totalAverageWaitingTime').html(response.totalAverageWaitingTime);
            const waitingPickupDatesLabels = response.waiting_pickup_dates;
            const dataAverageWaitingTimes = response.averageWaitingTimes;

            if (window.avgWaitingPickupChart) {
                window.avgWaitingPickupChart.destroy();
            }

            // Create the line chart with filled area
            window.avgWaitingPickupChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: waitingPickupDatesLabels, // X-axis labels
                    datasets: [{
                        label: 'Data', // Optional label
                        data: dataAverageWaitingTimes, // Y-axis values
                        borderColor: '#e64a19', // Line color (orange)
                        backgroundColor: 'rgba(230, 74, 25, 0.3)', // Filled area color with opacity
                        borderWidth: 3, // Thickness of the line
                        fill: true, // Fill the area under the line
                        pointRadius: 5, // Size of data points
                        pointBackgroundColor: '#e64a19', // Color of data points
                        pointBorderColor: '#fff', // Border color of data points
                        pointBorderWidth: 2, // Border width of data points
                        lineTension: 0
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }, // Hide X-axis grid lines
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }, // Customize X-axis font size
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e0e0e0'
                            }, // Y-axis grid lines color
                            ticks: {
                                stepSize: 1, // Y-axis step interval
                                font: {
                                    size: 12
                                }, // Customize Y-axis font size
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        }, // Hide the legend
                        tooltip: {
                            enabled: true
                        }, // Enable tooltips on hover
                    },
                },
            });
        }





        function renderAvgDriverWaitingDropOffChart(response) {
            const ctx = document.getElementById('avgdriverWaitingDropOff').getContext('2d');

            const labels = response.driverOfflineAvgDates;
            const data = response.averageOfflineDurations;
            $('#totalAverageWaitingTime2').html(response.totalAverageWaitingTime2);

            // Destroy existing chart instance if it exists
            if (window.avgDriverWaitingDropOffChart) {
                window.avgDriverWaitingDropOffChart.destroy();
            }

            // Create the line chart with filled area
            window.avgDriverWaitingDropOffChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // X-axis labels
                    datasets: [{
                        label: 'Data', // Optional label
                        data: data, // Y-axis values
                        borderColor: '#e64a19', // Line color (orange)
                        backgroundColor: 'rgba(230, 74, 25, 0.3)', // Filled area color with opacity
                        borderWidth: 3, // Thickness of the line
                        fill: true, // Fill the area under the line
                        pointRadius: 5, // Size of data points
                        pointBackgroundColor: '#e64a19', // Color of data points
                        pointBorderColor: '#fff', // Border color of data points
                        pointBorderWidth: 2, // Border width of data points
                        lineTension: 0
                    }, ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }, // Hide X-axis grid lines
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }, // Customize X-axis font size
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e0e0e0'
                            }, // Y-axis grid lines color
                            ticks: {
                                stepSize: 1, // Y-axis step interval
                                font: {
                                    size: 12
                                }, // Customize Y-axis font size
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        }, // Hide the legend
                        tooltip: {
                            enabled: true
                        }, // Enable tooltips on hover
                    },
                },
            });
        }



        function renderAvgOrderPerHourChart(response) {
            const ctx = document.getElementById('avgOrderPerHourChart').getContext('2d');
            const labels = response.ordersPerHours;
            const data = response.ordersPerHoursTotal;
            $('#totalAverageOfOrdersPerHour').html(response.ordersPerHoursaverageOrdersPerHour);

            // Destroy existing chart instance if it exists
            if (window.avgOrderPerHourgraph) {
                window.avgOrderPerHourgraph.destroy(); // Corrected variable name
            }

            // Create the line chart with filled area
            window.avgOrderPerHourgraph = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // X-axis labels
                    datasets: [{
                        label: 'Data', // Optional label
                        data: data, // Y-axis values
                        borderColor: '#e64a19', // Line color (orange)
                        backgroundColor: 'rgba(230, 74, 25, 0.3)', // Filled area color with opacity
                        borderWidth: 3, // Thickness of the line
                        fill: true, // Fill the area under the line
                        pointRadius: 5, // Size of data points
                        pointBackgroundColor: '#e64a19', // Color of data points
                        pointBorderColor: '#fff', // Border color of data points
                        pointBorderWidth: 2, // Border width of data points
                        lineTension: 0
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }, // Hide X-axis grid lines
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }, // Customize X-axis font size
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e0e0e0'
                            }, // Y-axis grid lines color
                            ticks: {
                                stepSize: 1, // Y-axis step interval
                                font: {
                                    size: 12
                                }, // Customize Y-axis font size
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false
                        }, // Hide the legend
                        tooltip: {
                            enabled: true
                        }, // Enable tooltips on hover
                    },
                },
            });
        }







        // Initial Bar Chart Data
        const defaultBarData = {
            labels: ['07', '08', '09', '10', '11', '12', '13'],
            datasets: [{
                    label: 'Green Section',
                    backgroundColor: '#4caf50',
                    data: [5, 3, 10, 45, 170, 180, 2],
                    stack: 'combined'
                },
                {
                    label: 'Brown Section',
                    backgroundColor: '#795548',
                    data: [0, 0, 5, 5, 15, 25, 0],
                    stack: 'combined'
                }
            ]
        };
        createBarChart(defaultBarData);
        // createOperatorBarChart(defaultBarData)

        // Initial Donut Chart Data



        // Extend Flatpickr to jQuery
        $.fn.flatpickr = function(options) {
            return this.each(function() {
                flatpickr(this, options);
            });
        };
        // 3. Flatpickr Setup
        $('#datePicker').flatpickr({
            mode: 'range',
            enableTime: true,         // Enables time selection
            dateFormat: 'Y-m-d H:i',  // Includes both date & time format
            onClose: function(selectedDates) {
                sendChartData();
            },
        });

        // Handle Date Selection to Update Both Charts
        function handleDateSelection(selectedDates) {

            const startDate = new Date(selectedDates[0]);
            const endDate = new Date(selectedDates[1]);


            // Check if the dates are the same (using getTime for accuracy)
            const datesAreEqual = startDate.getTime() === endDate.getTime();


            if (selectedDates.length === 1 || (selectedDates.length === 2 && datesAreEqual)) {
                const singleDayData = {
                    labels: ['07', '08', '09', '10', '11', '12', '13'],
                    datasets: [{
                            label: 'Green Section',
                            backgroundColor: '#4caf50',
                            data: [5, 3, 10, 45, 170, 180, 2],
                            stack: 'combined'
                        },
                        {
                            label: 'Brown Section',
                            backgroundColor: '#795548',
                            data: [0, 0, 5, 5, 15, 25, 0],
                            stack: 'combined'
                        }
                    ]
                };
                createBarChart(singleDayData);
                createOperatorBarChart(singleDayData);
                // createOperatorBarChart(singleDayData);

                const singleDayDonutData = {
                    labels: ['Morning', 'Afternoon', 'Evening'],
                    datasets: [{
                        label: 'Donut Data',
                        data: [30, 50, 20],
                        backgroundColor: ['#4caf50', '#795548', '#ff9800']
                    }]
                };
                createDonutChart(singleDayDonutData);
            } else {
                const dateLabels = [];
                const greenData = [];
                const brownData = [];

                let currentDate = new Date(selectedDates[0]);
                while (currentDate <= selectedDates[1]) {
                    dateLabels.push(currentDate.toISOString().split('T')[0]);
                    greenData.push(Math.floor(Math.random() * 150) + 50);
                    brownData.push(Math.floor(Math.random() * 50) + 10);
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                const rangeBarData = {
                    labels: dateLabels,
                    datasets: [{
                            label: 'Green Section',
                            backgroundColor: '#4caf50',
                            data: greenData,
                            stack: 'combined'
                        },
                        {
                            label: 'Brown Section',
                            backgroundColor: '#795548',
                            data: brownData,
                            stack: 'combined'
                        }
                    ]
                };
                createBarChart(rangeBarData);


                const rangeDonutData = {
                    labels: ['Green Total', 'Brown Total'],
                    datasets: [{
                        label: 'Donut Data',
                        data: [
                            greenData.reduce((a, b) => a + b, 0),
                            brownData.reduce((a, b) => a + b, 0)
                        ],
                        backgroundColor: ['#4caf50', '#795548']
                    }]
                };
                createDonutChart(rangeDonutData);
            }
        }





        $('#seeMoreDetailBtn').click(function() {
            getClientsOrdersData();
        })


        $('#driver-data-btn').click(function() {
            getDriversData();
        })



        function getClientsOrdersData() {

            const clientId = $('#clientFilter').val();



            $.ajax({
                url: '{{ route('get-clients-orders-data') }}',
                method: 'GET',
                data: {

                    client: clientId
                },
                success: function(response) {
                    const tableBody = $('#clients-orders-data');
                    tableBody.empty();

                    response.forEach(client => {
                        const tableRow = `
                            <tr class="table-row">
                                <td>${client.client_id}</td>
                                <td class="clientImageContainer">
                                    <div class="clientImageWrapper">
                                        <img src="${client.image}" alt="${client.full_name}" >
                                    </div>
                                </td>
                            <td>
                                    <div class="d-flex gap-1 flex-column align-items-center">
                                        <p class="smFontInTable">${client.full_name}</p>
                                    </div>
                                </td>
                                <td class="smFontInTable">${client.pending_orders}</td>
                                <td class="smFontInTable">${client.active_orders}</td>
                                <td class="smFontInTable">${client.failed_orders || '-'}</td>
                                <td class="smFontInTable">${client.canceled_orders || '-'}</td>
                                <td class="smFontInTable">${client.delivered_orders || '-'}</td>
                                <td class="smFontInTable">${client.average_waiting_time}</td>
                                <td class="smFontInTable">${client.average_delivery_time}</td>
                            </tr>
                        `;

                        tableBody.append(tableRow); // Add the row to the table
                    });
                },
                error: function(error) {
                    console.error('Error fetching client data:', error);
                }
            });


        }

        function getDriversData() {
            if (driverCurrentRequest) {
                console.log('aborting previous request');
                driverCurrentRequest.abort();
            }

            driverCurrentRequest = $.ajax({
                url: '{{ route('get-dashboard-drivers-data') }}',
                method: 'GET',
                data: {

                    search: $('#search').val()
                },
                success: function(response) {
                    initMapAssignWorker(response);
                    const tableBody = $('#driver-data-table');
                    tableBody.empty();
                    response.forEach(driver => {

                        const newRow = `
                    <tr class="table-row">
                        <td class="DriverImageContainer">
                            <div class="DriverImageWrapper">
                                <img src="${driver.image}" alt="DriverImage">
                            </div>
                        </td>
                        <td class="text-left">
                            <p class="mb-1">${driver.full_name}</p>
                            <p class="smFontInTable">${driver.phone}</p>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-column align-items-center">
                                <p class=" d-flex align-items-center gap-2">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.9751 3.01903L15.9351 1.28703C15.9474 1.26483 15.9552 1.24044 15.958 1.21524C15.9609 1.19003 15.9588 1.16452 15.9517 1.14014C15.9447 1.11577 15.933 1.09301 15.9172 1.07317C15.9014 1.05334 15.8818 1.03681 15.8596 1.02453C15.8374 1.01225 15.813 1.00446 15.7878 1.00161C15.7626 0.998761 15.7371 1.0009 15.7127 1.00791C15.6884 1.01492 15.6656 1.02667 15.6458 1.04247C15.6259 1.05828 15.6094 1.07783 15.5971 1.10003L14.6271 2.85003C13.7991 2.48692 12.9048 2.29944 12.0006 2.29944C11.0965 2.29944 10.2022 2.48692 9.37413 2.85003L8.40413 1.10003C8.37933 1.05494 8.33763 1.02155 8.28822 1.00721C8.2388 0.992859 8.18571 0.99873 8.14063 1.02353C8.09554 1.04833 8.06215 1.09002 8.0478 1.13943C8.03346 1.18885 8.03933 1.24194 8.06413 1.28703L9.02413 3.01903C8.111 3.46988 7.33952 4.16328 6.79415 5.0233C6.24879 5.88333 5.95056 6.87683 5.93213 7.89503H18.0691C18.0505 6.87662 17.752 5.88297 17.2062 5.02293C16.6605 4.16288 15.8886 3.4696 14.9751 3.01903ZM9.20013 5.67403C9.09981 5.67403 9.00175 5.64427 8.91835 5.58851C8.83495 5.53276 8.76997 5.45352 8.73162 5.36082C8.69328 5.26812 8.6833 5.16612 8.70294 5.06775C8.72259 4.96937 8.77097 4.87904 8.84198 4.80817C8.91298 4.73731 9.00341 4.6891 9.10183 4.66965C9.20024 4.6502 9.30222 4.66038 9.39484 4.69891C9.48746 4.73744 9.56658 4.80257 9.62217 4.88608C9.67776 4.96959 9.70732 5.06771 9.70713 5.16803C9.70686 5.30232 9.65333 5.43102 9.55828 5.52589C9.46322 5.62075 9.33442 5.67403 9.20013 5.67403ZM14.8021 5.67403C14.7018 5.67403 14.6037 5.64427 14.5203 5.58851C14.437 5.53276 14.372 5.45352 14.3336 5.36082C14.2953 5.26812 14.2853 5.16612 14.3049 5.06775C14.3246 4.96937 14.373 4.87904 14.444 4.80817C14.515 4.73731 14.6054 4.6891 14.7038 4.66965C14.8022 4.6502 14.9042 4.66038 14.9968 4.69891C15.0895 4.73744 15.1686 4.80257 15.2242 4.88608C15.2798 4.96959 15.3093 5.06771 15.3091 5.16803C15.3089 5.30232 15.2553 5.43102 15.1603 5.52589C15.0652 5.62075 14.9364 5.67403 14.8021 5.67403ZM5.93013 17.171C5.92986 17.3641 5.96771 17.5553 6.04151 17.7337C6.1153 17.9121 6.22358 18.0742 6.36015 18.2107C6.49672 18.3472 6.65888 18.4554 6.83734 18.529C7.01581 18.6027 7.20706 18.6404 7.40013 18.64H8.37313V21.64C8.37313 22.0009 8.51646 22.3469 8.77161 22.602C9.02675 22.8572 9.3728 23.0005 9.73362 23.0005C10.0945 23.0005 10.4405 22.8572 10.6956 22.602C10.9508 22.3469 11.0941 22.0009 11.0941 21.64V18.64H12.9081V21.64C12.9081 22.0007 13.0514 22.3466 13.3065 22.6017C13.5615 22.8567 13.9074 23 14.2681 23C14.6288 23 14.9747 22.8567 15.2298 22.6017C15.4848 22.3466 15.6281 22.0007 15.6281 21.64V18.64H16.6021C16.7949 18.6402 16.9859 18.6023 17.1641 18.5286C17.3422 18.4548 17.5041 18.3467 17.6405 18.2104C17.7768 18.074 17.8849 17.9121 17.9586 17.734C18.0324 17.5558 18.0703 17.3648 18.0701 17.172V8.37503H5.93013V17.171ZM4.06313 8.14103C3.88444 8.14103 3.70751 8.17624 3.54244 8.24465C3.37738 8.31306 3.22741 8.41332 3.10111 8.53972C2.97481 8.66611 2.87465 8.81615 2.80636 8.98127C2.73807 9.14639 2.70299 9.32335 2.70313 9.50203V15.171C2.70313 15.3496 2.7383 15.5265 2.80665 15.6915C2.875 15.8565 2.97517 16.0064 3.10146 16.1327C3.22775 16.259 3.37767 16.3592 3.54268 16.4275C3.70768 16.4959 3.88453 16.531 4.06313 16.531C4.24172 16.531 4.41857 16.4959 4.58357 16.4275C4.74858 16.3592 4.8985 16.259 5.02479 16.1327C5.15108 16.0064 5.25126 15.8565 5.3196 15.6915C5.38795 15.5265 5.42313 15.3496 5.42313 15.171V9.50203C5.42313 9.32343 5.38795 9.14658 5.3196 8.98158C5.25126 8.81658 5.15108 8.66665 5.02479 8.54036C4.8985 8.41408 4.74858 8.3139 4.58357 8.24555C4.41857 8.17721 4.24172 8.14203 4.06313 8.14203M19.9351 8.14203C19.7564 8.14203 19.5795 8.17724 19.4144 8.24565C19.2494 8.31406 19.0994 8.41432 18.9731 8.54072C18.8468 8.66711 18.7466 8.81715 18.6784 8.98227C18.6101 9.14739 18.575 9.32435 18.5751 9.50303V15.172C18.5751 15.3506 18.6103 15.5275 18.6786 15.6925C18.747 15.8575 18.8472 16.0074 18.9735 16.1337C19.0997 16.26 19.2497 16.3602 19.4147 16.4285C19.5797 16.4969 19.7565 16.532 19.9351 16.532C20.1137 16.532 20.2906 16.4969 20.4556 16.4285C20.6206 16.3602 20.7705 16.26 20.8968 16.1337C21.0231 16.0074 21.1233 15.8575 21.1916 15.6925C21.2599 15.5275 21.2951 15.3506 21.2951 15.172V9.50203C21.2951 9.14133 21.1518 8.79541 20.8968 8.54036C20.6417 8.28531 20.2958 8.14203 19.9351 8.14203Z" fill="#CECECE"></path></svg>
                                            <span>1.0.0</span>

                                </p>
                            </div>
                        </td>

                        <td>
                            <p class="smFontInTable">${driver.vehicle}</p>
                        </td>




                        <td class="smFontInTable">
                            ${driver.attendance_rate}
                        </td>
                        <td>
                            ${driver.acceptance_rate}
                        </td>
                        <td>
                            ${driver.status}
                        </td>
                    </tr>
                `;
                        tableBody.append(newRow);
                    });


                },
                complete: function() {
                    driverCurrentRequest =
                        null;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        $("#search").on("keyup change ", function() {

            PAGE_NUMBER = 1;
            getDriversData();

        });







        function initMapAssignWorker(drivers) {
            console.log('Initializing initMapAssignWorker...');

            // Initialize the map
            mapDriversAssign = new google.maps.Map(document.getElementById('formMap'), {
                center: {
                    lat: 24.7136,
                    lng: 46.6753
                },
                zoom: 5,
                mapTypeId: "roadmap", // Adjust zoom level as needed
            });

            // Clear existing markers
            markersDriversAssign.forEach(marker => marker.setMap(null));
            markersDriversAssign = [];

            // Create a new info window instance
            infoWindowDriversAssign = new google.maps.InfoWindow();

            // Create markers for each driver
            drivers.forEach(driver => {
                // Create a marker element
                const markerElement = createMarkerElement2(driver.image, true);

                // Apply border color based on driver status
                if (driver.status_value === 1) {
                    markerElement.style.border = '3px solid green'; // Green border for status 1
                } else {
                    markerElement.style.border = '3px solid red'; // Red border for other statuses
                }

                // Create a custom marker
                const marker = createCustomMarker2(driver, markerElement, driver.lat, driver.lng);

                // Click event to show info window with driver details
                google.maps.event.addDomListener(markerElement, 'click', () => {



                });

                markersDriversAssign.push(marker);
            });
        }


        function createMarkerElement2(imageUrl, isDriver = false) {
            const markerElement = document.createElement('div');
            markerElement.style.width = '40px';
            markerElement.style.height = '40px';

            if (isDriver) {
                // Use driver profile image or default user icon
                markerElement.style.backgroundImage = `url(${imageUrl || defaultUserIcon2})`;
            } else {
                // Use shop profile image or default home icon
                markerElement.style.backgroundImage = `url(${imageUrl || defaultHomeIcon2})`;
            }

            markerElement.style.backgroundSize = 'cover';
            markerElement.style.backgroundPosition = 'center';
            markerElement.style.borderRadius = '50%';
            markerElement.style.border = '2px solid #fff';
            markerElement.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
            return markerElement;
        }
        // Helper function to create a custom Google Maps marker
        function createCustomMarker2(data, markerElement, lat, lng) {
            const customMarker = new google.maps.OverlayView();

            customMarker.onAdd = function() {
                const panes = this.getPanes();
                panes.overlayImage.appendChild(markerElement);
            };

            customMarker.draw = function() {
                const position = this.getProjection().fromLatLngToDivPixel(new google.maps.LatLng(
                    parseFloat(lat),
                    parseFloat(lng)));
                markerElement.style.position = 'absolute';
                markerElement.style.left = `${position.x - 20}px`; // Adjust to center the marker
                markerElement.style.top = `${position.y - 20}px`; // Adjust to center the marker
            };

            customMarker.onRemove = function() {
                if (markerElement.parentNode) {
                    markerElement.parentNode.removeChild(markerElement);
                }
            };

            customMarker.setMap(mapDriversAssign);
            return customMarker;
        }







    });
</script>
