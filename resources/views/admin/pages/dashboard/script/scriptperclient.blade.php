<!-- HTML container for the chart -->

<script>
    $(document).ready(function() {

        var pieOrdersPerClientChart = echarts.init(document.getElementById('orders-per-client-chart'));


        function loadPerClientChartData() {

            pieOrdersPerClientChart.showLoading();


            var requestData = {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                clientFilter: $('#clientFilter').val(),
                city_id: $('#city_id').val()
            };

            // Call the Laravel API using AJAX
            $.ajax({
                url: '{{ route('OrderDashboard.OrdersPerClientChartData') }}',
                type: 'GET',
                data: requestData,
                success: function(response) {
                    var allChartData = [];
                    var legendData = [];

                    var clientDataMap = {};

                    $.each(response.data, function(index, item) {
                        allChartData.push({
                            value: item.total_orders,
                            name: item.client_name,
                            
                            itemData: item
                        });
                        legendData.push(item.client_name);
                        clientDataMap[item.client_name] = item;
                    });

                    var itemsPerPage = 20;
                    var currentPage = 1;

                    function renderChartPage(page) {
                        currentPage = page;
                        var start = (page - 1) * itemsPerPage;
                        var end = start + itemsPerPage;
                        var pageData = allChartData.slice(start, end);

                        var option = {
                            tooltip: {
                                trigger: 'item',
                                formatter: function(params) {
                                    var item = params.data.itemData;
                                    return `
                                        <strong>${params.name}</strong><br/>
                                        Total Orders: ${item.total_orders}<br/>
                                        ${item.total_branches ? 'Branches: ' + item.total_branches + '<br/>' : ''}
                                        ${item.total_drivers ? 'Operators: ' + item.total_drivers + '<br/>' : ''}
                                        
                                     
                                    `;
                                }
                            },
                            series: [{
                                name: 'Client',
                                type: 'pie',
                                radius: ['40%', '70%'],
                                avoidLabelOverlap: true,
                                minAngle:0,
                                itemStyle: {
                                    borderRadius: 10,
                                    borderColor: '#fff',
                                    borderWidth: 2
                                },
                                label: {
                                    show: true,
                                    formatter: '{b}: {c}'
                                },
                                emphasis: {
                                    label: {
                                        show: true,
                                        fontSize: '18',
                                        fontWeight: 'bold'
                                    }
                                },
                                labelLine: {
                                    show: true
                                },
                                data: pageData
                            }]
                        };

                        pieOrdersPerClientChart.setOption(option, true);
                    }

                    function renderPagination() {
                        var totalPages = Math.ceil(allChartData.length / itemsPerPage);
                        var pagination = '';
                        var maxVisiblePages = 7;
                        var halfVisible = Math.floor(maxVisiblePages / 2);
                        var startPage, endPage;

                        if (totalPages <= maxVisiblePages) {
                            startPage = 1;
                            endPage = totalPages;
                        } else {
                            if (currentPage <= halfVisible + 1) {
                                startPage = 1;
                                endPage = maxVisiblePages - 2;
                            } else if (currentPage >= totalPages - halfVisible) {
                                startPage = totalPages - (maxVisiblePages - 2);
                                endPage = totalPages;
                            } else {
                                startPage = currentPage - halfVisible + 1;
                                endPage = currentPage + halfVisible - 1;
                            }
                        }

                        pagination += `
                            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>`;

                        if (startPage > 1) {
                            pagination += `
                                <li class="page-item ${1 === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="1">1</a>
                                </li>`;
                            if (startPage > 2) {
                                pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                            }
                        }

                        for (var i = startPage; i <= endPage; i++) {
                            pagination += `
                                <li class="page-item ${i === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                </li>`;
                        }

                        if (endPage < totalPages) {
                            if (endPage < totalPages - 1) {
                                pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                            }
                            pagination += `
                                <li class="page-item ${totalPages === currentPage ? 'active' : ''}">
                                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                                </li>`;
                        }

                        pagination += `
                            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>`;

                        $('#chartPagination').html(pagination);
                    }

                    renderChartPage(1);
                    renderPagination();

                    $('#chartPagination').off('click').on('click', '.page-link', function(e) {
                        e.preventDefault();
                        var selectedPage = parseInt($(this).data('page'));
                        if (!isNaN(selectedPage)) {
                            renderChartPage(selectedPage);
                            renderPagination();
                        }
                    });

                    pieOrdersPerClientChart.hideLoading();
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data: " + error);
                    pieOrdersPerClientChart.hideLoading();
                }
            });
        }

        $('#filter').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();
            if (!fromDate || !toDate) {
                // alert("يرجى تحديد تاريخ البداية والنهاية.");
                return;
            }
            loadPerClientChartData();
        });

       
        $(window).on("resize", function() {
            setTimeout(function() {
                pieOrdersPerClientChart.resize();
            }, 100);
        });
        $(".sidebartoggler").on("click", function() {
            pieOrdersPerClientChart.resize();
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#clientreportBtn').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var clientFilter = $('#clientFilter').val();
            var city_id = $('#city_id').val();
            if (!fromDate || !toDate) {
                alert("يرجى تحديد تاريخ البداية والنهاية.");
                return;
            }
            // Clear previous data
            $('#clientreportTable tbody').empty();
            $('#totalOrders').empty();

            
            $.ajax({
                url: '{{ route('OrderDashboard.OrdersPerClientData') }}', 
                type: 'GET',
                data: {

                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    clientFilter: $('#clientFilter').val(),
                    city_id: $('#city_id').val()
                },
                success: function(response) {
                    $('#clientreportTable tbody').empty(); // Clear previous data
                    $.each(response.data, function(index, user) {
                        var row = '<tr>' +
                            '<td>' + user.client_name + '</td>' +
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
                        $('#clientreportTable tbody').append(row);
                    });

                    // Update order counts
                    $('#totalOrdersPerClient').text('Total Orders: ' + response.counts_orders);
                    $('#counts_delivered_ordersPerClient').text('Total Delivered: ' + response
                        .counts_delivered_orders);
                    $('#counts_cancel_ordersPerClient').text('Total Cancel: ' + response
                        .counts_cancel_orders);


                    var modalElement = document.getElementById('clientreportModal');
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
