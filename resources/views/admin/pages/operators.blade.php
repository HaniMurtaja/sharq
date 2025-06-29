@extends('admin.layouts.app')

<style>
    .custom-select-height {
        height: 3rem !important;
    }

    .custom-margin {
        height: 3rem;
        margin-top: 10px !important
    }

    .modal.show {
        opacity: 1 !important;
    }

    @media (max-width: 992px) {
        .top-lg-70px {
            top: 70px !important;
        }
    }

    .roundCheckbox input[type="checkbox"]:checked+label:after {
        opacity: 1;
    }

    .roundCheckbox input[type="checkbox"]:checked+label {
        background-color: #66bb6a;
        border-color: #66bb6a;
    }
</style>



@section('content')
    <!-- Drawer Overlay -->
    <div id="drawer-overlay" data-drawer="Individuals" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 drawer-overlay">
    </div>


    <!-- New Individual -->
    <!-- Drawer -->
    @include('admin.pages.operators.add-edit')
    <!-- End Drawer -->


    <!-- New Shift -->
    <!-- Drawer -->

    @include('admin.pages.shifts.add-edit')

    <!-- End Drawer -->

    <!-- New Group -->
    <!-- Drawer -->
    @include('admin.pages.groups.add-edit')
    <!-- End Drawer -->

    <div class="p-6">
        <!-- Navigation Tabs -->
        <div class="flex flex-col-reverse justify-between gap-4 py-6 md:gap-0 md:flex-row">
            <div class="flex mb-2 space-x-8 border-b operator_tabs">
                <button class="px-4 py-2 font-semibold border-b-2 border-mainColor text-mainColor operator_tab"
                    data-tab="Individuals" id="operators">
                    Individuals
                </button>
                <button class="px-4 py-2 operator_tab" id="shifts" data-tab="Shifts">
                    Shifts
                </button>
                <button class="px-4 py-2 operator_tab" data-tab="Groups" id="groups">
                    Groups
                </button>
            </div>


            <div class="flex space-x-4 operator_btns " data-tab="Individuals">
                <a href="/operator-report.html"
                    class="flex items-center justify-center w-full gap-1 px-4 py-2 bg-transparent border rounded-md md:gap-3 md:w-48 border-blue1 text-blue1">
                    <img src="src/assets/icons/book.svg" alt="" />
                    <span>Operator Report</span>
                </a>
                <button type="button" id="operators-new"
                    class="flex items-center justify-center w-full gap-3 px-4 py-2 text-white rounded-md open-drawer md:w-48 bg-blue1 border-blue1"
                    data-drawer="Individuals">
                    <img src="src/assets/icons/add-square.svg" alt="" />
                    <span>New</span>
                </button>
            </div>

            <div class="hidden space-x-4 operator_btns" data-tab="Shifts">

                <button type="button" id="shifts-new"
                    class="flex items-center justify-center w-full gap-3 px-4 py-2 text-white rounded-md open-drawer md:w-48 bg-blue1 border-blue1"
                    data-drawer="Shifts">
                    <img src="src/assets/icons/add-square.svg" alt="" />
                    <span>New</span>
                </button>
            </div>

            <div class="hidden space-x-4 operator_btns" data-tab="Groups">

                <button type="button" id="new-group"
                    class="flex items-center justify-center w-full gap-3 px-4 py-2 text-white rounded-md open-drawer md:w-48 bg-blue1 border-blue1"
                    data-drawer="Groups">
                    <img src="src/assets/icons/add-square.svg" alt="" />
                    <span>New</span>
                </button>
            </div>
        </div>

        <!-- Individuals -->
        @include('admin.pages.operators.list')

        @include('admin.pages.shifts.list')

        @include('admin.pages.groups.list')

        <!-- Pagination -->


    </div>

    <div>

        <div class="modal fade " id="historyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered my-modal-dialog  modal-lg"
                role="document">
                <div class="modal-content top-lg-70px">
                    <div class="modal-header p-2 border-0">
                        <h5 class="modal-title fw-bold">
                            Identity Verfication
                        </h5>
                        <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                    fill="black"></path>
                            </svg>

                        </button>
                    </div>
                    <div class="modal-body p-2">

                        <div class="flex flex-col w-full gap-3" id="verificaation_div">
                            <h6
                                class="text-sm fw-bold d-flex justify-content-between align-items-center bg-gray-100 w-full p-2 rounded border border-gray-400 dashed-border">
                                Identity verfication status
                                <span class="text-sm text-success" id="verification_status">(Verified)</span>
                            </h6>
                            <div class="grid w-full  grid-cols-1 gap-2 md:grid-cols-1">
                                <div
                                    class="text-sm fw-bold d-flex justify-content-between align-items-center w-full p-2 rounded border border-gray-400 dashed-border">
                                    <span>Verify</span>
                                    <div class="roundCheckbox w-7">
                                        <input type="checkbox" id="verification_checkbox" />
                                        <label for="verification_checkbox"></label>
                                    </div>
                                </div>
                            </div>
                            <p class="fw-bold text-sm">National Info</p>
                            <input
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                hidden name="operator_verification_id" id="operator_verification_id">



                            <div class="rounded border border-gray-400 dashed-border p-2">
                                <div class="grid w-full grid-cols-1 gap-2  md:grid-cols-1">
                                    <span>ID Number</span>
                                    <div class="d-flex justify-content-between align-items-center ">
                                        <p class="font-bold w-full p-2  rounded border border-gray-400 dashed-border"
                                            id="social_id_no">

                                            -----
                                        </p>
                                    </div>
                                </div>

                                <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-2">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex justify-between w-full gap-2">
                                            <span class="text-sm">Front Side</span>
                                        </div>
                                        <div class="w-full h-230px p-2 rounded border border-gray-400 dashed-border">
                                            <img id ="id_card_image_front" class="w-full h-full object-contain"
                                               src="{{asset('new/src/assets/images/No_Image_Available.jpeg')}}" />


                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <div class="flex justify-between w-full gap-2">
                                            <span class="text-sm">Back Side</span>
                                        </div>
                                        <div class="w-full h-230px p-2 rounded border border-gray-400 dashed-border">
                                            <img id="id_card_image_back" class="w-full h-full object-contain"
                                               src="{{asset('new/src/assets/images/No_Image_Available.jpeg')}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <p class="fw-bold text-sm">License Info</p>

                            <div class="rounded border border-gray-400 dashed-border p-2">
                                <div class="grid w-full grid-cols-1 gap-8  md:grid-cols-2">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex justify-between w-full gap-2">
                                            <span class="text-sm">Front Side</span>
                                        </div>
                                        <div class="w-full h-230px p-2 rounded border border-gray-400 dashed-border">
                                            <img id ="license_image_front" class="w-full h-full object-contain"
                                                src="{{asset('new/src/assets/images/No_Image_Available.jpeg')}}" />


                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <div class="flex justify-between w-full gap-2">
                                            <span class="text-sm">Back Side</span>
                                        </div>
                                        <div class="w-full h-230px p-2 rounded border border-gray-400 dashed-border">
                                            <img id="license_image_back" class="w-full h-full object-contain"
                                               src="{{asset('new/src/assets/images/No_Image_Available.jpeg')}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @include('admin.pages.operators.scripts')
