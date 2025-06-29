@extends('admin.layouts.app')


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />

<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/orders.css') }}" />


@section('title')
    Show Export
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">
        @include('admin.pages.exports.orders.search')
        <div class="pxy-256 bg-white br-96 mb-192 ">
            <div class="table-responsive w-full overflow-x-auto  border br-64 ">
                <table id="order-list" class="text-center fw-semibold w-100">
                    <thead class="">
                        <tr>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">ID</th>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">Status</th>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">Download</th>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">CreatedAt</th>

                        </tr>
                    </thead>
                    <tbody style="text-align: center">
                        @forelse ($items as $order)
                            <tr class="fs-112 text-center black-58">
                                <td class="pxy-1288">{{ $order->id }}</td>

                                <td class="pxy-1288">
                                    @if ($order->is_ready)
                                        <span class="badge bg-success">جاهز</span>
                                    @else
                                        <span class="badge bg-warning">قيد المعالجة</span>
                                    @endif
                                </td>
                                <td class="pxy-1288">
                                    @if ($order->is_ready)
                                        <a href="{{ $order->full_patch }}" class="btn btn-sm btn-primary" download
                                            target="_blank">
                                            تحميل
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>لم يجهز بعد</button>
                                    @endif
                                </td>
                                <td class="pxy-1288">{{ $order->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد نتائج</td>
                            </tr>
                        @endforelse


                    </tbody>
                </table>

            </div>
            <div class="d-flex justify-content-between pagination mt-192">
                {!! $items->appends(request()->all())->links() !!}
            </div>
        </div>

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

        $(document).on('click', '.order-history-btn', function(e) {
            e.preventDefault();

            let dataId = $(this).data('id');

            $.ajax({
                url: '{{ route('get-order-history') }}',
                type: 'GET',
                data: {
                    order_id: dataId,
                },
                success: function(response) {
                    // console.log('Success:', response);

                    // Populate modal content
                    let createdAt = response.order.created_at ? new Date(response.order
                            .created_at) :
                        null;
                    let client_order_id = response.order_number || '---';
                    $('#modalTitle').text(
                        `Logs - #${response.order.id} Order ID - #${client_order_id} `
                    );
                    $('#brand').text(response.brand || '---');

                    $('#created_at').text(createdAt ? createdAt.toLocaleString() : '---');
                    $('#cancel_reason').text(response.cancel_reason);
                    $('#branch').text(response.branch || '---');
                    $('#customerPhone').text(response.order.customer_phone || '---');
                    $('#customerName').text(response.order.customer_name || '---');

                    // Populate history table
                    let historyRows = '';

                    response.histories.forEach(history => {
                        let description = history.description || '--';
                        historyRows += `
                    <tr>
                     <td colspan="2">${history.date_log} &nbsp;&nbsp;</td>
                         <td></td>
                        <td colspan="3">${history.action} &nbsp;&nbsp;</td>
                        <td></td>
                        <td colspan="5">${description} &nbsp;&nbsp;</td>
                    </tr>`;
                    });
                    $('#historyTable').html(historyRows);

                    // Show the modal
                    $('#orderHistoryModal').fadeIn();
                },

                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                }
            });

        });



        $(document).on('click', '.unifonic-response-btn', function(e) {
            e.preventDefault();

            let dataId = $(this).data('id');

            $.ajax({
                url: '{{ route('UnifonicResponse') }}',
                type: 'GET',
                data: {
                    order_id: dataId,
                },
                success: function(response) {



                    const container = $('#messages_contenair');
                    container.empty();

                    response.logs.forEach(log => {


                        container.append(`<p>${log.response_body}</p>`);
                    });

                },

                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                }
            });

        });



        // $(document).on('click', '.order-view-btn', function(e) {
        //     e.preventDefault();

        //     let dataId = $(this).data('id');

        //     $.ajax({
        //         url: '{{ route('get-order-history') }}',
        //         type: 'GET',
        //         data: {
        //             order_id: dataId,
        //         },
        //         success: function(response) {

        //             let createdAt = response.order.created_at ? new Date(response.order
        //                     .created_at) :
        //                 null;
        //             let client_order_id = response.order_number || '---';
        //             $('#modalTitle').text(
        //                 `Logs - #${response.order.id} Order ID - #${client_order_id} `
        //             );
        //             $('#brand').text(response.brand || '---');

        //             $('#created_at').text(createdAt ? createdAt.toLocaleString() : '---');
        //             $('#cancel_reason').text(response.cancel_reason);
        //             $('#branch').text(response.branch || '---');
        //             $('#customerPhone').text(response.order.customer_phone || '---');
        //             $('#customerName').text(response.order.customer_name || '---');

        //             // Populate history table
        //             let historyRows = '';

        //             response.histories.forEach(history => {
        //                 let description = history.description || '--';
        //                 historyRows += `
        //                 <tr>
        //                  <td colspan="2">${history.date_log} &nbsp;&nbsp;</td>
        //                      <td></td>
        //                     <td colspan="3">${history.action} &nbsp;&nbsp;</td>
        //                     <td></td>
        //                     <td colspan="5">${description} &nbsp;&nbsp;</td>
        //                 </tr>`;
        //             });
        //             $('#historyTable').html(historyRows);

        //             // Show the modal
        //             $('#orderHistoryModal').fadeIn();
        //         },

        //         error: function(xhr, status, error) {
        //             console.error('Error:', xhr.responseText);
        //         }
        //     });

        // });

        $(document).on('shown.bs.modal', '#viewOrderModal', function() {
            function initializeSelect2(parent, id, placeholder = null, search = Infinity) {
                $(`#${id}`).val(null).trigger("change.select2"); // Clear any existing value

                // Select2 configuration
                const select2Config = {
                    dropdownParent: parent,
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: search, // Ensures the search box is always visible
                    language: {
                        searching: function() {
                            return "Searching...";
                        },
                        noResults: function() {
                            return "No matching results found";
                        }
                    }
                };

                // Add placeholder only if provided
                if (placeholder) {
                    select2Config.placeholder = placeholder;
                } else {
                    const firstOptionValue = $(`#${id} option:first`).val();
                    if (firstOptionValue) {
                        $(`#${id}`).val(firstOptionValue).trigger("change");
                    }
                }

                // Initialize Select2
                $(`#${id}`).select2(select2Config);


            }

            initializeSelect2("#viewOrderModal .modal-body .viewJobStatus", "job-status");
            initializeSelect2("#viewOrderModal .modal-body .viewOrderType", "order-type");
            initializeSelect2("#viewOrderModal .modal-body .viewPaymentMethod", "order-payment-method");
            initializeSelect2("#viewOrderModal .modal-body .viewClientLocations", "clients", null, 0);
            initializeSelect2("#viewOrderModal .modal-body .viewBranchsLocation", "branchs", null, 0);
        });

        $(document).on('shown.bs.modal', '#editOrderModal', function() {
            function initializeSelect2(parent, id, placeholder = null, search = Infinity) {
                $(`#${id}`).val(null).trigger("change.select2"); // Clear any existing value

                // Select2 configuration
                const select2Config = {
                    dropdownParent: parent, // Dynamically find the closest modal body
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: search, // Ensures the search box is always visible
                    language: {
                        searching: function() {
                            return "Searching...";
                        },
                        noResults: function() {
                            return "No matching results found";
                        }
                    }
                };
                select2Config.dropdownAutoWidth = true;


                // Add placeholder only if provided
                if (placeholder) {
                    select2Config.placeholder = placeholder;
                } else {
                    const firstOptionValue = $(`#${id} option:first`).val();
                    if (firstOptionValue) {
                        $(`#${id}`).val(firstOptionValue).trigger("change");
                    }
                }

                // Initialize Select2
                $(`#${id}`).select2(select2Config);


            }

            initializeSelect2("#editOrderModal .modal-body .jobStatus", "edit-job-status");
            initializeSelect2("#editOrderModal .modal-body .typeOne", "edit-order-type-one");
            initializeSelect2("#editOrderModal .modal-body .typeTwo", "edit-order-type-two");
            initializeSelect2("#editOrderModal .modal-body .paymentMethod",
                "edit-order-payment-method");
            initializeSelect2("#editOrderModal .modal-body .paidStatus", "order-paid");
            initializeSelect2("#editOrderModal .modal-body .clientsLocation", "edit-clients", null, 0);
            initializeSelect2("#editOrderModal .modal-body .branchsLocation", "edit-branchs", null, 0);
            initializeSelect2("#editOrderModal .modal-body .operatorsList", "edit-operators",
                "operators", 0);


            $(document).on("change", "#edit-operators", function() {
                let selectedText = $("#edit-operators option:selected").text();

                $("#editOrderModal .text-slide").text(selectedText);
            });



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



    $(document).on("mouseenter", ".text-slide-wrapper", function() {

        const $textSlide = $(this).find(".text-slide");
        const textContent = $textSlide.text();
        console.log(textContent)
        const textLength = textContent.length;
        const $wrapper = $(this);

        const textWidth = $textSlide[0].scrollWidth;
        const wrapperWidth = $wrapper.outerWidth();

        const moveDistance = textWidth - wrapperWidth;
        const calcValue = `-${moveDistance}px`;

        const keyframes = `
                @keyframes textSlide {
                    0% {
                        transform: translateX(0); /* Start position */
                    }
                    50% {
                        transform: translateX(${calcValue}); /* Move to the last letter */
                    }
                    100% {
                        transform: translateX(0); /* Return to start */
                    }
                }`;
        // Check if the keyframes are already appended to avoid duplication
        if ($("style#textSlideKeyframe").length === 0) {
            $("head").append(`<style id="textSlideKeyframe">${keyframes}</style>`);
        } else {
            // If style already exists, update the keyframes
            $("style#textSlideKeyframe").html(keyframes);
        }



    });

</script>
{{-- <script>
    setTimeout(function () {
        location.reload();
    }, 60000); // 10000 ملي ثانية = 10 ثواني
</script> --}}
