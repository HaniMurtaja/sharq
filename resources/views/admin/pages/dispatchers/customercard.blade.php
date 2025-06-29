<div class="customcard">
                        <div class="cardLeftSide mous-click-new" data-popup="{{ @$order->infoWindowContent }}"
                             ondblclick="openAssignDriverModal({{ @$order->id }})"
                             ondblclick="openAssignDriverModal({{ @$order->id }})"
                             onclick="openOrderPopup(this, {{ @$order->lat }},{{ @$order->lng }}, {{@$order->branch_lat}}, {{@$order->branch_lng}})">
                            <input type="checkbox">
                            <div class="cardImageWrapper">
                                <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                            </div>
                            <div class="cardContent">
                                <span>{{ @$order->branch?->name ?? @$order->branchIntegration?->name }}</span>
<span>{{ @$order->shop?->full_name ?? @$order->branchIntegration?->client?->full_name }}</span>
</div>
</div>
<div class="cardRightSide">
    <div class="cardIdStatus">

        <span>#{{ @$order->client_order_id ?? @$order->id }}</span>
        <span class="status">Created</span>
    </div>
    <div class="cardTimeOperations">
        <div class="driverCardImage">
            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
        </div>
        <p>{{ @$order->created_time }}</p>
    </div>

</div>
</div>