@endsection


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo"></script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqpmaTL93NuLN-EsSUN_-5lmVtTqnLIAo&callback=initMap2&libraries=places&v=weekly"
    defer></script>


<script>
    let formMap;

    let marker;



    window.initAutocomplete = initMap2;

    function initMap2() {
        const lat = parseFloat(document.getElementById('lat_order_hidden').value) || 24.7136;
        const lng = parseFloat(document.getElementById('long_order_hidden').value) || 46.6753;
        const initialLocation = {
            lat: lat,
            lng: lng
        };
        const formMap = new google.maps.Map(document.getElementById("formMap"), {
            center: initialLocation,
            zoom: 13,
            mapTypeId: "roadmap",
        });
        // Create the search box and link it to the UI element.
        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);

        formMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // Bias the SearchBox results towards current map's viewport.
        formMap.addListener("bounds_changed", () => {
            searchBox.setBounds(formMap.getBounds());
        });

        let markers = [];

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach((marker) => {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();

            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };

                // Create a marker for each place.
                markers.push(
                    new google.maps.Marker({
                        formMap,
                        icon,
                        title: place.name,
                        position: place.geometry.location,
                    }),
                );
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            formMap.fitBounds(bounds);
        });

        var marker;
        formMap.addListener('click', function(event) {
            console.log(4546);

            var lat = event.latLng.lat();
            var lng = event.latLng.lng();
            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: formMap
            });
            console.log(marker);

            $('#lat_order_hidden').val(lat);
            $('#long_order_hidden').val(lng);



        });
    }
