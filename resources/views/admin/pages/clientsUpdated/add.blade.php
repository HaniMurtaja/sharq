@extends('admin.layouts.app')


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" defer></script>
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/cropperCustomStyle.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />


@section('title')
    Add Client
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2">


            <div class="flex flex-column pb-192 border-bottom justify-between md:flex-row gap-192 mb-4">
                <p id="tabDescription" class="text-black fs-192 fw-bold">
                    <a href="{{ route('clientupdated') }}">Clients</a>
                    <span class="fs-118 gray-94">»</span>
                    <span class="">Add Client</span>
                </p>
            </div>


            <form action="{{ route('clientupdated.store') }}" method="POST"
                class="p-4 bg-white br-96 d-flex flex-column gap-4" id="client-form" enctype="multipart/form-data">
                @csrf
                <p class="sectionTitle">Information</p>

                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-2">
                    <div class="position-relative w-50">
                        <input type="file" accept="image/*" name="profile_photo" id="fileInput" style="display: none;">
                        <button type="button" id="uploadButton" class="uploadImageBtn">
                            <svg width="25.6px" height="25.6px" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16.0013 29.3327C23.3651 29.3327 29.3346 23.3631 29.3346 15.9993C29.3346 8.63555 23.3651 2.66602 16.0013 2.66602C8.63751 2.66602 2.66797 8.63555 2.66797 15.9993C2.66797 23.3631 8.63751 29.3327 16.0013 29.3327Z"
                                    fill="#a30133" stroke="#a30133" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path
                                    d="M16.1622 17.04C16.0689 17.0267 15.9489 17.0267 15.8422 17.04C13.4956 16.96 11.6289 15.04 11.6289 12.68C11.6289 10.2667 13.5756 8.30667 16.0022 8.30667C18.4156 8.30667 20.3756 10.2667 20.3756 12.68C20.3622 15.04 18.5089 16.96 16.1622 17.04Z"
                                    fill="white" stroke="#a30133" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path
                                    d="M24.989 25.84C22.6156 28.0133 19.469 29.3333 16.0023 29.3333C12.5356 29.3333 9.38896 28.0133 7.01562 25.84C7.14896 24.5867 7.94896 23.36 9.37562 22.4C13.029 19.9733 19.0023 19.9733 22.629 22.4C24.0556 23.36 24.8556 24.5867 24.989 25.84Z"
                                    fill="white" stroke="#a30133" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                            <svg width="25.6px" height="25.6px" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect width="32" height="32" rx="16" fill="white"></rect>
                                <path
                                    d="M11.6334 24.3333H20.3667C22.6667 24.3333 23.5834 22.925 23.6917 21.2083L24.1251 14.325C24.2417 12.525 22.8084 11 21.0001 11C20.4917 11 20.0251 10.7083 19.7917 10.2583L19.1917 9.05001C18.8084 8.29167 17.8084 7.66667 16.9584 7.66667H15.0501C14.1917 7.66667 13.1917 8.29167 12.8084 9.05001L12.2084 10.2583C11.9751 10.7083 11.5084 11 11.0001 11C9.19172 11 7.75839 12.525 7.87506 14.325L8.30839 21.2083C8.40839 22.925 9.33339 24.3333 11.6334 24.3333Z"
                                    stroke="#a30133" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                                <path d="M14.75 12.6667H17.25" stroke="#a30133" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path
                                    d="M16 21C17.4916 21 18.7083 19.7833 18.7083 18.2917C18.7083 16.8 17.4916 15.5833 16 15.5833C14.5083 15.5833 13.2916 16.8 13.2916 18.2917C13.2916 19.7833 14.5083 21 16 21Z"
                                    stroke="#a30133" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </button>
                        <div id="croppedImage"></div>
                        @error('profile_photo')
                            <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-4 create-user-options">
                        <div class=" p-0 form-switch d-flex justify-content-between align-items-center">
                            <label class="form-check-label" for="auto_dispatch">Auto Dispatch</label>
                            <input class="form-check-input position-relative m-0 ml-3" type="checkbox" role="switch"
                                id="auto_dispatch" value="0" name="auto_dispatch">
                        </div>
                        <div class=" p-0 form-switch d-flex justify-content-between align-items-center">
                            <label class="form-check-label" for="is_integration">Integration</label>
                            <input class="form-check-input position-relative m-0 ml-3" id="is_integration" value="0"
                                name="is_integration" type="checkbox" role="switch" id="cv" name="cv">
                        </div>
                    </div>
                </div>



                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="text" id="account_number" name="account_number"
                                value="{{ old('account_number') }}" />
                            <legend>Account number</legend>
                            @error('account_number')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>

                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" />
                            <legend>Name</legend>

                        </fieldset>
                        @error('name')
                            <div class="text-danger top-100 d-block fs-896px position-absolute left-24px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <div class="modalSelectBox phoneNewBranch w-100 d-flex flex-row-reverse position-relative">
                            <input type="number" id="phone" name="phone" value="{{ old('phone') }}" />
                            <label class="customSelectLegend legendPhoneNumber positioned">Phone Number</label>
                            <select class="phoneNumberCreateClient select2 w-25">
                                <option></option>
                                <option value="1">+996</option>
                            </select>

                            @error('phone')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="text" name="email" id="email" value="{{ old('email') }}" />
                            <legend>Email</legend>
                            @error('email')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>
                </div>

                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="password" name="password" id="password" />
                            <legend>Password</legend>
                            @error('password')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="modalSelectBox countryNewBranch">
                            <label class="customSelectLegend left-24px">Country</label>
                            <select class="countryAddress select2" name="country_id">
                                <option></option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="text-danger top-100 d-block fs-896px position-absolute left-24px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <div class="modalSelectBox cityNewBranch">
                            <label class="customSelectLegend left-24px">City</label>
                            <select class="cityAddress select2" name="city_id">
                                <option></option>
                                @foreach ($all_cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <div class="text-danger top-100 d-block fs-896px position-absolute left-24px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="modalSelectBox currencySelect">
                            <label class="customSelectLegend left-24px">Currency</label>
                            <select class="currency select2" name="currency">
                                <option></option>
                                @foreach (App\Enum\Currency::values() as $key => $value)
                                    <option value="{{ $key }}" {{ old('currency') == $key ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                            @error('currency')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="number" id="defaultPreparationTime" name="default_prepartion_time"
                                value="{{ old('default_prepartion_time') }}" />
                            <legend>Default preparation time</legend>
                            @error('default_prepartion_time')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>

                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="number" id="minPreparationTime" name="min_prepartion_time"
                                value="{{ old('min_prepartion_time') }}" />
                            <legend>Min. preparation time</legend>
                            @error('min_prepartion_time')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>
                </div>

                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="number" id="partialPay" name="partial_pay"
                                value="{{ old('partial_pay') }}" />
                            <legend>Partial pay</legend>
                            <span>%</span>
                            @error('partial_pay')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>

                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="text" id="price_order" name="price_order"
                                value="{{ old('price_order') }}" />
                            <legend>Price Of Order</legend>
                            @error('price_order')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>
                </div>

                <div class="row gap-md-0 gap-3">
                    <div class="col-12">
                        <fieldset>
                            <textarea placeholder="Note" id="note" name="note" class="br-8px fs-112 border-b4 p-112 w-100">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </fieldset>
                    </div>
                </div>

                <p class="sectionTitle">Group</p>

                <div class="row gap-md-0 gap-3">
                    <div class="col-md-6 col-12">
                        <div class="modalSelectBox clientGroupCreateUser">
                            <label class="customSelectLegend left-24px">Client group</label>
                            <select class="clientGroup select2" name="client_group_id">
                                <option></option>
                                @foreach ($client_groups as $group)
                                    <option value="{{ $group->id }}"
                                        {{ old('client_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_group_id')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="modalSelectBox driverGroupCreateUser">
                            <label class="customSelectLegend left-24px">Driver group</label>
                            <select class="driverGroup select2" name="driver_group_id">
                                <option></option>
                                @foreach ($driver_groups as $group)
                                    <option value="{{ $group->id }}"
                                        {{ old('driver_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_group_id')
                                <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="itemListDivider"></div>

                <div id="integration-div" style="display: none">

                    <p class="sectionTitle mb-4">Integration Company</p>

                    <div class="modalSelectBox intergrationCompany">
                        <label class="customSelectLegend left-24px">Integration Company</label>
                        <select class="integrationBox select2" name="integration_id">
                            <option value=""></option>
                            @foreach ($integrations as $integration)
                                <option value="{{ $integration->id }}"
                                    {{ old('integration_id') == $integration->id ? 'selected' : '' }}>
                                    {{ $integration->name }}</option>
                            @endforeach
                        </select>
                        @error('integration_id')
                            <div class="text-danger top-100 d-block fs-896px position-absolute">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                    <div>
                        <button type="submit" id="save-client-btn" class="templateSaveBtn bg-red-a3">
                            Save
                        </button>
                    </div>
                </div>
            </form>

    </div>



    <!-- add image Modal in new client  -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold " id="photoModalLabel">Crop and Resize Your Photo</h5>
                    <svg data-bs-dismiss="modal" aria-label="Close" width="19.2px" height="19.2px" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <div class="crop-container">
                        <img id="uploadedImage" src="" alt="Image to crop">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="templatesActionBtns w-100 mt-4 d-flex justify-content-end align-items-center" dir="ltr">
                    <div>
                        <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" id="cropAndSaveButton" class="templateSaveBtn bg-red-a3">
                            Save changes
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


<script>
    $(document).ready(function() {

        $(".phoneNumberCreateClient").select2({
            placeholder: "Code",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        });
        $(".cityAddress").select2({
            placeholder: "City",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('select2:select', function() {
            $('.cityNewBranch .customSelectLegend').addClass('positioned');
        });
        $(".countryAddress").select2({
            placeholder: "Country",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('select2:select', function() {
            $('.countryNewBranch .customSelectLegend').addClass('positioned');
        });


        $(".integrationBox").select2({
            placeholder: "Intergration Company",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('select2:select', function() {
            $('.intergrationCompany .customSelectLegend').addClass('positioned');
        });




        $(".currency").select2({
            placeholder: "Currency",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('select2:select', function() {
            $('.currencySelect .customSelectLegend').addClass('positioned');
        });
        $(".clientGroup").select2({
            placeholder: "Client group",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('select2:select', function() {
            $('.clientGroupCreateUser .customSelectLegend').addClass('positioned');
        });
        $(".driverGroup").select2({
            placeholder: "Driver Group",
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        }).on('select2:select', function() {
            $('.driverGroupCreateUser .customSelectLegend').addClass('positioned');
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



        let cropper;

        // Open modal on file selection
        $(document).on("click", "#uploadButton", function(e) {
            e.stopPropagation();
            $("#fileInput").click();
        });

        $(document).on("change", "#fileInput", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                console.log("Selected file name:", file.name);
                reader.onload = function(e) {
                    $("#uploadedImage").attr("src", e.target.result);

                    // Show the modal (handled by Bootstrap)
                    $("#photoModal").modal("show");

                    // Initialize Cropper after the image is loaded in the modal
                    $("#photoModal").on("shown.bs.modal", function() {
                        // Destroy the previous cropper instance if it exists
                        if (cropper) {
                            cropper.destroy();
                        }

                        cropper = new Cropper($("#uploadedImage")[0], {
                            aspectRatio: 1, // Maintain square aspect ratio
                            viewMode: 1,
                            dragMode: "move",
                            autoCropArea: 0.8,
                            cropBoxResizable: true, // Allow resizing of the crop box
                            cropBoxMovable: true, // Allow moving the crop box
                            toggleDragModeOnDblclick: false,
                            background: false,
                        });
                        $(".crop-container").css("background-color",
                            "transparent");
                    });
                };
                reader.readAsDataURL(file);
            }
        });


        // Optional: Reset cropper when the modal is closed
        $("#photoModal").on("hidden.bs.modal", function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        $(document).on("click", "#cropAndSaveButton", function() {
            if (cropper) {
                console.log("Saving cropped image...");

                // Get the cropped image as a canvas
                const croppedCanvas = cropper.getCroppedCanvas({
                    width: 300, // Set desired width
                    height: 300 // Set desired height
                });

                // Convert the canvas to a blob and create a URL
                croppedCanvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    console.log("Cropped image URL:", url);

                    // Create an image element to display the cropped image
                    const img = document.createElement("img");
                    img.src = url;
                    img.style.borderRadius = "50%"; // Make the image circular
                    img.style.maxWidth = "300px"; // Limit the width of the image
                    img.style.height = "auto"; // Keep the aspect ratio

                    // Append the image to the body or a specific container
                    $("#croppedImage").html(img);

                    // Optionally, you can append it to a specific element
                    // $("#someContainer").append(img);

                    // Close the modal
                    $("#photoModal").modal("hide");

                    // Destroy the cropper instance to free up resources
                    cropper.destroy();
                });
            } else {
                console.log("Cropper is not initialized.");
            }
        });



        function updateCheckboxValues() {
            $('input[type="checkbox"]').each(function() {
                $(this).val(this.checked ? 1 : 0);
            });
        }

        $('#auto_dispatch').on('change', function(e) {
            e.preventDefault();
            console.log(454);

            updateCheckboxValues();
        });


        $('#is_integration').on('change', function(e) {
            e.preventDefault();
            console.log(454);

            updateCheckboxValues();
        });

    })
</script>
