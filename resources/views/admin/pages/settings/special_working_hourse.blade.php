<div class="tab-pane table-responsive p-0" style="height: 450px;" id="special_business_hours">
    <div class="card-header">
        <h4>Special Business Hours </h4>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="special-hours-form" method="post">
            @csrf



            <div class="row">
               


                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group date reservationdate" id="reservationdate2" data-target-input="nearest">
                            <input type="text" id="special_start_time" name="special_start_time" placeholder="Start"
                                class="form-control datetimepicker-input" data-target="#reservationdate2"
                                data-toggle="datetimepicker" />

                        </div>

                    </div>

                    <div class="text-danger" id="special_start_time_error"></div>

                </div>




                <div class="col-md-6">
                    <div class="form-group">
                        <div class="input-group date reservationdate" id="reservationdate" data-target-input="nearest">
                            <input type="text" id="special_end_time" name="special_end_time" placeholder="End"
                                class="form-control datetimepicker-input" data-target="#reservationdate"
                                data-toggle="datetimepicker" />

                        </div>

                    </div>

                    <div class="text-danger" id="special_end_time_error"></div>

                </div>

               
            </div>


            <br>
            <p>Clients</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2 days" multiple="multiple" name="clients[]"
                            style="width: 100%;">
                            @foreach ($clients as $client)
                                <option @if (in_array($client->id, $special_clients)) selected @endif value="{{ $client->id }}">
                                    {{ $client->full_name }} </option>
                            @endforeach
                        </select>
                        @error('clients')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-md-3">
                    <button type="button" id="save-special-business-hours" class="btn btn-block bg-gradient-primary btn-sm">Save</button>
                </div>
            </div>
        </form>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Special Business Hours </h3>


                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="height: 400px;">
                        <table id="branches-table" class="table table-head-fixed text-nowrap">
                            <thead>
                               
                               
                                <td>Start date</td>
                                <td>Start time</td>
                                <td>End date</td>
                                <td>End time</td>
                                <td>Client</td>

                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>

<script>
      

    $(document).ready(function() {
        $('.days').select2({
            placeholder: "Clients",
            allowClear: true
        });

        
    });
</script>
