<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


<style>
    .year {
        max-height: 100px;
        /* Adjust the height as needed */
        overflow-y: auto;
    }
    .form-control,
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }
</style>
<div class="p-0 active tab-pane table-responsive" style="height: 450px;" id="activity">

    <div class="card-header">
        <h3 class="card-title" id="title-vehicle"> New Vehicle</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" id="vehicle-form">
            @csrf
            <p>Vehicle image</p>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="file" id="file-upload" class="file-upload" accept="image/*" name="vehicle_image">
                        <label for="file-upload" class="upload-label" id="upload-label">
                            <svg viewBox="0 0 24 24" id="user-icon">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </label>
                    </div>
                </div>
            </div>
            <p>Vehicle information</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" placeholder="Name" name="name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="type">
                            <option value="" selected="selected" disabled>Type</option>
                            @foreach ($settings->vehicle_types as $type)
                            <option value="{{$type}}">{{$type}}</option>
                            @endforeach                            
                        </select>
                    </div>
                </div>
            </div>











            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="plate_number" placeholder="Plate Number" name="plate_number">
                        <input name="vehicle_id" id="vehicle_id" hidden >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="vin_number" placeholder="VIN Number" name="vin_number">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="make" placeholder="Make" name="make">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="model" placeholder="Model" name="model">
                    </div>
                </div>
            </div>



            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2 year" style="width: 100%;" name="year" id="yearSelect">
                            <option value="" selected="selected" disabled>Year</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group custom-select2-search">
                        <select class="form-control select2" style="width: 100%;" id="color" name="color">
                            <option value="" selected="selected" disabled>Color</option>
                            <option value="Black">Black</option>
                            <option value="White">White</option>
                            <option value="Silver">Silver</option>
                            <option value="Gray">Gray</option>
                            <option value="Red">Red</option>
                            <option value="Blue">Blue</option>
                            <option value="Brown">Brown</option>
                            <option value="Green">Green</option>
                            <option value="Gold">Gold</option>
                            <option value="Yellow">Yellow</option>
                            <option value="Orange">Orange</option>
                            <option value="Purple">Purple</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">


                <div class="col-md-6">


                    <div class="form-group">
                        <p>ID card photo</p>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    Browse… <input type="file" name="id_card_image" id="imgInpProfile">
                                </span>
                            </span>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <div id = "id_card_privew_section">
                            <img id='profile_image' />
                        </div>
                        
                    </div>

                </div>

            </div>

            <p>Milage info</p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="vehicle_milage" placeholder="Vehicle Milage" name="vehicle_milage">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="last_service_milage" placeholder="Last Service Milage" name="last_service_milage">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="due_service_milage" placeholder="Due Service Milage" name="due_service_milage">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="service_milage_limit" placeholder="Service Milage Limit" name="service_milage_limit">
                    </div>
                </div>
            </div>
            
           

            <div class="row">

                <div class="col-md-3">
                    <button type="button" class="btn btn-block bg-gradient-primary btn-sm" id="save-vehicle-btn">Save</button>

                </div>


            </div>
        </form>


    </div>



</div>

