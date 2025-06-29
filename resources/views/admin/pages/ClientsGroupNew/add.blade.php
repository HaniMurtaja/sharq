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
    Add Clients Group
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2">
        <section class="">

            <div class="flex flex-column pb-192 border-bottom justify-between md:flex-row gap-192 mb-4">
                <p id="tabDescription" class="text-black fs-192 fw-bold">
                    <a href="{{ route('ClientsGroupNew') }}">Clients Group</a>
                    <span class="fs-118 gray-94">»</span>
                    <span class="">Add Clients Group</span>
                </p>
            </div>



            <form action="{{ route('ClientsGroupNew.store') }}" method="POST"
                class="p-4 bg-white br-96 d-flex flex-column gap-4" id="client-form" enctype="multipart/form-data">
                @csrf
                <span class="visibility-hidden"></span>
                <input name="client_group_id" hidden="">
                <fieldset class="floating-label-input">
                    <input type="text" name="name" id="group_name" required="">
                    <legend>Group Name<span class="text-danger">*</span></legend>
                </fieldset>
                <span class="visibility-hidden"></span>
                <p class="sectionTitle">Details</p>
                <span class="visibility-hidden"></span>

                <div class="custom-fieldset" data-select2-id="select2-data-19861-f9jw">
                    <label for="template-name" class="custom-legend">
                        Calculation Method <span class="text-danger d-none">*</span>
                    </label>
                    <select class="form-control " required name="calculation_method" id="calculation_method" style="width: 100%;"
                      >
                        <option value="" selected="selected" disabled="">Calculation method
                        </option>
                        <option value="area_to_area">Area to Area</option>
                        <option value="per_area">Per Area</option>
                        <option value="city_to_city">City to City</option>
                        <option value="per_stop">Per Stop</option>
                        <option value="per_km">Per km</option>
                        <option value="formula">Formula</option>
                        <option value="flat_rate">Flat Rate</option>

                    </select>

                </div>
                <fieldset class="floating-label-input">
                    <input type="number" step="any" value="" id="default_delivery_fee" name="default_delivery_fee"
                        required="" >
                    <legend>Default delivery fee<span class="text-danger">*</span></legend>
                </fieldset>
                <fieldset class="floating-label-input">
                    <input type="number" step="any"  id="collection_amount" name="collection_amount" required="" >
                    <legend>Collection amount<span class="text-danger">*</span></legend>
                </fieldset>
                <div class="custom-fieldset" data-select2-id="select2-data-19873-uold">
                    <label for="template-name" class="custom-legend">
                        Service Type <span class="text-danger d-none">*</span>
                    </label>
                    <select
                        class="form-control "
                        style="width: 100%" id="service_type" name="service_type"
                      >
                        <option ></option>
                        <option value="Delivery" >Delivery</option>

                    </select>

                </div>

                <div id="calcMethod">
                    <p>Flat Rate</p>

                    <div class="row mt-2">

                        <div class="col-md-6">
                            <fieldset class="floating-label-input">
                                <input name="collection_amount" type="number" step="any"
                                    >
                                <legend>Collection Amount <span class="text-danger">*</span></legend>
                            </fieldset>
                        </div>

                    </div>
                </div>



                <!-- Buttons -->
                <div class="templatesActionBtns w-100 d-flex justify-content-between align-items-center" dir="ltr">

                    <div>

                        <button  id="btn-save-group" class="templateSaveBtn">Save </button>
                    </div>
                </div>


            </form>

        </section>
    </div>



    <!-- add image Modal in new client  -->
@endsection
