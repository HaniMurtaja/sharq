<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;
    var pusher = new Pusher('1b6199ab825d2ac03096', {
        cluster: 'eu'
    });

    @if (auth()->user()->user_role == \App\Enum\UserRole::CLIENT)
        var client_data = {{ auth()->id() }};
    @endif

    @if (auth()->user()->user_role == \App\Enum\UserRole::BRANCH)
        var branch_data = {{ auth()->user()->branch_id }};
    @endif

    @if (auth()->user()->user_role == \App\Enum\UserRole::DISPATCHER)
        var allow_city = @json($allow_city);
    @endif
    var user_role = {{ auth()->user()->user_role }};
    var roles = [{{ \App\Enum\UserRole::CLIENT->value }}, {{ \App\Enum\UserRole::BRANCH->value }},
        {{ \App\Enum\UserRole::DISPATCHER->value }}
    ]
    var channel = pusher.subscribe('orders');
    channel.bind('pusher:subscription_succeeded', function() {


        channel.bind('refresh_pages_token', function(data) {
            window.location.reload();
        });
        channel.bind('createOrder', function(data) {
            var widget = updateWidget(data.order);

            if (roles.includes(user_role)) {
                if (user_role == {{ \App\Enum\UserRole::BRANCH->value }} && data.order.branch_id ==
                    branch_data) {
                    $('.collapseContent-All-orders').find('.order_items').prepend(widget);
                    $('.collapseContent-pending-order').find('.order_items').prepend(widget);

                }
                if (user_role == {{ \App\Enum\UserRole::CLIENT->value }} && data.order.ingr_shop_id ==
                    client_data) {
                    $('.collapseContent-All-orders').find('.order_items').prepend(widget);
                    $('.collapseContent-pending-order').find('.order_items').prepend(widget);

                }
            } else {
                $('.collapseContent-All-orders').find('.order_items').prepend(widget);
                $('.collapseContent-pending-order').find('.order_items').prepend(widget);
            }

        });
        channel.bind('updateStatus', function(data) {
            // var open_widget = [];
            var openElements = $('.collapseContent').filter(function() {
                return $(this).css('max-height') !== '0px';
            });

            if (openElements.length > 0 && orderFilter == 1) {
                var widget = updateWidget(data.order);
                $('#order-id-' + data.order.id).remove();

                openElements.each(function() {
                    var textContent = $(this).data('status');
                    if (data.order.order_label.includes(textContent)) {
                        var check_if_exist = $('.collapseContent-' + textContent).find(
                            '.order_items').find('#order-id-' + data.order.id);
                        if (check_if_exist.length == 0) {
                            if (roles.includes(user_role)) {
                                if (user_role == {{ \App\Enum\UserRole::BRANCH->value }} && data
                                    .order.branch_id == branch_data) {
                                    $('.collapseContent-' + textContent).find('.order_items')
                                        .prepend(widget);
                                }
                                if (user_role == {{ \App\Enum\UserRole::CLIENT->value }} && data
                                    .order.ingr_shop_id == client_data) {
                                    $('.collapseContent-' + textContent).find('.order_items')
                                        .prepend(widget);
                                }
                                if (user_role == {{ \App\Enum\UserRole::DISPATCHER->value }} &&
                                    allow_city.includes(data.order.city)) {
                                    $('.collapseContent-' + textContent).find('.order_items')
                                        .prepend(widget);
                                }
                            } else {
                                $('.collapseContent-' + textContent).find('.order_items')
                                    .prepend(widget);

                            }
                        }
                    }

                });
            }


        });
    });



    function updateWidget(data) {
        let dropdownItems = '';

        // Check if the user can assign orders
        dropdownItems += `
                            @can('can_assign_orders')
            <li>
                <a class="dropdown-item open-modal-test" href="#"
                data-bs-toggle="modal" data-bs-target="#exampleModal"
                data-id="${data.id}">
                                        Assign
                                        </a>
                                    </li>
                                    @endcan
            `;
        dropdownItems += `
                            @can('can_unassign_orders')
        <li>
            <a class="dropdown-item UnAssign"
             data-id="${data.id}"
                                     href="#">
                                        UnAssign
                                        </a>
                                    </li>
                                    @endcan
        `;

        dropdownItems += `
                            @can('can_change_status_to_delivered_orders')
        <li>
            <a class="dropdown-item ChangeStatusToDelivered"
             data-id="${data.id}"
                                     href="#">
                                       Delivered
                                        </a>
                                    </li>
                                    @endcan
        `;

        // Check if the user can cancel orders and the order status matches
        if ((data.status_value === 22 || data.status_value === 21)) {
            dropdownItems += `
                            @can('can_accept_cancel_request')
                <li>
                    <a class="dropdown-item accept-cancel-request" href="#"
                    data-id="${data.id}">
                                Accept
                                </a>
                            </li>
                            @endcan`;
        }

        // Check if the user can make a cancel request and the status does not match specific values
        if (![9, 10, 22].includes(data.status_value)) {
            dropdownItems += `
                            @can('can_make_cancel_request')
                <li>
                    <a class="dropdown-item request-client-cancel" href="#"
                    data-id="${data.id}">
                                    Cancel
                                    </a>
                                </li>
                                @endcan`;
        }

        // Always add the log item
        dropdownItems += `
                                <li>
                                    <a class="dropdown-item order-history-btn" href="#"
                                    data-id="${data.id}" data-bs-toggle="modal"
                                    data-bs-target="#historyModal">
                                    Log
                                    </a>
                                </li>`;
        const statusDateTime = new Date(data
            .status_date_time); // Convert to Date object
        var userRole = '{{ Auth::user()->role_id }}';
        var customerCardHtml = `
                <div class="customcard" id="order-id-${data.id}">
                    <!-- Checkbox on the top-left corner -->
                    <input type="checkbox" class="card-checkbox position-absolute top-8px" data-order-id="${data.id}" >

                    <div class="cardLeftSide mous-click-new"

            onclick="handleClick(event, ${data.id}, ${data.lat}, ${data.lng}, ${data.branch_lat}, ${data.branch_lng}, ${userRole})"
                        >
                        <div class="cardImageWrapper">
                            <img src="${data.shop_profile || 'https://fakeimg.pl/300/'}" alt="Branch Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>${data.branch_name}</span>
                            <span>${data.shop_name}</span>
                            <span class="status" style="background-color: ${getStatusColor(data.status_label)};color: #fff;">
                                ${data.status_label}
                            </span>
                            <!--<span class="status">${data.status_label}</span>-->
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">
                        <div class="text-slide-wrapper">
                                            <span class="text-slide"> #${data.order_id}</span>
                            </div>
                            <!--<span>#${data.order_id}</span>-->
                            <div class="dropdown">
                                <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg width="1.6rem" height="1.6rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3.33333 9.33325C2.6 9.33325 2 8.73325 2 7.99992C2 7.26659 2.6 6.66659 3.33333 6.66659C4.06667 6.66659 4.66667 7.26659 4.66667 7.99992C4.66667 8.73325 4.06667 9.33325 3.33333 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                        <path d="M12.6673 9.33325C11.934 9.33325 11.334 8.73325 11.334 7.99992C11.334 7.26659 11.934 6.66659 12.6673 6.66659C13.4007 6.66659 14.0007 7.26659 14.0007 7.99992C14.0007 8.73325 13.4007 9.33325 12.6673 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                        <path d="M7.99935 9.33325C7.26602 9.33325 6.66602 8.73325 6.66602 7.99992C6.66602 7.26659 7.26602 6.66659 7.99935 6.66659C8.73268 6.66659 9.33268 7.26659 9.33268 7.99992C9.33268 8.73325 8.73268 9.33325 7.99935 9.33325Z" stroke="#949494" stroke-width="1.2"></path>
                                    </svg>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">${dropdownItems}</ul>
                            </div>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                                <img src="${data.driver_photo || 'https://cdn-icons-png.flaticon.com/512/149/149071.png'}" alt="Driver Image"/>
                            </div>
                            <p class="time-difference" data-cancelled_date = "${data.cancelled_date}" data-delivery-date ="${data.delivery_date}" data-created-date = "${data.created_date}" data-status-value= "${data.status_value}" data-time="${data.status_date_time}"></p>
                        </div>
                    </div>
                </div>
                `;

        return customerCardHtml

    }

    $(document).ready(function() {








        $(document).on("mouseenter", ".text-slide-wrapper", function() {

            const $textSlide = $(this).find(".text-slide");
            const textContent = $textSlide.text();
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

    })
</script>
<script>
    function getStatusColor(status) {
        switch (status) {
            case 'Order created':
                return '#4CAF50'; // Green - Success
            case 'Pending driver acceptance':
                return '#2196F3'; // Blue - Information
            case 'Pending order preparation':
                return '#03A9F4'; // Light Blue - Informational
            case 'Arrived to pickup':
                return '#FF9800'; // Orange - In Progress
            case 'Order picked up':
                return '#F06292'; // Amber - In Progress
            case 'Arrived to dropoff':
                return '#009688'; // Teal - In Progress
            case 'Order delivered':
                return '#388E3C'; // Dark Green - Success
            case 'Order cancelled':
                return '#F44336'; // Red - Error
            case 'Driver acceptance timeout':
                return '#FFEB3B'; // Yellow - Warning
            case 'Driver accepted the order':
                return '#3F51B5'; // Indigo - Confirmation
            case 'Driver rejected the order':
                return '#FF5722'; // Deep Orange - Rejection
            case 'Order Unassigned':
                return '#9E9E9E'; // Grey - Neutral
            case 'Order failed':
                return '#B71C1C'; // Dark Red - Critical Error
            case 'Order Cancellation is being Proccessed':
                return '#9C27B0'; // Purple - Special
            case 'Client Cancellation Request is being Proccessed':
                return '#673AB7'; // Deep Purple - Special
            default:
                return '#9E9E9E'; // Grey - Default Neutral
        }
    }
</script>
