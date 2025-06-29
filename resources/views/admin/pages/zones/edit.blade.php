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

    @include('admin.includes.content-header', ['header' => 'Edit Zone', 'title' => 'Clients'])

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="settings">
                            <div class="card-header">
                                <h3 class="card-title"> Edit Zone</h3>
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
                                <form id="zone-form" method="POST" action="{{ route('update-zone', $zone->id) }}">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="text"
                                                    class="form-control @error('zone_name') is-invalid @enderror"
                                                    id="zone_name" name="zone_name" placeholder="Zone Name"
                                                    value="{{ old('zone_name', $zone->name) }}">
                                                @error('zone_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div id="repeater">
                                        @if ($zone->details->isNotEmpty())
                                            @foreach ($zone->details as $detail)
                                                <div class="row mb-2">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <select class="form-control select2 city-select"
                                                                style="width: 100%;" name="city[]">
                                                                <option value="" selected="selected" disabled>City
                                                                </option>
                                                                @foreach ($all_cities as $city)
                                                                    <option value="{{ $city->id }}"
                                                                        @if ($city->id == $detail->city_id) selected @endif>
                                                                        {{ $city->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="text-danger city_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <select class="form-control select2 area-select"
                                                                style="width: 100%;" name="area[]"
                                                                @if (!$detail->city_id) disabled @endif>
                                                                <option value="" selected="selected" disabled>Area
                                                                </option>
                                                                @foreach ($all_areas as $area)
                                                                    <option value="{{ $area->id }}"
                                                                        @if ($area->id == $detail->area_id) selected @endif>
                                                                        {{ $area->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="text-danger area_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-add mx-1"
                                                                style="color: green;">
                                                                <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-delete"
                                                                style="color: red;">
                                                                <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="row mb-2">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <select class="form-control select2 city-select"
                                                            style="width: 100%;" name="city[]">
                                                            <option value="" selected="selected" disabled>City
                                                            </option>
                                                            @foreach ($all_cities as $city)
                                                                <option value="{{ $city->id }}">{{ $city->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-danger city_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <select class="form-control select2 area-select"
                                                            style="width: 100%;" name="area[]" disabled>
                                                            <option value="" selected="selected" disabled>Area
                                                            </option>

                                                        </select>
                                                        <span class="text-danger area_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-add mx-1"
                                                            style="color: green;">
                                                            <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-delete" style="color: red;">
                                                            <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" id="save-zone-btn"
                                                class="btn btn-block bg-gradient-primary btn-sm">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                allowClear: true
            });


            $(document).on('change', '.city-select', function() {
                var cityId = $(this).val();
                var areaSelect = $(this).closest('.row').find('.area-select');

                if ($(this).val()) {
                    $.ajax({
                        url: '{{ route('city-areas') }}',
                        type: 'GET',
                        data: {
                            city_id: cityId
                        },
                        success: function(response) {
                            console.log(response); // Log the response to check the data
                            areaSelect.prop('disabled', false);
                            areaSelect.empty();
                            areaSelect.append(
                                '<option value="" selected="selected" disabled>Area</option>'
                            );

                            $.each(response, function(key, area) {

                                areaSelect.append('<option value="' + area.id + '">' +
                                    area.name + '</option>');
                            });
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr.responseText);
                        }
                    });
                } else {
                    areaSelect.prop('disabled', true);
                    areaSelect.empty();
                    areaSelect.append('<option value="" selected="selected" disabled>Area</option>');
                }
            });

            function enableAreaSelect() {
                $('.city-select').each(function() {
                    var areaSelect = $(this).closest('.row').find('.area-select');
                    if ($(this).val()) {
                        areaSelect.prop('disabled', false);
                    } else {
                        areaSelect.prop('disabled', true);
                    }
                });
            }

            $(document).on('change', '.city-select', function() {
                var areaSelect = $(this).closest('.row').find('.area-select');
                if ($(this).val()) {
                    areaSelect.prop('disabled', false);
                } else {
                    areaSelect.prop('disabled', true);
                }
            });


            // Function to update delete button status
            function updateDeleteButtons(repeaterId) {
                var repeater = document.getElementById(repeaterId);
                var rows = repeater.querySelectorAll('.row');
                rows.forEach(function(row, index) {
                    var deleteButton = row.querySelector('.btn-delete');
                    if (rows.length === 1) {
                        deleteButton.disabled = true;
                    } else {
                        deleteButton.disabled = false;
                    }
                });
            }

            // Function to add a new row
            function addRow(repeaterId) {
                var repeater = document.getElementById(repeaterId);
                var newRow = document.createElement('div');
                newRow.classList.add('row', 'mb-2');

                newRow.innerHTML = `
        
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control select2 city-select" style="width: 100%;" name="city[]">
                                <option value="" selected="selected" disabled>City</option>
                                @foreach ($all_cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger city_error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control select2 area-select" style="width: 100%;" name="area[]" disabled>
                                <option value="" selected="selected" disabled>Area</option>
                                @foreach ($all_areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger area_error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button type="button" class="btn btn-add mx-1" style="color: green;">
                                <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                            </button>
                            <button type="button" class="btn btn-delete" style="color: red;">
                                <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                            </button>
                        </div>
                    </div>
             
            `;
                $('.select2').select2();

                // Append the new row and update delete button status
                repeater.appendChild(newRow);
                updateDeleteButtons(repeaterId);



                enableAreaSelect();
            }

            // Function to delete a row
            function deleteRow(button) {
                var row = button.closest('.row');
                row.remove();
                updateDeleteButtons(row.parentNode.id);
                $('.select2').select2();

                enableAreaSelect();
            }

            // Event listener for adding rows
            $('#repeater').on('click', '.btn-add', function() {
                addRow('repeater');
            });

            $('#repeater2').on('click', '.btn-add', function() {
                addRow('repeater2');
            });

            // Event listener for deleting rows
            $('#repeater').on('click', '.btn-delete', function() {
                deleteRow(this);
            });

            $('#repeater2').on('click', '.btn-delete', function() {
                deleteRow(this);
            });

            // Initial update of delete buttons
            updateDeleteButtons('repeater');
            updateDeleteButtons('repeater2');
            enableAreaSelect()

        });
    </script>

@endsection
