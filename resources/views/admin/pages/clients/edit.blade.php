@extends('admin.layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.0/css/dataTables.dataTables.min.css">
@endsection
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        /* Reduced width */
        height: 20px;
        /* Reduced height */
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        /* Reduced height */
        width: 14px;
        /* Reduced width */
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #f46624;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #f46624;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(20px);
        /* Adjusted for smaller width */
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 20px;
        /* Adjusted for smaller height */
    }

    .slider.round:before {
        border-radius: 50%;
    }


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

    /* Default fade behavior */
    .fade {
        opacity: 0;
        display: none !important;
        transition: opacity 0.3s ease-in-out, display 0.3s ease-in-out;
    }

    /* When show is applied, override fade */
    .fade.show {
        opacity: 1 !important;
        display: block !important;
        transition: opacity 0.3s ease-in-out, display 0.3s ease-in-out;
    }

    .select2-container--default .select2-results>.select2-results__options {
        max-height: 75px;
        overflow-y: auto;
        font-size: .8rem;
    }
</style>

@section('content')
    {{-- @include('admin.includes.content-header', ['header' => 'Edit Client', 'title' => 'Clients']) --}}

    <div class="flex flex-col p-6">

        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="tab-pane" id="settings">

                        <div class="card-body">


                            <form id="client-form" enctype="multipart/form-data"
                                action="{{ route('update-client', $client->id) }}" method="post">
                                @method('PUT')
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="file" id="file-upload" class="file-upload" name="profile_photo"
                                                accept="image/*">
                                            @if ($profile_url)
                                                <label for="file-upload" class="upload-label"
                                                    style="background-image: url('{{ $profile_url }}')"
                                                    id="upload-label">
                                                </label>
                                            @else
                                                <label for="file-upload" class="upload-label" id="upload-label">
                                                    <svg viewBox="0 0 24 24" id="user-icon">
                                                        <path
                                                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                    </svg>
                                                </label>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <a type="button" href = "{{ route('clients') }}" id="save-client-btn"
                                            style="background-color: #F46624; color:#f7f7f7; height:38px; float: right;"
                                            class="btn">Back <i class="fa-solid fa-arrow-right"></i> </a>
                                    </div>
                                </div>

                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="flex justify-between">
                                            <label class="block mb-2 text-black1">Auto Dispatch</label>
                                            <div class="switch-container">
                                                <input type="checkbox" value="{{ $client?->client?->auto_dispatch }}"
                                                    name="auto_dispatch"
                                                    {{ $client?->client?->auto_dispatch == 1 ? 'checked' : '' }}
                                                    id="auto_dispatch" class="switch-checkbox" />
                                                <label for="auto_dispatch" class="switch-label">
                                                    <span class="switch-button"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>




                                    <div class="col-md-6">
                                        <div class="flex justify-between">
                                            <label class="block mb-2 text-black1">Integration</label>
                                            <div class="switch-container">
                                                <input type="checkbox" value="{{ $client?->client?->is_integration }}"
                                                    name="is_integration"
                                                    {{ $client?->client?->is_integration == 1 ? 'checked' : '' }}
                                                    id="is_integration" class="switch-checkbox" />
                                                <label for="is_integration" class="switch-label">
                                                    <span class="switch-button"></span>
                                                </label>
                                            </div>
                                        </div>

                                    </div>



                                </div>

                                <!-- Auto Dispatch -->



                                <!-- Integration -->
                                <br>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Token</label>
                                        <p>{{ $client->integration_token }}</p>
                                    </div>


                                </div>





                                {{-- <div class="row">
                                        <div class="col-md-6">
                                            <p for="id_card_image" class="form-label">Personal Photo</p>
                                            @if ($client->getFirstMediaUrl('profile'))
                                                <img id="profileImagePreview"
                                                    src="{{ $client->getFirstMediaUrl('profile') }}"
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
                                    </div> --}}
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input hidden name="account_number" id="account_number"
                                                value="{{ $client->account_number }}">
                                            <input type="text" class="form-control" id="account_number" name="account_number"
                                                placeholder="account number" value="{{ old('account_number', $client->account_number) }}">

                                            @error('account_number')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input hidden name="current_client_id" id="current_client_id"
                                                value="{{ $client->id }}">
                                            <input type="text" class="form-control" id="firstName" name="name"
                                                placeholder="Name" value="{{ old('name', $client->first_name) }}">

                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="lastName" name="phone"
                                                placeholder="Phone Number" value="{{ old('phone', $client->phone) }}">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" value="{{ old('email', $client->email) }}"
                                                class="form-control" id="email" name="email" placeholder="Email">
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Password">
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control select2" style="width: 100%;" name="country_id">
                                                <option value="" selected="selected" disabled>Country</option>
                                                @foreach ($countries as $country)
                                                    <option
                                                        {{ old('countery_id', $client->client?->country_id) == $country->id ? 'selected' : '' }}
                                                        value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('countery')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control select2" name="city_id" style="width: 100%;">
                                                <option value="" selected="selected" disabled>City</option>
                                                @foreach ($all_cities as $city)
                                                    <option
                                                        {{ old('city_id', $client->client?->city_id) == $city->id ? 'selected' : '' }}
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control select2" style="width: 100%;" name="currency">
                                                <option value="" selected="selected" disabled>Currency</option>
                                                @foreach (App\Enum\Currency::values() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ $client->client?->currency?->value == $key ? 'selected' : '' }}>
                                                        {{ $value }}</option>
                                                @endforeach
                                            </select>
                                            @error('currency')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="defaultPreparationTime"
                                                name="default_prepartion_time"
                                                value="{{ old('name', $client->client?->default_prepartion_time) }}"
                                                placeholder="Default preparation time">
                                            @error('default_prepartion_time')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="minPreparationTime"
                                                name="min_prepartion_time"
                                                value="{{ old('name', $client->client?->min_prepartion_time) }}"
                                                placeholder="Min. preparation time">
                                            @error('min_prepartion_time')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="partialPay"
                                                name="partial_pay"
                                                value="{{ old('partial_pay', $client->client?->partial_pay) }}"
                                                placeholder="Partial pay">
                                            @error('partial_pay')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="note" name="note"
                                                placeholder="Note" value="{{ old('note', $client->client?->note) }}">
                                            @error('note')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <p>Group</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control select2" style="width: 100%;"
                                                name="client_group_id">
                                                <option value="" selected="selected" disabled>Client group
                                                </option>
                                                @foreach ($client_groups as $group)
                                                    <option
                                                        {{ old('client_group_id', $client->client?->client_group_id) == $group->id ? 'selected' : '' }}
                                                        value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('client_group_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control select2" style="width: 100%;"
                                                name="driver_group_id">
                                                <option value="" selected="selected" disabled>Driver group
                                                </option>
                                                @foreach ($driver_groups as $group)
                                                    <option value="{{ $group->id }}"
                                                        {{ old('driver_group_id', $client->client?->driver_group_id) == $group->id ? 'selected' : '' }}>
                                                        {{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('driver_group_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="integration-div" style="display: none">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control select2" style="width: 100%;" id="integration_id"
                                                name="integration_id">
                                                <option value="" selected="selected" disabled>Integration
                                                </option>
                                                @foreach ($integrations as $integration)
                                                    <option value="{{ $integration->id }}"
                                                        {{ old('integration_id', $client->client?->integration_id) == $integration->id ? 'selected' : '' }}>
                                                        {{ $integration->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <button type="submit" id="save-client-btn"
                                            style="background-color: #F46624; color:#f7f7f7; height:38px"
                                            class="btn">Save changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-sm-4">

                <div class="card">
                    <div class="card-body">
                        <i class="fa-solid fa-house"></i> &nbsp; &nbsp;
                        {{ $client->branches()->count() }} Branches
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fa-solid fa-list"></i> &nbsp; &nbsp;
                        {{ $client->orders()->count() }} Orders

                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">

                        <div id="div-balance">
                            <i class="fa-solid fa-dollar-sign"></i>
                            &nbsp; &nbsp;
                            <a href="#" id="charge-balance" data-bs-toggle="modal" data-bs-target="#chargeModal">
                                <span style="color: #f46624; text:bold">
                                    <div style="display: inline" id="amount-div">
                                        {{ $client->wallet ? $client->wallet?->balance : 0 }} </div> Balance
                                </span> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        @include('admin.pages.clients.orders')


        @include('admin.pages.clients.branches')

        @include('admin.pages.clients.users')



    </div>


    <div class="modal fade" style="height: 300px;" id="chargeModal">
        <div class="modal-dialog my-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Charge Wallet
                    </h5>

                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="chargeWalletForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p>Amount</p>
                                    <input class="form-control" style="width: 100%;" name="amount" id="amount"
                                        type="number" required>
                                    <div style="color:red" id="amountError"></div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <br>

                    <div class="modal-footer">
                        <button class="btn" style="background-color: #F46624; color:#f7f7f7; height:38px"
                            id="charge-client-balance" type="button">
                            Save
                        </button>

                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal" aria-label="Close">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>


    <script src="//cdn.datatables.net/2.1.0/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo"></script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
        defer></script>

    <script>
        let formMap;
        let marker;
        let lat = 24.7136;
        let lng = 46.6753;




        window.initAutocomplete = initMap2;

        function initMap2() {
            console.log('999999');

            const latInput = document.getElementById('lat_order_hidden');
            const lngInput = document.getElementById('long_order_hidden');

            lat = parseFloat(latInput.value) || 24.7136;
            lng = parseFloat(lngInput.value) || 46.6753;

            const initialLocation = {
                lat: lat,
                lng: lng
            };

            formMap = new google.maps.Map(document.getElementById("formMap"), {
                center: initialLocation,
                zoom: 13,
                mapTypeId: "roadmap",
            });

            marker = new google.maps.Marker({
                position: initialLocation,
                map: formMap
            });

            // Extract Lat/Lng from Google Maps URL
            function extractLatLngFromLink(link) {
                const regex = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
                const match = link.match(regex);
                if (match) {
                    const extractedLat = parseFloat(match[1]);
                    const extractedLng = parseFloat(match[2]);
                    return {
                        lat: extractedLat,
                        lng: extractedLng
                    };
                }
                return null;
            }

            // Function to update the map with new lat/lng
            function updateMap(lat, lng) {
                const newPosition = {
                    lat: lat,
                    lng: lng
                };
                formMap.setCenter(newPosition);
                if (marker) marker.setMap(null);

                marker = new google.maps.Marker({
                    position: newPosition,
                    map: formMap
                });
                console.log(marker);


                // Update the input fields
                latInput.value = lat;
                lngInput.value = lng;
            }


            // Handle shortened Google Maps URLs
            function resolveShortUrl(shortUrl) {
                $.ajax({
                    url: '{{ route('resolve-url') }}',
                    method: 'POST',
                    data: {
                        url: shortUrl,
                        _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
                    },
                    success: function(response) {
                        // Check if the response has lat and lng
                        if (response.lat && response.lng) {
                            // Log the resolved coordinates for debugging
                            console.log("Resolved coordinates:", response.lat, response.lng);
                            const lat = parseFloat(response.lat);
                            const lng = parseFloat(response.lng);
                            updateMap(lat, lng);

                            // Update the map with resolved lat/lng
                        } else {
                            alert('Unable to resolve the shortened URL.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while resolving URL.');
                    }
                });
            }

            // Search by link functionality
            const searchLinkInput = document.getElementById('search-link');
            searchLinkInput.addEventListener('change', function() {
                const mapLink = searchLinkInput.value;

                if (mapLink.includes('goo.gl')) {
                    // If it's a shortened URL, resolve it first
                    console.log('Shortened URL detected:', mapLink);
                    resolveShortUrl(mapLink); // No need for a callback; handle it in the success function
                } else {
                    // Handle full Google Maps links directly
                    const extractedLatLng = extractLatLngFromLink(mapLink);
                    if (extractedLatLng) {
                        console.log("Extracted coordinates from full URL:", extractedLatLng);
                        updateMap(extractedLatLng.lat, extractedLatLng
                            .lng); // Update the map with extracted lat/lng
                    } else {
                        alert('Invalid Google Maps link format.');
                    }
                }
            });


            // Click event on map to update lat/lng inputs and place a marker
            formMap.addListener('click', function(event) {
                lat = event.latLng.lat();
                lng = event.latLng.lng();

                if (marker) marker.setMap(null);

                marker = new google.maps.Marker({
                    position: {
                        lat: lat,
                        lng: lng
                    },
                    map: formMap
                });

                // Update the lat/lng input fields when clicking the map
                latInput.value = lat;
                lngInput.value = lng;
            });

            // Add event listeners to lat/lng inputs to update the map when values change
            function updateMapByLatLng() {
                const newLat = parseFloat(latInput.value);
                const newLng = parseFloat(lngInput.value);

                if (!isNaN(newLat) && !isNaN(newLng)) {
                    updateMap(newLat, newLng);
                }
            }

            latInput.addEventListener('change', updateMapByLatLng);
            lngInput.addEventListener('change', updateMapByLatLng);
        }




        function updateCheckboxValues() {
            $('input[type="checkbox"]').each(function() {
                $(this).val(this.checked ? 1 : 0);
            });
        }

        document.getElementById('auto_dispatch').addEventListener('change', function() {
            console.log(454);

            updateCheckboxValues();
        });

        document.getElementById('is_integration').addEventListener('change', function() {
            console.log(454);

            updateCheckboxValues();
        });



        console.log('hi');




        $(document).on('change', '.status-toggle', function() {
            console.log('Status toggle changed:', $(this).data('id'));
            updateCheckboxValues();
        });

        $(document).on('change', '#auto-dispatch-toggle', function() {
            console.log('Auto dispatch toggle changed:', $(this).data('id'));
            updateCheckboxValues();
        });



        function reinitializeSelect2() {
            $('#repeater .row select').each(function() {

                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }

                $(this).select2({
                    allowClear: true,

                });
            });
        }

        function modalCLosed() {
            console.log('closed');

            // Reset latitude and longitude
            lat = 24.7136;
            lng = 46.6753;

            // Update the map and marker with the new coordinates
            updateMap(lat, lng);
        }

        // Ensure the `updateMap` function updates the map and marker position
        function updateMap(lat, lng) {
            console.log('update');

            const newPosition = {
                lat: lat,
                lng: lng
            };
            formMap.setCenter(newPosition); // Center the map on the new position

            if (marker) {
                marker.setPosition(newPosition); // Update marker's position
            } else {
                // If the marker doesn't exist, create it
                marker = new google.maps.Marker({
                    position: newPosition,
                    map: formMap
                });
            }

            // Update any other elements that reflect latitude and longitude values
            document.getElementById('lat_order_hidden').value = lat;
            document.getElementById('long_order_hidden').value = lng;
        }




        $(document).ready(function() {

            $(document).on('change', '#branch-country', function() {
                citySelect = $('#branch-city');
                citySelect.prop('disabled', false);
            });



            $('#type').select2({
                allowClear: true,
                placeholder: 'Type',
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    return data.text;
                },
                templateSelection: function(data) {
                    if (!data.id) {
                        return data.text;
                    }
                    return data.text;
                }
            });


            $('#is_integration').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#integration-div').show();
                } else {
                    $('#integration-div').hide();
                }
            });


            if ($('#is_integration').is(':checked')) {
                $('#integration-div').show();
            } else {
                $('#integration-div').hide();
            }


            $(document).on('change', '#branch-city', function() {
                var cityId = $(this).val();
                var areaSelect = $('#branch-area');

                if (cityId) {
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



            var id = $('#current_client_id').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2({
                allowClear: true
            });

            $('#openUsersModal').on('click', function() {

                $('#nav-home-tab').tab('show');
            });



            $('#charge-client-balance').click(function() {

                $('#amountError').text('');

                $.ajax({
                    url: "{{ route('charge-client-wallet') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: $('#amount').val(),
                        client_id: id
                    },
                    success: function(response) {
                        if (response.success) {

                            console.log('Client wallet charged successfully!');

                            $('#chargeModal').hide(); // Hides the modal
                            $('.modal-backdrop').remove(); // Removes the backdrop
                            $('body').removeClass('modal-open');
                            console.log(response);
                            $('#amount').val('')
                            $('#amount-div').html(response.amount);
                        }
                    },
                    error: function(xhr) {

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.amount) {
                                $('#amountError').text(errors.amount[0]);
                            }
                        }
                    }
                });
            });


            var usersTable = $('#users-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-client-users') }}",
                    "type": "GET",
                    'data': function(d) {
                        d.id = id;
                    },
                    "dataSrc": function(response) {
                        console.log('AJAX Response:', response);

                        // Update Select2 dropdown
                        $('#client-exist-user-form #user_id').empty();
                        $('#client-exist-user-form #user_id').append(
                            '<option value="" selected="selected" disabled>User</option>');

                        if (response.all_users) {
                            response.all_users.forEach(function(user) {
                                $('#client-exist-user-form #user_id').append('<option value="' +
                                    user.id + '">' + user.name + '</option>');
                            });

                            $('#client-exist-user-form #user_id').select2({
                                width: '100%'
                            });
                        }

                        // Return only the data part to DataTables
                        return response.data;
                    },
                    "error": function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                    <label class="switch">
                        <input type="checkbox" class="status-toggle" data-id="${row.model.id}" ${row.model.status ? 'checked' : ''}>
                        <span class="slider round"></span>
                    </label>
                `;
                        },
                        "orderable": false
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                    <label class="switch">
                        <a href="#" data-toggle="modal" data-target="#editUsersModel" class="nav-link edit-client-user" data-user='${JSON.stringify(row)}'>
                            <i class="nav-icon fas fa fa-cog" style="color: #f46624"></i>
                        </a>
                    </label>
                `;
                        },
                        "orderable": false
                    }
                ],
                "pageLength": 3,
                "lengthChange": false
            });





            var ordersTable = $('#orders-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-client-orders') }}",
                    "type": "GET",
                    'data': {
                        'id': id
                    }
                },

                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "branch"
                    },
                    {
                        "data": "customer_name"
                    },
                    {
                        "data": "customer_area"
                    },

                    {
                        "data": "status"
                    },

                ],
                "pageLength": 3,
                "lengthChange": false
            });








            var branchesTable = $('#branches-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('get-client-branches') }}",
                    "type": "GET",
                    'data': {
                        'id': id
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            let input_id = 'status_toggel' + row.model.id;
                            return `



                     <div class="switch-container">
                                                <input type="checkbox" id="${input_id}"
                                                     data-id="${row.model.id}" ${row.model.status ? 'checked' : ''}

                                                   class="switch-checkbox status-toggle" />
                                                <label for="${input_id}" class="switch-label">
                                                    <span class="switch-button"></span>
                                                </label>
                                            </div>
                `;
                        },
                        "orderable": false
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            let input_id = 'auto_dispatch' + row.model.id;
                            return `
                    <div class="switch-container">
                        <input type="checkbox"  id ="${input_id}"
                            data-id="${row.model.id}" ${row.model.auto_dispatch ? 'checked' : ''}
                            class="auto-dispatch-toggle switch-checkbox" />
                        <label for="${input_id}" class="switch-label">
                            <span class="switch-button"></span>
                        </label>
                    </div>
                `;
                        },
                        "orderable": false
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                    <label class="switch">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"
                           class="nav-link edit-client-branch"  data-branch='${JSON.stringify(row)}'>
                            <i class="nav-icon fas fa fa-cog" style="color: #f46624"></i>
                        </a>
                    </label>
                `;
                        },
                        "orderable": false
                    }
                ],
                "pageLength": 3,
                "lengthChange": false
            });



            $(document).on('click', '.edit-client-branch', function(event) {
                var data = $(this).data('branch');
                var branch = data.model;
                var user_branch = data.user_branch;
                console.log(user_branch)
                if (branch) {
                    $('#exampleModalLabel').text('Edit Branch');
                    $('#client-branch-form input[name="branch_name"]').val(branch.name);
                    $('#client-branch-form input[name="branch_id"]').val(branch.id);
                    $('#client-branch-form input[name="user_branch_id"]').val(user_branch.id);
                    $('#client-branch-form input[name="branch_email"]').val(user_branch.email);
                    $('#client-branch-form input[name="branch_phone"]').val(branch.phone);
                    $('#client-branch-form select[name="client_group_id"]').val(branch.client_group_id)
                        .trigger('change');
                    $('#client-branch-form select[name="driver_group_id"]').val(branch.driver_group_id)
                        .trigger('change');
                    $('#client-branch-form input[name="lat"]').val(branch.lat);
                    $('#client-branch-form input[name="lng"]').val(branch.lng);
                    $('#client-branch-form select[name="country"]').val(branch.country).trigger('change');
                    $('#client-branch-form select[name="city_id"]').val(branch.city_id).trigger('change');
                    $('#client-branch-form select[name="area_id"]').val(branch.area_id).trigger('change');
                    $('#client-branch-form select[name="pickup_id"]').val(branch.pickup_id);
                    $('#client-branch-form input[name="street"]').val(branch.street);
                    $('#client-branch-form input[name="custom_id"]').val(branch.custom_id);
                    $('#client-branch-form input[name="landmark"]').val(branch.landmark);
                    $('#client-branch-form input[name="building"]').val(branch.building);
                    $('#client-branch-form input[name="floor"]').val(branch.floor);
                    $('#client-branch-form input[name="apartment"]').val(branch.apartment);
                    $('#client-branch-form textarea[name="discription"]').val(branch.discription);

                    updateMap(parseFloat(branch.lat), parseFloat(branch.lng));

                    // Handle business hours if they exist
                    if (branch.business_hours && branch.business_hours.length > 0) {
                        $('#repeater .row').not(':first').remove(); // Remove existing rows except the first
                        branch.business_hours.forEach(function(hour, index) {
                            if (index > 0) {
                                $('#repeater .btn-add').last().click(); // Add new rows
                            }
                            var row = $('#repeater .row').eq(index);
                            row.find('select[name^="business_hours["][name$="[day]"]').val(hour.day)
                                .trigger('change');
                            row.find('select[name^="business_hours["][name$="[start]"]').val(hour
                                .start).trigger('change');
                            row.find('select[name^="business_hours["][name$="[end]"]').val(hour.end)
                                .trigger('change');
                        });
                    }

                } else {
                    $('#exampleModalLabel').text('Add new branch');

                    $('#client-branch-form').trigger("reset");
                    $('#repeater .row').not(':first').remove(); // Remove all rows except the first
                    $('#repeater .row:first select').val('').trigger(
                        'change'); // Reset the first row's selects
                }
            });

            $('#create-client-branch').on('click', function() {
                // Reset the form

                $('#client-branch-form')[0].reset();
                modalCLosed();
                // Remove all repeater rows except the first one
                $('#repeater .row').not(':first').remove();

                // Set default values for the first row's selects
                $('#repeater .row:first').each(function() {
                    // Find the select elements within the row
                    var $selects = $(this).find('select');

                    // Loop through each select and set the first option as selected and disabled
                    $selects.each(function() {
                        var $select = $(this);
                        // Set the first option as selected and disabled
                        $select.find('option:first').prop('selected', true).prop('disabled',
                            true);
                        // Trigger change event to update Select2
                        $select.trigger('change');
                    });
                });

                reinitializeSelect2();
            });

            function reinitializeSelect2() {
                $('.select2').select2({
                    allowClear: true
                });
            }


            $('#save-client-branch-btn').click(function() {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                var formData = new FormData($('#client-branch-form')[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('save-client-branch') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#client-branch-form')[0].reset();
                        alert('branch saved successfully');
                        $('#exampleModal .btnClose').trigger('click');

                        lat = 24.7136;
                        lng = 46.6753;
                        branchesTable.ajax.reload();


                    },
                    error: function(error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputElement = $('[name="' + key + '"]');
                                inputElement.addClass('is-invalid');
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            });
                        } else {
                            console.error(error);
                        }
                    }
                });
            });





            $('#save-client-user-btn').click(function() {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                var formData = new FormData($('#client-user-form')[0]);
                formData.append('id', id);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('save-client-user') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#client-branch-form')[0].reset();
                        alert('User saved successfully');
                        $('#usersModel').hide(); // Hides the modal
                        $('.modal-backdrop').remove(); // Removes the backdrop
                        $('body').removeClass('modal-open');
                        usersTable.ajax.reload();


                    },
                    error: function(error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputElement = $('[name="' + key + '"]');
                                inputElement.addClass('is-invalid');
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            });
                        } else {
                            console.error(error);
                        }
                    }
                });
            });

            $('#save-client-exist-user-btn').click(function() {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                var formData = new FormData($('#client-exist-user-form')[0]);
                formData.append('id', id);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('save-client-exist-user') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#user_id').find('option:first').prop('selected', true).prop(
                            'disabled',
                            true);

                        $('#user_id').trigger('change');

                        alert('User saved successfully');
                        $('#usersModel').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        usersTable.ajax.reload();


                    },
                    error: function(error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputElement = $('[name="' + key + '"]');
                                inputElement.addClass('is-invalid');
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            });
                        } else {
                            console.error(error);
                        }
                    }
                });

                $('#usersModel').on('shown.bs.modal', function() {
                    $('#nav-home-tab').tab('show');
                });
            });


            $(document).on('click', '.edit-client-user', function(event) {
                var data = $(this).data('user');
                console.log(data)
                var user = data.model.user;

                if (user) {
                    $('#edit-user-form input[name="first_name"]').val(user.first_name);
                    $('#edit-user-form input[name="last_name"]').val(user.last_name);
                    $('#edit-user-form input[name="email"]').val(user.email);
                    $('#edit-user-form input[name="phone"]').val(user.phone);
                    $('#edit-user-form input[name="edit_user_client_id"]').val(user.id);
                    $('#edit-user-form input[name="mac_address"]').val(data.user.mac_address);
                    $('#edit-user-form input[name="password"]').val(
                        '');
                    var profilePhotoUrl = data.profile_url;
                    if (profilePhotoUrl) {
                        var escapedUrl = profilePhotoUrl.replace(/\(/g, '%28').replace(/\)/g,
                            '%29'); // Escape parentheses
                        $('#upload-label-user').css('background-image', `url(${escapedUrl})`);
                        $('#user-icon-user').hide();
                    } else {
                        $('#upload-label-user').css('background-image', 'none');
                        $('#user-icon-user').show();
                    }
                }


            });


            $('#edit-client-user-btn').click(function() {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                var formData = new FormData($('#edit-user-form')[0]);
                formData.append('id', id);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('update-client-user') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#client-branch-form')[0].reset();
                        alert('User saved successfully');
                        $('#editUsersModel').hide(); // Hides the modal
                        $('.modal-backdrop').remove(); // Removes the backdrop
                        $('body').removeClass('modal-open');
                        usersTable.ajax.reload();


                    },
                    error: function(error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputElement = $('[name="' + key + '"]');
                                inputElement.addClass('is-invalid');
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            });
                        } else {
                            console.error(error);
                        }
                    }
                });
            });




            $('#client-user-delete-btn').click(function() {
                $('input').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                var formData = new FormData($('#edit-user-form')[0]);
                formData.append('id', id);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('delete-client-user') }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#client-branch-form')[0].reset();
                        alert('User Deleted successfully');
                        $('#editUsersModel').hide(); // Hides the modal
                        $('.modal-backdrop').remove(); // Removes the backdrop
                        $('body').removeClass('modal-open');
                        usersTable.ajax.reload();


                    },
                    error: function(error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var inputElement = $('[name="' + key + '"]');
                                inputElement.addClass('is-invalid');
                                inputElement.after(
                                    '<span class="text-danger invalid-feedback" role="alert">' +
                                    value[0] + '</span>');
                            });
                        } else {
                            console.error(error);
                        }
                    }
                });
            });









            $('#branches-table').on('change', '.status-toggle', function() {
                var branchId = $(this).data('id');
                var newStatus = $(this).is(':checked');
                var status = 0;
                if (newStatus) {
                    status = 1;
                }
                console.log(status);


                $.ajax({
                    url: '{{ route('change-client-branch-status') }}',
                    method: 'POST',
                    data: {
                        id: branchId,
                        status: status
                    },
                    success: function(response) {
                        console.log('Status updated successfully.');
                    },
                    error: function(error) {
                        console.log('Failed to update status.');
                    }
                });
            });


            $('#branches-table').on('change', '.auto-dispatch-toggle', function() {
                var branchId = $(this).data('id');
                var newAutoDispatch = $(this).is(':checked');
                var auto_dispatch = 0;
                if (newAutoDispatch) {
                    auto_dispatch = 1;
                }
                $.ajax({
                    url: '{{ route('change-client-branch-auto-dispatch') }}',
                    method: 'POST',
                    data: {
                        id: branchId,
                        auto_dispatch: auto_dispatch
                    },
                    success: function(response) {
                        console.log('Auto dispatch updated successfully.');
                    },
                    error: function(error) {
                        console.log('Failed to update auto dispatch.');
                    }
                });
            });



            $('#users-table').on('change', '.status-toggle', function() {

                var newStatus = $(this).is(':checked');
                var status = 0;
                var id = $(this).data('id');
                if (newStatus) {
                    status = 1;
                }

                $.ajax({
                    url: '{{ route('change-client-user-status') }}',
                    method: 'POST',
                    data: {
                        id: id,
                        status: status
                    },
                    success: function(response) {
                        console.log('Status updated successfully.');
                    },
                    error: function(error) {
                        console.log('Failed to update status.');
                    }
                });
            });











        });

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


        const fileUpload = document.getElementById('file-upload');
        const uploadLabel = document.getElementById('upload-label');
        const userIcon = document.getElementById('user-icon');

        fileUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadLabel.style.backgroundImage = `url(${e.target.result})`;
                    userIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });



        const fileUploadUser = document.getElementById('file-upload-user');
        const uploadLabelUser = document.getElementById('upload-label-user');
        const userIconUser = document.getElementById('user-icon-user');

        fileUploadUser.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadLabelUser.style.backgroundImage = `url(${e.target.result})`;
                    userIconUser.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
