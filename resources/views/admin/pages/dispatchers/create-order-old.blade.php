<div id="drawer" data-drawer="Dispatcher"
    class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-2/3">

    <div class="flex flex-col h-screen overflow-scroll">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700">
                On-demand Delivery
            </h5>
            <button id="close-drawer" class="text-gray-500 close-drawer" onclick="closeOrderDrawer()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 p-8">

            <form id="regForm" style="margin-top: 0px">
                @csrf
                <div class="stepwizard col-md-offset-3">
                    <div class="stepwizard-row setup-panel">
                        <div class="stepwizard-step">
                            <a href="#step-1" type="button" class="btn btn-default">1</a>
                            <p>Drop-off Detail</p>
                        </div>
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <div class="stepwizard-step">
                            <a href="#step-2" type="button" class="btn btn-default" disabled="disabled">2</a>
                            <p>Pickup Detail</p>
                        </div>
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <div class="stepwizard-step">
                            <a href="#step-3" type="button" class="btn btn-default" disabled="disabled">3</a>
                            <p>Summary</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="lat_order_hidden" name="lat_order_hidden" />
                <input type="hidden" id="long_order_hidden" name="lng_order_hidden" />
                <input type="hidden" id="lat_client_branch_hidden" name="lat_client_branch_hidden" />
                <input type="hidden" id="lng_client_branch_hidden" name="lng_client_branch_hidden" />
                <input hidden id="distance" name="distance">

                <div class="tab" id="step-1" style="display: block">
                    <div class="mt-5 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input type="text" placeholder="Name..." name="customer_name" id="firstName"
                                        name="first_name"
                                        class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor">
                                </p>
                                @error('customer_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input type="text" placeholder="Phone..." name="customer_phone" id="firstName"
                                        name="first_name"
                                        class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor">
                                </p>
                                @error('customer_phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <input type="text" placeholder="Search Box" id="pac-input"
                                class="w-full h-12 p-2 px-4 border rounded-lg controls border-gray1 focus:outline-none focus:border-mainColor">
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12">


                            <div wire:ignore id="formMap" style="width:100%; height:150px;"></div>
                            <span id="map_error"></span>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea name="instructions"
                                    class="w-full p-2 px-4 bg-white border rounded-lg textarea controls border-gray1 focus:outline-none focus:border-mainColor"
                                    placeholder="Instructions"></textarea>
                                @error('instructions')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div style="overflow:auto;" id="nextprevious">
                        <div style="float:right;">
                            <button type="button"
                                class="search-btn p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                                id="save-operator-btn" onclick="nextPrev(1)">
                                Next
                            </button>
                        </div>
                    </div>

                </div>


                <div class="tab" id="step-2" style="display: none">
                    <div class="mt-5 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control select2 custom-select2-search" style="width: 100%;"
                                    name="client_id" id="client_id">
                                    <option value="" selected="selected" disabled>Client </option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                    @endforeach

                                </select>
                                @error('client_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control select2" style="width: 100%;" name="branch_id"
                                    id="branch_id">
                                    <option value="" selected="selected" disabled>Branch</option>


                                </select>
                                @error('branch_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input placeholder="Client order ID..."
                                        class="w-full h-12 p-2 px-4 border rounded-lg controls border-gray1 focus:outline-none focus:border-mainColor"
                                        name="client_order_id">
                                </p>
                                @error('client_order_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input placeholder="Number of items..."
                                        class="w-full h-12 p-2 px-4 border rounded-lg controls border-gray1 focus:outline-none focus:border-mainColor"
                                        name="items_no">
                                </p>
                                @error('items_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input placeholder="Order value..."
                                        class="w-full h-12 p-2 px-4 border rounded-lg controls border-gray1 focus:outline-none focus:border-mainColor"
                                        name="order_value">
                                </p>
                                @error('order_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control select2" style="width: 100%;" name="payment_method">
                                    <option value="" selected="selected" disabled>Payment method
                                    </option>
                                    @foreach (App\Enum\PaymentType::cases() as $paymentType)
                                        <option value="{{ $paymentType->value }}">
                                            {{ $paymentType->getLabel() }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('payment_method')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control select2" style="width: 100%;" name="proof_action">
                                    <option value="" selected="selected" disabled>Proof of
                                        action
                                    </option>
                                    <option value="Image">Image</option>
                                    <option value="QR Code">QR Code</option>

                                </select>
                                @error('proof_action')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control select2" id="v_seelct" style="width: 100%;"
                                    name="vehicle_id">
                                    <option value="" selected="selected" disabled>Vehicles
                                    </option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                    @endforeach

                                </select>
                                @error('vehicle_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <textarea name="pickup_instructions"
                                    class="w-full p-2 px-4 bg-white border rounded-lg txtarea controls border-gray1 focus:outline-none focus:border-mainColor"
                                    placeholder="Pickup instructions"></textarea>
                                @error('pickup_instructions')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <textarea name="order_details"
                                    class="w-full p-2 px-4 bg-white border rounded-lg txtarea controls border-gray1 focus:outline-none focus:border-mainColor"
                                    placeholder="Order details"></textarea>
                                @error('order_details')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div style="overflow:auto;" class="tap2" id="nextprevious">
                        <div style="float:right;">
                            <button type="button"
                                class="prev-btn p-3 !px-20 !text-base text-white rounded-md bg-gray-400"
                                id="prevBtn" onclick="nextPrev(-1)">
                                Previous
                            </button>
                            <button type="button"
                                class="next-btn p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                                onclick="nextPrev(1)">Next</button>
                        </div>
                    </div>

                </div>


                <div class="tab" id="step-3" style="display: none">
                    <div class="mt-5 row">
                        <div class="col-md-12">
                            <div wire:ignore id="formMap2" class="w-full" style="height:150px;"></div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                    <input type="text" id="date_time" name="date_time"
                                        placeholder="Order date time"
                                        class="w-full h-12 p-2 px-4 border rounded-lg form-control datetimepicker-input border-gray1 focus:outline-none focus:border-mainColor"
                                        data-target="#reservationdate" data-toggle="datetimepicker" />


                                </div>

                            </div>

                            @error('date_time')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input placeholder="Preperation time..."
                                        class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                        id="preperation_time" name="preperation_time">
                                </p>
                                @error('preperation_time')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">

                                <p>
                                    <input placeholder="Driver will arive in..." disabled id="driver_arrive_time"
                                        class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                        name="arive_in">
                                </p>
                                @error('arive_in')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input placeholder="Drived in..." oninput="this.className = ''" name="driver_in"
                                        id="driver_in"
                                        class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor">
                                </p>
                                @error('driver_in')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <p>
                                    <input readonly placeholder="Service fees..."
                                        class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                        id="service_fees" name="service_fees">
                                </p>
                                @error('service_fees')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                    </div>

                    <div style="overflow:auto;" class="tap3" id="nextprevious">
                        <div style="float:right;">
                            <button type="button" id="prevBtn"
                                class="prev-btn p-3 !px-20 !text-base text-white rounded-md bg-gray-400"
                                onclick="nextPrev(-1)">
                                Previous
                            </button>
                            <button class="next-btn p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                                onclick="saveOrder()" type="button">
                                save
                            </button>
                        </div>

                    </div>

                </div>


            </form>



        </div>
    </div>
</div>
