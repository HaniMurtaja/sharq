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
    @include('admin.includes.content-header', ['header' => 'Edit Group', 'title' => 'Groups'])




    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">


                <div class="col-md-12">

                    <div class="card card-default">




                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="timeline">
                            <div class="card-header">
                                <h3 class="card-title">Edit group</h3>
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
                                <form id="group-form" action="{{ route('update-clients-group', $group->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="group_name" id="group_name" class="form-control"
                                                    placeholder="Group Name" value="{{ old('group_name', $group->name) }}">
                                                @error('group_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <p>Delivery Fee</p>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" id="calculation_method"
                                                    name="calculation_method" style="width: 100%;">
                                                    <option value="" disabled
                                                        {{ old('calculation_method', $group->calculation_method) == '' ? 'selected' : '' }}>
                                                        Calculation method</option>
                                                    <option value="Area to Area"
                                                        {{ old('calculation_method', $group->calculation_method) == 'Area to Area' ? 'selected' : '' }}>
                                                        Area to Area</option>
                                                    <option value="Per Area"
                                                        {{ old('calculation_method', $group->calculation_method) == 'Per Area' ? 'selected' : '' }}>
                                                        Per Area</option>
                                                    <option value="City to City"
                                                        {{ old('calculation_method', $group->calculation_method) == 'City to City' ? 'selected' : '' }}>
                                                        City to City</option>
                                                    <option value="Per Stop"
                                                        {{ old('calculation_method', $group->calculation_method) == 'Per Stop' ? 'selected' : '' }}>
                                                        Per Stop</option>
                                                    <option value="Per km"
                                                        {{ old('calculation_method', $group->calculation_method) == 'Per km' ? 'selected' : '' }}>
                                                        Per km</option>
                                                    <option value="Formula"
                                                        {{ old('calculation_method', $group->calculation_method) == 'Formula' ? 'selected' : '' }}>
                                                        Formula</option>
                                                    <option value="Flat Rate"
                                                        {{ old('calculation_method', $group->calculation_method) == 'Flat Rate' ? 'selected' : '' }}>
                                                        Flat Rate</option>
                                                </select>
                                                @error('calculation_method')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" id="default_delivery_fee" name="default_delivery_fee"
                                                    class="form-control" placeholder="Default delivery fee"
                                                    value="{{ old('default_delivery_fee', $group->default_delivery_fee) }}">
                                                @error('default_delivery_fee')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" id="collection_amount" name="collection_amount"
                                                    class="form-control" placeholder="Collection amount"
                                                    value="{{ old('collection_amount', $group->collection_amount) }}">
                                                @error('collection_amount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" id="service_type" name="service_type"
                                                    style="width: 100%;">
                                                    <option value="" disabled
                                                        {{ old('service_type', $group->service_type) == '' ? 'selected' : '' }}>
                                                        Service type</option>
                                                    <option value="Delivery"
                                                        {{ old('service_type', $group->service_type) == 'Delivery' ? 'selected' : '' }}>
                                                        Delivery</option>
                                                </select>
                                                @error('service_type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" id="btn-save-group"
                                                class="btn btn-block bg-gradient-primary btn-sm">Save</button>
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
        $(document).ready(function() {

            $('.select2').select2({
                allowClear: true
            });



        });
    </script>
@endsection
