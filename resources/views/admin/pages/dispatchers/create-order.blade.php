<div class="overlay" id="overlay_order"></div>
<div id="loader_order" class="loader"></div>
<div id="drawer" data-drawer="Dispatcher"
    class="fixed top-0 right-0 z-50 min-h-full pr-4 pb-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-2/3">

    <div class="flex flex-col h-screen overflow-scroll onDemondContainer">
        <div class="flex items-center justify-between mb-6 flex-row-reverse">
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

        <div class="flex flex-col gap-2 px-3 onDemondFormContainer">

            <form id="regForm" >
                @csrf


                <input type="hidden" id="lat_order_hidden" name="lat_order_hidden" />
                <input type="hidden" id="long_order_hidden" name="lng_order_hidden" />
                <input type="hidden" id="lat_client_branch_hidden" name="lat_client_branch_hidden" />
                <input type="hidden" id="lng_client_branch_hidden" name="lng_client_branch_hidden" />
                <input hidden id="distance" name="distance">

                {{-- <div class="tab" id="step-1" style="display: block"> --}}
                <div class="row">
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


                        <div  id="formMap" style="width:100%; height:150px;"></div>
                        <span id="map_error"></span>
                    </div>
                </div>
                <br>

                @if (auth()->user()->user_role?->value != 5)
                    <div class="row">

                        @if (auth()->user()->user_role != \App\Enum\UserRole::CLIENT)
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
                        @endif
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control select2" style="width: 100%;" name="branch_id"
                                    id="branch_id">
                                    <option value="" selected="selected" disabled>Branch</option>
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('branch_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <label for="">{{ $auth_name }}</label>
                    </div>

                @endif





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
                                <input readonly placeholder="Service fees..." value="{{ $fees }}"
                                    class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                    id="service_fees" name="service_fees">
                            </p>
                            @error('service_fees')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>




                </div>


                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control select2" style="width: 100%;" id="payment_method"
                                name="payment_method">
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



                    <div class="col-md-6">
                        <div class="form-group">
                            <p>
                                <input placeholder="Order value..." hidden
                                    class="w-full h-12 p-2 px-4 border rounded-lg controls border-gray1 focus:outline-none focus:border-mainColor"
                                    name="order_value" id="order_value">
                            </p>
                            @error('order_value')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>



                </div>
                <div class="row">
                    <label>Customer Address</label>
                    <div class="col-md-6">
                        <div class="form-group">
                            <textarea class="form-control select2" style="width: 100%;" placeholder="Customer Address" id="customer_address"
                                name="customer_address">

                            </textarea>
                            @error('customer_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>



                <div style="overflow:auto;" class="tap3" id="nextprevious">
                    <div style="float:right;">

                        <button class="next-btn p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                            onclick="saveOrder()" type="button">
                            save
                        </button>
                    </div>

                </div>


                {{-- </div> --}}






            </form>



        </div>
    </div>
</div>
