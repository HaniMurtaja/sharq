<link rel="stylesheet" type="text/css" href="{{asset('maps/datepickerf/jquery.datetimepicker.css')}}">

<form id="formname">
<div class="row">
    <div class="col-md-3">
        <span>Data From</span>
        <div class="form-floating mb-3">
            <input type='text' class="form-control datetimepicker1" value="{{@request()->fromtime}}" autocomplete="off"   name="fromtime"  />
            <label for="tb-fnameddd">Data From</label>
        </div>
    </div>
    <div class="col-md-3">
        <span>Data To</span>
        <div class="form-floating mb-3">
            <input type='text' class="form-control datetimepicker1" value="{{@request()->totime}}" autocomplete="off"   name="totime"  />
            <label for="tb-fnameddd">Data To</label>
        </div>
    </div>



        <div class="col-md-2">
            <button type="submit" id="btnSubmit" class="flex gap-3 p-3 px-8 text-white rounded-md bg-green1" >
                <img src="{{ asset('new/src/assets/icons/filter.svg') }}" alt="" />
                <span>Apply Filter</span>
            </button>
        </div>
        <div class="col-md-2">
            <a class="btn btn-info" href="{{route('reports.reportCitys')}}">Back</a>
        </div>
        <h3>{{@$numberOfDays}}</h5>
</div>
</form>

<script>
    $(document).ready(function () {

        $("#formname").submit(function (e) {


            //stop submitting the form to see the disabled button effect
           // e.preventDefault();

            //disable the submit button
            $("#btnSubmit").attr("disabled", true);
            return true;
        });
    });
</script>
