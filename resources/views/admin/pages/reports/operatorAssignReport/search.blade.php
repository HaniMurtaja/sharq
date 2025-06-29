<link rel="stylesheet" type="text/css" href="{{ asset('maps/datepickerf/jquery.datetimepicker.css') }}">

<form id="operatoe-data-filter-form">
    <div class="row">
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Data From</span>
            <div class="form-floating">
                <input type='text' class="form-control fs-112 fw-semibold black-1a br-96 pxy-96 h-auto datetimepicker1"
                    value="{{ @request()->fromtime }}" autocomplete="off" name="fromtime" id="fromtime" />
                <label for="tb-fnameddd" class="d-none">Data From</label>
            </div>
        </div>
        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <span class="fs-112 gray-94 fw-semibold">Data To</span>
            <div class="form-floating">
                <input type='text'
                    class="form-control fs-112 fw-semibold black-1a br-96 pxy-96 h-auto datetimepicker1"
                    value="{{ @request()->totime }}" autocomplete="off" id="totime" name="totime" />
                <label for="tb-fnameddd" class="d-none">Data To</label>
            </div>
        </div>

        <div class="col-md-3 d-flex flex-column gap-2 h-fit-content">
            <label class="flex flex-col gap-2 m-0">
                <span class="fs-112 gray-94 fw-semibold">Acceptance Rate</span>
                <select
                    class="form-control shadow-none custom-select2-search w-full border rounded-md acceptance_rate focus:outline-none focus:border-mainColor border-gray5 h-[2.9rem] px-3"
                    style="width: 100%;" name="acceptance_rate" id="acceptance_rate">

                    <option></option>
                    <option value="0">Acceptance rate is greater than 2 minutes</option>
                    <option value="1">Acceptance rate less than or equal to 2 minutes</option>
                </select>
            </label>
        </div>




        <div class="col-md-12 text-end mt-4">
            <a class="p-9228 black-1a bg-light br-96  fs-112 fw-semibold border" href="#" id="back-btn">Back</a>
            <button type="button" id="operatoe-data-filter-btn"
                class="pxy-828 text-white br-96 bg-red-a3 fs-112 fw-semibold">
                <span>Apply Filter</span>
            </button>

        </div>


    </div>
</form>
