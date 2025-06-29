@extends('admin.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
        background-color: #e9ecef;
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
    @include('admin.includes.content-header', ['header' => 'Edit Area', 'title' => 'Locations'])




    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">


                <div class="col-md-12">

                    <div class="card card-default">




                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="timeline">
                            <div class="card-header">
                                <h3 class="card-title">Edit Area</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="shift-form" action="{{ route('update-area', $area->id) }}" method="POST">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="area_name" name="area_name"
                                                    placeholder="Area Name" value="{{ isset($area) ? $area->name : '' }}">
                                                @error('area_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select class="form-control select2" id="city_id" name="city_id"
                                                    style="width: 100%;">
                                                    <option value="" selected="selected" disabled>City</option>
                                                    @foreach ($cities as $city)
                                                        <option
                                                            {{ isset($area) && $area->city_id == $city->id ? 'selected' : '' }}
                                                            value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach

                                                </select>
                                                @error('city_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block bg-gradient-primary btn-sm"
                                                id="save-shift-btn">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>







                    </div>

                    <!-- /.col -->

                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // AJAX request on Save button click
        $(document).ready(function() {

            $('.select2').select2({
                allowClear: true
            });



        });
    </script>
@endsection
