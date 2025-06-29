<link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">

<form>
    <input type="hidden" name="assigned_by" value="{{ request()->assigned_by }}" placeholder="assigned_by">





    <div class="row gy-3">

        <div class="col-md-3">

            <label class="flex flex-col gap-2">
                <span class="fs-112 gray-94 fw-semibold">Date</span>
                <select
                    class="form-control select2all shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" name="datesearch">
                    <option value="" selected="selected" disabled>ingr_branch_id</option>
                    <option value="">Please choose</option>
                    <option value="created_at" @if (request()->datesearch == 'created_at') selected @endif>Created At</option>
                    <option value="driver_assigned_at" @if (request()->datesearch == 'driver_assigned_at') selected @endif>Assigned At
                    </option>


                </select>


            </label>

        </div>



        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Data From</span>
            <div class="form-floating">
                <input type='text'
                    class="form-control fs-112 fw-semibold black-1a br-96 pxy-96 h-auto datetimepicker1"
                    value="{{ @request()->fromtime }}" required="" autocomplete="off" name="fromtime" />
                <label for="tb-fnameddd" class="d-none">Data From</label>
            </div>
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Data To</span>
            <div class="form-floating">
                <input type='text'
                    class="form-control fs-112 fw-semibold black-1a br-96 pxy-96 h-auto datetimepicker1"
                    value="{{ @request()->totime }}" required="" autocomplete="off" name="totime" />
                <label for="tb-fnameddd" class="d-none">Data To</label>
            </div>
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">ID</span>
            <input type="text" name="id" value="{{ request()->id }}" id="search"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Search Order ID </span>
            <input type="text" name="client_order_id" value="{{ request()->client_order_id }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Search Order String ID </span>
            <input type="text" name="client_order_id_string" value="{{ request()->client_order_id_string }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Customer Name</span>
            <input type="text" name="customer_name" value="{{ request()->customer_name }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Customer Phone</span>
            <input type="text" name="customer_phone" value="{{ request()->customer_phone }}"
                class="fs-112 fw-semibold black-1a br-96 p-2 border h-348" />
        </div>

        <div class="col-md-3">
            @php
                $status_ids = [];
                if (request()->status_ids != '') {
                    $status_ids = request()->status_ids;
                }
            @endphp

            <label class="flex flex-col gap-2 m-0">
                <span class="fs-112 gray-94 fw-semibold">Status</span>
                <select
                    class="form-control shadow-none custom-select2-search w-full border rounded-md status focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" multiple="multiple" id="status" name="status_ids[]">

                    {{-- <option value="-1">All</option> --}}
                    @foreach (App\Enum\OrderStatus::cases() as $status)
                        @if (in_array($status->value, $status_ids))
                            <option value="{{ $status->value }}" selected="true">{{ $status->getLabel() }}</option>
                        @else
                            <option value="{{ $status->value }}">{{ $status->getLabel() }}</option>
                        @endif
                    @endforeach
                </select>
            </label>
        </div>
        <div class="col-md-3">

            <label class="flex flex-col gap-2">
                <span class="fs-112 gray-94 fw-semibold">Branchs</span>
                <select
                    class="form-control select2all shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" id="ingr_branch_id" name="ingr_branch_id">
                    <option value="" selected="selected" disabled>ingr_branch_id</option>
                    <option value="">Please choose</option>
                    @foreach ($getClientBranches as $ids => $ClientBranches)
                        <option value="{{ $ids }}"@if (request()->ingr_branch_id == $ids) selected @endif>
                            {{ $ClientBranches }}</option>
                    @endforeach

                </select>
            </label>

        </div>

        <div class="col-md-3">

            <label class="flex flex-col gap-2">
                <span class="fs-112 gray-94 fw-semibold">Client</span>
                <select
                    class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" id="client_id" name="client_id">
                    <option value="" selected="selected" disabled>CLient</option>
                    @if (auth()->user()->user_role?->value == 2)
                        <option>{{ auth()->user()->full_name }}</option>
                    @else
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"@if (request()->client_id == $client->id) selected @endif>
                                {{ $client->full_name }}</option>
                        @endforeach
                    @endif
                </select>
            </label>

        </div>

        <div class="col-md-3">

            <label class="flex flex-col gap-2">
                <span class="fs-112 gray-94 fw-semibold">Driver</span>
                <select
                    class="form-control shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" id ="driver_id" name="driver_id">
                    <option value="" selected="selected" disabled>Drivre</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" @if (request()->driver_id == $driver->id) selected @endif>
                            {{ $driver->full_name }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="col-md-3">

            <label class="flex flex-col gap-2">
                <span class="fs-112 gray-94 fw-semibold">City</span>
                <select
                    class="form-control select2all shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" id ="city_id" name="city_id">
                    <option value=""></option>
                    @foreach ($citys as $ids => $city)
                        <option value="{{ $ids }}" @if (request()->city_id == $ids) selected @endif>
                            {{ $city }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="col-md-3"></div>

        <div class="col-md-12 text-end">
            <a class="p-9228 black-1a bg-light br-96  fs-112 fw-semibold border"
                href="{{ route('export.GetOrders') }}">Back</a>
            {{-- <button type="submit" class="pxy-828 text-white br-96 bg-red-a3 fs-112 fw-semibold">
                <span>Apply Filter</span>
            </button> --}}

               <button type="submit" name="export"  value="xlsx"  class="btn btn-success pxy-828 text-white br-96 bg-red-a3 fs-112 fw-semibold">
                <span>Excel</span>
            </button>

        </div>

    </div>
</form>
