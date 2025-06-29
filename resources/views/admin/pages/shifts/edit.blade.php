@extends('admin.layouts.app')
<style>
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
    @include('admin.includes.content-header', ['header' => 'Edit Shift', 'title' => 'Shifts'])




    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">


                <div class="col-md-12">

                    <div class="card card-default">




                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="timeline">
                            <div class="card-header">
                                <h3 class="card-title">Edit Shifts</h3>
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
                                <form id="shift-form" action="{{ route('edit-shift', $shift->id) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <p>Shift Name</p>
                                                <input type="text" class="form-control" id="shift_name" name="shift_name"
                                                    placeholder="Shift Name"
                                                    value="{{ isset($shift) ? $shift->name : '' }}">
                                                <span class="text-danger" id="shift_name_error"></span>
                                                @error('shift_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" id="shift_from" name="shift_from"
                                                    style="width: 100%;">
                                                    <option value="" selected disabled>From</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ isset($shift) && $shift->from == $i ? 'selected' : '' }}>
                                                            {{ $i }}</option>
                                                    @endfor
                                                </select>
                                                @error('shift_from')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror <select class="form-control select2" id="shift_from_type"
                                                    name="shift_from_type" style="width: 100%;">
                                                    <option value="PM"
                                                        {{ isset($shift) && $shift->shift_from_type == 'PM' ? 'selected' : '' }}>
                                                        PM</option>
                                                    <option value="AM"
                                                        {{ isset($shift) && $shift->shift_from_type == 'AM' ? 'selected' : '' }}>
                                                        AM</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" id="shift_to" name="shift_to"
                                                    style="width: 100%;">
                                                    <option value="" selected disabled>To</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ isset($shift) && $shift->to == $i ? 'selected' : '' }}>
                                                            {{ $i }}</option>
                                                    @endfor
                                                </select>
                                                @error('shift_to')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <span class="text-danger" id="shift_to_error"></span>
                                                <select class="form-control select2" id="shift_to_type" name="shift_to_type"
                                                    style="width: 100%;">
                                                    <option value="PM"
                                                        {{ isset($shift) && $shift->shift_to_type == 'PM' ? 'selected' : '' }}>
                                                        PM</option>
                                                    <option value="AM"
                                                        {{ isset($shift) && $shift->shift_to_type == 'AM' ? 'selected' : '' }}>
                                                        AM</option>
                                                </select>
                                                <span class="text-danger" id="shift_to_type_error"></span>
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        
            $(document).ready(function() {




                // Initialize Select2 for city and days_off fields
                $('.select2').select2({
                    placeholder: "Select a city",
                    allowClear: true
                });
                $('.days').select2({
                    placeholder: "Days off",
                    allowClear: true
                });

            });
    </script>
@endsection
