<link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">

<form>
    <div class="row">
        <div class="col-md-3">
            <span>Data From</span>
            <div class="form-floating mb-3">
                <input type='text' class="form-control datetimepicker1" id="from_date" value="{{ @request()->fromtime }}"
                    autocomplete="off" name="fromtime" />
                <label for="tb-fnameddd">Data From</label>
            </div>
        </div>
        <div class="col-md-3">
            <span>Data To</span>
            <div class="form-floating mb-3">
                <input type='text' class="form-control datetimepicker1" id="to_date"
                    value="{{ @request()->totime }}" autocomplete="off" name="totime" />
                <label for="tb-fnameddd">Data To</label>
            </div>
        </div>
        <div class="col-md-3">

            <label class="flex flex-col gap-3">
                <span>Dispatcher</span>
                <select
                    class="form-control select2all shadow-none custom-select2-search w-full border rounded-md  focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" id="assigned_by" name="assigned_by">
                    <option value="">Dispatcher</option>

                    @foreach ($getDispatcher as $getDispat)
                        <option value="{{ $getDispat->id }}"@if (request()->assigned_by == $getDispat->id) selected @endif>
                            {{ $getDispat->full_name }}</option>
                    @endforeach

                </select>
            </label>

        </div>
        <div class="col-md-3">

            <label class="flex flex-col gap-3">
                <span>Client</span>
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

            <label class="flex flex-col gap-3">
                <span>City</span>
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


        <div class="col-md-2">
            <button type="submit" class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1">
                <img src="{{ asset('new/src/assets/icons/filter.svg') }}" alt="" />
                <span>Apply Filter</span>
            </button>
        </div>

        <div class="col-md-2">
            <a class="btn btn-info" href="{{ route('report.dispatcherAssignReport') }}">Back</a>
        </div>
        <h3>{{ @$numberOfDays }}</h5>
    </div>
</form>
