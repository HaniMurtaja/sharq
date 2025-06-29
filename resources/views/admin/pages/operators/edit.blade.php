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
    @include('admin.includes.content-header', ['header' => 'Edit Operator', 'title' => 'Operator'])

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="settings">
                            <div class="card-header">
                                <h3 class="card-title"> Edit Operator</h3>
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
                                <form enctype="multipart/form-data" id="operator-form" method="POST"
                                    action="{{ route('edit-operator', $operator) }}">

                                    @csrf

                                    <p>Information</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p for="id_card_image" class="form-label">Personal Photo</p>
                                            @if ($operator->getFirstMediaUrl('profile'))
                                                <img id="profileImagePreview"
                                                    src="{{ $operator->getFirstMediaUrl('profile') }}"
                                                    alt="Current Profile Image"
                                                    style="max-width: 300px; max-height:300px; display: block" />
                                            @else
                                                <img id="profileImagePreview" src="#" alt="Current Profile Image"
                                                    style="max-width: 300px; max-height: 300px; display: none;" />
                                            @endif
                                            <br>
                                            <input name="profile_photo" class="file-upload form-control form-control-sm"
                                                id="id_card_image" accept="image/*" type="file"
                                                onchange="previewImage(event)" />
                                            @error('profile_photo')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>


                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>First Name</p>
                                                <input type="text" class="form-control" id="firstName" name="first_name"
                                                    placeholder="First Name"
                                                    value="{{ old('first_name', $operator->first_name) }}">
                                                @error('first_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Last Name</p>
                                                <input type="text" class="form-control" id="lastName" name="last_name"
                                                    placeholder="Last Name"
                                                    value="{{ old('last_name', $operator->last_name) }}">
                                                @error('last_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Phone</p>
                                                <input type="text" class="form-control" id="phone" name="phone"
                                                    placeholder="Phone" value="{{ old('phone', $operator->phone) }}">
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Birth Date</p>
                                                <div class="input-group date" id="reservationdate"
                                                    data-target-input="nearest">

                                                    <input type="text" name="birth_date" placeholder="Birth Date"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#reservationdate" data-toggle="datetimepicker"
                                                        type="text" name="birth_date" placeholder="Birth Date"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#reservationdate" data-toggle="datetimepicker"
                                                        value="{{ old('birth_date', $operator->operator->birth_date ? \Carbon\Carbon::parse($operator->operator->birth_date)->format('m/d/Y') : '') }}" />
                                                </div>
                                                @error('birth_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <p for="id_card_image" class="form-label">ID card photo</p>
                                            @if ($operator->getFirstMediaUrl('card_images'))
                                                <img id="idCardImagePreview"
                                                    src="{{ $operator->getFirstMediaUrl('card_images') }}"
                                                    alt="Current ID Card Image"
                                                    style="max-width: 300px; max-height: 300px; display: block" />
                                            @else
                                                <img id="idCardImagePreview" src="#" alt="Current ID Card Image"
                                                    style="max-width: 300px; max-height: 300px; display: none;" />
                                            @endif
                                            <br>
                                            <input name="id_card_image" class="file-upload form-control form-control-sm"
                                                id="id_card_image" accept="image/*" type="file"
                                                onchange="previewIdCardImage(event)" />
                                            @error('id_card_image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <br>
                                    <p>More detail</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Emergency contact name</p>
                                                <input type="text" class="form-control" id="emergency_contact_name"
                                                    name="emergency_contact_name" placeholder="Emergency contact name"
                                                    value="{{ old('emergency_contact_name', $operator->operator->emergency_contact_name) }}">
                                                @error('emergency_contact_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Emergency contact phone</p>
                                                <input type="text" class="form-control" id="emergency_contact_phone"
                                                    name="emergency_contact_phone" placeholder="Emergency contact phone"
                                                    value="{{ old('emergency_contact_phone', $operator->operator->emergency_contact_phone) }}">
                                                @error('emergency_contact_phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Social ID number</p>
                                                <input type="text" class="form-control" id="social_id_no"
                                                    name="social_id_no" placeholder="Social ID number"
                                                    value="{{ old('social_id_no', $operator->operator->social_id_no) }}">
                                                @error('social_id_no')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>City</p>
                                                <select class="form-control select2 city-select" name="city">
                                                    <option value="" selected="selected" disabled>City</option>
                                                    @foreach ($cities as $city)
                                                        <option value="{{ $city->id }}"
                                                            {{ old('city', $operator->operator->city_id) == $city->id ? 'selected' : '' }}>
                                                            {{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('city')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p for="license_front_image" class="form-label">License Front</p>
                                            @if ($operator->getFirstMediaUrl('license_front_image'))
                                                <img id="licenseFrontImagePreview"
                                                    src="{{ $operator->getFirstMediaUrl('license_front_image') }}"
                                                    alt="Current License Front Image"
                                                    style="max-width: 300px; max-height: 300px; display: block" />
                                            @else
                                                <img id="licenseFrontImagePreview" src="#"
                                                    alt="Current License Front Image"
                                                    style="max-width: 300px; max-height: 300px; display: none;" />
                                            @endif
                                            <br>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p for="license_back_image" class="form-label">License Back</p>
                                                @if ($operator->getFirstMediaUrl('license_back_image'))
                                                    <img id="licenseBackImagePreview"
                                                        src="{{ $operator->getFirstMediaUrl('license_back_image') }}"
                                                        alt="Current License Back Image"
                                                        style="max-width: 300px; max-height: 300px;  display: block" />
                                                @else
                                                    <img id="licenseBackImagePreview" src="#"
                                                        alt="Current License Back Image"
                                                        style="max-width: 300px; max-height: 300px; display: none;" />
                                                @endif
                                                <br>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">

                                            <input class="form-control file-upload form-control-sm"
                                                id="license_front_image" name="license_front_image" accept="image/*"
                                                type="file" onchange="previewLicenseFrontImage(event)" />
                                            @error('license_front_image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <input class="form-control form-control-sm file-upload"
                                                    id="license_back_image" accept="image/*" type="file"
                                                    name="license_back_image" onchange="previewLicenseBackImage(event)" />
                                                @error('license_back_image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>IBan</p>
                                                <input type="text" class="form-control" id="iban" name="iban"
                                                    placeholder="IBan"
                                                    value="{{ old('iban', $operator->operator->iban) }}">
                                                @error('iban')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Password</p>
                                                <input type="password" class="form-control" id="password"
                                                    name="password" placeholder="Password">
                                                @error('password')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Order Value</p>
                                                <input type="text" class="form-control" id="orderValue" name="order_value"
                                                       placeholder="Order Value"
                                                       value="{{ old('order_value', $operator->operator->order_value) }}">
                                                @error('order_value')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div id="map" style="height: 500px;"></div>
                                    <input type="hidden" name="lat" id="latitude" value="{{ $operator->operator->lat}}">
                                    <input type="hidden" name="lng" id="longitude" value="{{ $operator->operator->lng}}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2 group city-select" name="group_id">
                                                    <option value="" selected="selected" disabled>Group</option>
                                                    @foreach ($all_groups as $group)
                                                        <option value="{{ $group->id }}"
                                                            {{ old('group', $operator->operator->group_id) == $group->id ? 'selected' : '' }}>
                                                            {{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('group')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2 branch-group city-select"
                                                    name="branch_group_id">
                                                    <option value="" selected="selected" disabled>Branch Group
                                                    </option>
                                                    @foreach ($all_groups as $branchGroup)
                                                        <option value="{{ $branchGroup->id }}"
                                                            {{ old('branch_group', $operator->operator->branch_group_id) == $branchGroup->id ? 'selected' : '' }}>
                                                            {{ $branchGroup->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('branch_group')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2 shift city-select" name="shift_id">
                                                    <option value="" selected="selected" disabled>Shift</option>
                                                    @foreach ($all_shifts as $shift)
                                                        <option value="{{ $shift->id }}"
                                                            {{ old('shift', $operator->operator->shift_id) == $shift->id ? 'selected' : '' }}>
                                                            {{ $shift->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('shift')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2 days" multiple="multiple"
                                                    name="days_off[]" style="width: 100%;">
                                                    @php
                                                        $daysOff = [];
                                                        if ($operator->operator->days_off) {
                                                            $daysOff = json_decode($operator->operator->days_off);
                                                        }
                                                    @endphp

                                                    <option value="Sunday"
                                                        {{ in_array('Sunday', $daysOff ?? []) ? 'selected' : '' }}>Sunday
                                                    </option>
                                                    <option value="Monday"
                                                        {{ in_array('Monday', $daysOff ?? []) ? 'selected' : '' }}>Monday
                                                    </option>
                                                    <option value="Tuesday"
                                                        {{ in_array('Tuesday', $daysOff ?? []) ? 'selected' : '' }}>Tuesday
                                                    </option>
                                                    <option value="Wednesday"
                                                        {{ in_array('Wednesday', $daysOff ?? []) ? 'selected' : '' }}>
                                                        Wednesday</option>
                                                    <option value="Thursday"
                                                        {{ in_array('Thursday', $daysOff ?? []) ? 'selected' : '' }}>
                                                        Thursday</option>
                                                    <option value="Friday"
                                                        {{ in_array('Friday', $daysOff ?? []) ? 'selected' : '' }}>Friday
                                                    </option>
                                                    <option value="Saturday"
                                                        {{ in_array('Saturday', $daysOff ?? []) ? 'selected' : '' }}>
                                                        Saturday</option>
                                                </select>


                                                @error('days_off')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit"
                                                class="btn btn-block bg-gradient-primary btn-sm">Save</button>
                                        </div>
                                    </div>
                                    <br>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let map, marker;

        function initMap() {
            // Initialize the map
            const lat = parseFloat(document.getElementById('latitude').value) ||  24.7136;
            const lng = parseFloat(document.getElementById('longitude').value) || 46.6753;
            const initialLocation = {lat: lat, lng: lng};

            map = new google.maps.Map(document.getElementById('map'), {
                center: initialLocation,
                zoom: 8
            });

            // Place initial marker if coordinates are provided
            if (lat && lng) {
                placeMarker(initialLocation);
            }

            // Add a marker on click
            map.addListener('click', function(event) {
                placeMarker(event.latLng);
            });
        }

        function placeMarker(location) {
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map
                });
            }

            // Set the latitude and longitude values to the hidden fields
            document.getElementById('latitude').value = location.lat();
            document.getElementById('longitude').value = location.lng();
        }
    </script>
    <script type="text/javascript">
        $(function() {
            $('#reservationdate').datetimepicker({
                format: 'L'
            });
        });

        function previewLicenseFrontImage(event) {
            const input = event.target;
            const preview = document.getElementById('licenseFrontImagePreview');

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

        function previewLicenseBackImage(event) {
            const input = event.target;
            const preview = document.getElementById('licenseBackImagePreview');

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
        $(document).ready(function() {




            // Initialize Select2 for city and days_off fields
            $('.city-select').select2({
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
