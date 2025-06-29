{{-- <div wire:poll.3s>
    <div class="card-header" id="headingThree">
        <h5 class="mb-0">
            <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                data-target="#cancellationRequestsOrders" aria-expanded="false" aria-controls="collapseThree">
                Drivers cancellation requests <p style="color: red; display:inline">({{ $orders_count }})</p>
            </button>
        </h5>
    </div>
    <div id="cancellationRequestsOrders" wire:ignore.self class="collapse" aria-labelledby="headingThree"
        data-parent="#accordion">
        <div class="card-body" style="overflow-x: auto;">
             <table class="table table-responsive table-sm mous-click-new">
                <thead>
                    <tr>

                        <td>ID</td>
                        <td>CO ID</td>
                        <td>Date</td>
                        <td>Shop</td>
                        <td>Branch</td>
                        <td>Customer name</td>
                        <td>Customer phone</td>

                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('dispatcher'))
                            <td></td>
                            <td></td>
                        @endif
                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('dispatcher'))
                            <td></td>
                        @endif
                        <td></td>


                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td> {{ $order->id }} </td>
                            <td> {{ $order->client_order_id }}</td>
                            <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="order-row" data-id="{{ $order->id }}" data-lat="{{ $order->lat }}"
                                data-lng="{{ $order->lng }}" data-branch="{{ $order->branch?->name }}"
                                data-customer="{{ $order->shop?->full_name }}"> <a href="#"
                                    style="color: #343a40">
                                    {{ $order->shop?->full_name ?? $order->branchIntegration?->client?->full_name }}
                                </a></td>
                            <td>{{ $order->branch?->name ?? $order->branchIntegration?->name }}</td>
                            <td>{{ $order->customer_name }} </td>
                            <td>{{ $order->customer_phone }}</td>

                            @if ((auth()->user()->hasRole('admin') || auth()->user()->hasRole('dispatcher')) && $order->status->value == 21)
                                <td><a type="button" style="color: #f46624;; text:bold"
                                        wire:click="accept({{ $order->id }})">
                                        <i class="fa-solid fa-check"></i> </a></td>

                                <td><a type="button" style="color: #f46624;; text:bold"
                                        onclick="Livewire.dispatch('openModal', { component: 'write-note', arguments: { order_id: {{ $order->id }} }})">
                                        <i class="fa-solid fa-message"></i> </a></td>
                            @endif

                            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('dispatcher'))
                                <td><a type="button" style="color: #f46624;; text:bold"
                                        onclick="Livewire.dispatch('openModal', { component: 'assign-worker', arguments: { order: {{ $order }} }})">
                                        <i class="fa-regular fa-pen-to-square"></i> </a></td>
                                <td><a type="button" style="color: #f46624;; text:bold"
                                        onclick="Livewire.dispatch('openModal', { component: 'order-history', arguments: { order_id: {{ $order->id }} }})">
                                        <i class="nav-icon fas fa-file"></i> </a></td>
                            @endif
                        </tr>
                    @endforeach

                </tbody>
                <tfoot>

                </tfoot>

            </table>
            {{ $orders->links() }}
        </div>
    </div>
</div> --}}



<div wire:poll.3s>




    <div class="collapsible">
    <button type="button" style="background-color: #fff"
            class="flex items-center justify-between w-full px-2 mt-2 py-2 text-sm  rounded-md toggleButton collapseCustomBtn">
            <div class="flex items-center gap-2">
                <span class="w-0.5 h-6 bg-danger"></span>
                <span class="orderCount">Drivers cancellation requests ({{ $orders_count }})</span>
            </div>
            <span class="d-flex gap-1 align-items-center">
                <span class="delayedCount">
                    56 Delayed
                </span>
            <svg width="11.2px" height="5.6px" viewBox="0 0 14 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.19037 0.436467C1.48327 0.143574 1.95814 0.143574 2.25103 0.436467L6.5977 4.78313C6.81814 5.00357 7.18327 5.00357 7.40371 4.78313L11.7504 0.436467C12.0433 0.143574 12.5181 0.143574 12.811 0.436467C13.1039 0.72936 13.1039 1.20423 12.811 1.49713L8.46437 5.84379C7.65814 6.65002 6.34327 6.65002 5.53704 5.84379L1.19037 1.49713C0.89748 1.20423 0.89748 0.72936 1.19037 0.436467Z" fill="#949494"></path></svg>
            </span>
        </button>
        <div style="background-color: #fff; margin: 0 !important;position: relative;top: -3px; " class="overflow-hidden text-sm transition-all duration-500 ease-in-out collapseContent max-h-0 md:text-base"
            wire:ignore.self>
            <div class="flex flex-col " style="width: 96%;
            margin: auto !important;">

            @foreach ($orders as $order)

            <div class="customcard">
                    <div class="cardLeftSide mous-click-new" data-popup="{{ $order->infoWindowContent }}"
                                            ondblclick="openAssignDriverModal({{ $order->id }})"
                                            ondblclick="openAssignDriverModal({{ $order->id }})"
                                            onclick="openOrderPopup(this, {{ $order->lat }},{{ $order->lng }}, {{$order->branch_lat}}, {{$order->branch_lng}})">
                        <input type="checkbox">
                        <div class="cardImageWrapper">
                            <img src="https://fakeimg.pl/300/" alt="Driver Image" width="100" height="100">
                        </div>
                        <div class="cardContent">
                            <span>{{ $order->branch?->name ?? $order->branchIntegration?->name }}</span>
                            <span>{{ $order->shop?->full_name ?? $order->branchIntegration?->client?->full_name }}</span>
                        </div>
                    </div>
                    <div class="cardRightSide">
                        <div class="cardIdStatus">

                               <span>#{{ $order->client_order_id ?? $order->id }}</span>
                            <span class="status">Created</span>
                        </div>
                        <div class="cardTimeOperations">
                            <div class="driverCardImage">
                               <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Driver Image" width="100" height="100"/>
                            </div>
                            <p>{{ $order->created_time }}</p>
                        </div>

                    </div>
                </div>
                @endforeach




                {{ $orders->links() }}
            </div>
        </div>
    </div>







</div>
