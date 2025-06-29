@extends('admin.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style>
    .file-upload {
        display: none;
    }

    .upload-label {
        display: inline-block;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        background-color: #f0f0f0;
        border: 2px solid #ccc;
        background-size: cover;
        background-position: center;
    }

    .upload-label svg {
        width: 50%;
        height: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        fill: #aaa;
    }

    .file-upload:focus+.upload-label,
    .file-upload:active+.upload-label {
        outline: 2px solid #007bff;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;

    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }
</style>
@section('content')
    @include('admin.includes.content-header', ['header' => 'Edit Vehicle', 'title' => 'Vehicles'])




    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card card-default">
                        <div class="tab-content">



                            <div class="active tab-pane table-responsive p-0" style="height: 450px;" id="activity">

                                <div class="card-header">
                                    <h3 class="card-title"> Edit Vehicles</h3>

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
                                    <form method="post" enctype="multipart/form-data" id="vehicle-form"
                                        action="{{ route('edit-vehicle', $vehicle) }}">
                                        @csrf
                                        <p>Vehicle image</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if ($vehicle->getFirstMediaUrl('vehicle_image'))
                                                    <img id="profileImagePreview"
                                                        src="{{ $vehicle->getFirstMediaUrl('vehicle_image') }}"
                                                        alt="Current Profile Image"
                                                        style="max-width: 300px; max-height:300px; display: block" />
                                                @else
                                                    <img id="profileImagePreview" src="#" alt="Current Profile Image"
                                                        style="max-width: 300px; max-height: 300px; display: none;" />
                                                @endif
                                                <br>
                                                <input name="vehicle_image" class="file-upload form-control form-control-sm"
                                                    id="id_card_image" accept="image/*" type="file"
                                                    onchange="previewImage(event)" />
                                                @error('vehicle_image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <p>Vehicle information</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="name"
                                                        placeholder="Name" name="name" value="{{ $vehicle->name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control select2" style="width: 100%;"
                                                        name="type">
                                                        <option value="" disabled>Type</option>
                                                        @foreach ($settings->vehicle_types as $type)
                                                            <option @if ($vehicle->type == $type) selected @endif
                                                                value="{{ $type }}">{{ $type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="plate_number"
                                                        placeholder="Plate Number" name="plate_number"
                                                        value="{{ $vehicle->plate_number }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="vin_number"
                                                        placeholder="VIN Number" name="vin_number"
                                                        value="{{ $vehicle->vin_number }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="make"
                                                        placeholder="Make" name="make" value="{{ $vehicle->make }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="model"
                                                        placeholder="Model" name="model" value="{{ $vehicle->model }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control select2 year" style="width: 100%;"
                                                        name="year" id="yearSelect">
                                                        <option value="" disabled>Year</option>
                                                        @for ($year = 1980; $year <= \Carbon\Carbon::now()->year; $year++)
                                                            <option value="{{ $year }}"
                                                                @if ($vehicle->year == $year) selected @endif>
                                                                {{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control select2" style="width: 100%;"
                                                        id="color" name="color">
                                                        <option value="" disabled>Color</option>
                                                        <option value="Black"
                                                            @if ($vehicle->color == 'Black') selected @endif>Black</option>
                                                        <option value="White"
                                                            @if ($vehicle->color == 'White') selected @endif>White</option>
                                                        <option value="Silver"
                                                            @if ($vehicle->color == 'Silver') selected @endif>Silver
                                                        </option>
                                                        <option value="Gray"
                                                            @if ($vehicle->color == 'Gray') selected @endif>Gray</option>
                                                        <option value="Red"
                                                            @if ($vehicle->color == 'Red') selected @endif>Red</option>
                                                        <option value="Blue"
                                                            @if ($vehicle->color == 'Blue') selected @endif>Blue</option>
                                                        <option value="Brown"
                                                            @if ($vehicle->color == 'Brown') selected @endif>Brown</option>
                                                        <option value="Green"
                                                            @if ($vehicle->color == 'Green') selected @endif>Green
                                                        </option>
                                                        <option value="Gold"
                                                            @if ($vehicle->color == 'Gold') selected @endif>Gold
                                                        </option>
                                                        <option value="Yellow"
                                                            @if ($vehicle->color == 'Yellow') selected @endif>Yellow
                                                        </option>
                                                        <option value="Orange"
                                                            @if ($vehicle->color == 'Orange') selected @endif>Orange
                                                        </option>
                                                        <option value="Purple"
                                                            @if ($vehicle->color == 'Purple') selected @endif>Purple
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p for="id_card_image" class="form-label">ID card photo</p>
                                                @if ($vehicle->getFirstMediaUrl('id_card_image'))
                                                    <img id="idCardImagePreview"
                                                        src="{{ $vehicle->getFirstMediaUrl('id_card_image') }}"
                                                        alt="Current ID Card Image"
                                                        style="max-width: 300px; max-height: 300px; display: block" />
                                                @else
                                                    <img id="idCardImagePreview" src="#"
                                                        alt="Current ID Card Image"
                                                        style="max-width: 300px; max-height: 300px; display: none;" />
                                                @endif
                                                <br>
                                                <input name="id_card_image"
                                                    class="file-upload form-control form-control-sm" id="id_card_image"
                                                    accept="image/*" type="file"
                                                    onchange="previewIdCardImage(event)" />
                                                @error('id_card_image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <br>
                                        <p>Milage info</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="vehicle_milage"
                                                        placeholder="Vehicle Milage" name="vehicle_milage"
                                                        value="{{ $vehicle->vehicle_milage }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="last_service_milage"
                                                        placeholder="Last Service Milage" name="last_service_milage"
                                                        value="{{ $vehicle->last_service_milage }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="due_service_milage"
                                                        placeholder="Due Service Milage" name="due_service_milage"
                                                        value="{{ $vehicle->due_service_milage }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="service_milage_limit"
                                                        placeholder="Service Milage Limit" name="service_milage_limit"
                                                        value="{{ $vehicle->service_milage_limit }}">
                                                </div>
                                            </div>
                                        </div>
                                        <p>Operators</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control select2" style="width: 100%;"
                                                        name="operator_id">
                                                        <option value="" disabled>Drivers</option>
                                                        @foreach ($operators as $operator)
                                                            <option value="{{ $operator->id }}"
                                                                @if ($vehicle->operator_id == $operator->id) selected @endif>
                                                                {{ $operator->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-block bg-gradient-primary btn-sm"
                                                    id="save-vehicle-btn">Save</button>
                                            </div>
                                        </div>
                                    </form>



                                </div>



                            </div>




                        </div>



                    </div>

                    <!-- /.col -->

                    <!-- /.col -->
                </div>


            </div><!-- /.container-fluid -->
    </section>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            $('.select2').select2({
                allowClear: true
            });

           


        });

        function previewIdCardImage(event) {
            const input = event.target;
            const preview = document.getElementById('idCardImagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('profileImagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }
    </script>
@endsection