</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const openDrawerButtons = document.querySelectorAll('.open-drawer');

        openDrawerButtons.forEach((button) => {
            button.addEventListener('click', function() {
                console.log('open');
                const drawer = document.getElementById('drawer');

                drawer.classList.remove('translate-x-full');
                $('.city').select2({
                    placeholder: "Select a city",
                    allowClear: true,
                });
                $('.select2').select2({
                    allowClear: true,
                });
                $('.days').select2({
                    placeholder: "Days off",
                    allowClear: true,
                });









                const fileUpload2 = document.getElementById('file-upload');
                const uploadLabel2 = document.getElementById('upload-label');
                const userIcon2 = document.getElementById('user-icon');
                console.log(fileUpload2);

                fileUpload2.addEventListener('change', (event) => {
                    console.log('file');
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            uploadLabel2.style.backgroundImage =
                                `url(${e.target.result})`;
                            userIcon2.style.display = 'none';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $('#shift_from').select2({
                    allowClear: true,
                    placeholder: 'Client',
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
                    },
                });

                $('#shift_from_type').select2({
                    allowClear: true,
                    placeholder: 'Client',
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
                    },
                });


                $('#shift_to').select2({
                    allowClear: true,
                    placeholder: 'Client',
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
                    },
                });

                $('#shift_to_type').select2({
                    allowClear: true,
                    placeholder: 'Client',
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
                    },
                });

                const fileUploadVehicle = document.getElementById('file-upload-vehicle');
                const uploadLabelVehicle = document.getElementById('upload-label-vehicle');
                const userIconVehicle = document.getElementById('user-icon-vehicle');

                fileUploadVehicle.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            uploadLabelVehicle.style.backgroundImage =
                                `url(${e.target.result})`;
                            userIconVehicle.style.display = 'none';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });






    });











    $(document).ready(function() {



        $(document).on('change', '.btn-file :file', function() {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [label]);
        });

        $('.btn-file :file').on('fileselect', function(event, label) {

            var input = $(this).parents('.input-group').find(':text'),
                log = label;

            if (input.length) {
                input.val(log);
            } else {
                if (log) alert(log);
            }

        });

        function readURLProfile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#profile_image').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURLIDCard(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#id_card_privew').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURLFrontLicence(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#front_image_lice_privew').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURLBackLicence(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#back_image_lice_privew').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imgInpProfile").change(function() {
            readURLProfile(this);
        });

        $("#imgInpIDCard").change(function() {
            readURLIDCard(this);
        });

        $("#imgInpBackLicence").change(function() {
            readURLBackLicence(this);
        });

        $("#imgInpFrontLicence").change(function() {
            readURLFrontLicence(this);
        });




        // Initialize Select2 for city and days_off fields

    });
</script>
