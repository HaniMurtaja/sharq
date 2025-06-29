<div class="collapsible">

    <button type="button"
        class="flex items-center justify-between w-full px-3 py-3 mt-3 text-sm border rounded-md toggleButton border-gray1 text-black1 bg-gray8">
        <div class="flex items-center gap-4">
            <span class="w-0.5 h-6 bg-gray7"></span>
            <span>Add Vehicle <span>
        </div>
        <span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.92 8.94995L13.4 15.47C12.63 16.24 11.37 16.24 10.6 15.47L4.07996 8.94995"
                    stroke="#A30133" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </span>
    </button>
    <div class="mt-4 overflow-hidden text-sm transition-all duration-500 ease-in-out collapseContent max-h-0 md:text-base" >
        <div class="flex flex-col gap-3  rounded shadow-md  ">
            <form method="post" enctype="multipart/form-data" id="vehicle-form">
                @csrf
                <p>Vehicle image</p>
               
                    <div class="flex flex-col">
                        
                        <label
                            class="flex items-center justify-center w-20 h-20 bg-gray-200 bg-center bg-cover rounded-full"
                            for="file-upload-vehicle" id="upload-label-vehicle">
                            <input type="file" class="hidden" id="file-upload-vehicle" accept="image/*" name="vehicle_image" />
                            <svg height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"
                                id="user-icon-vehicle">
                                <path
                                    d="M24 8c-4.42 0-8 3.58-8 8 0 4.41 3.58 8 8 8s8-3.59 8-8c0-4.42-3.58-8-8-8zm0 20c-5.33 0-16 2.67-16 8v4h32v-4c0-5.33-10.67-8-16-8z"
                                    fill="gray" <!-- Set color here -->
                                    />
                                    <path d="M0 0h48v48h-48z" fill="none" />
                            </svg>
                        </label>
                        
                    </div>

                <p>Vehicle information</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="name" placeholder="Name"
                                name="name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mt-2 custom-select2-search">
                            <select class="w-full p-2 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2 custom-select-height" style="width: 100%;" name="type" id="type">
                                <option value="" selected="selected" disabled>Type</option>
                                @foreach ($settings->vehicle_types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>











                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="plate_number"
                                placeholder="Plate Number" name="plate_number">
                            <input name="vehicle_id" id="vehicle_id" hidden>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="vin_number"
                                placeholder="VIN Number" name="vin_number">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="make" placeholder="Make"
                                name="make">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="model" placeholder="Model"
                                name="model">
                        </div>
                    </div>
                </div>



                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control select2 year" style="width: 100%;" name="year"
                                id="yearSelect">
                                <option value="" selected="selected" disabled>Year</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <select class="form-control select2" style="width: 100%;" id="color"
                                name="color">
                                <option value="" selected="selected" disabled>Color</option>
                                <option value="Black">Black</option>
                                <option value="White">White</option>
                                <option value="Silver">Silver</option>
                                <option value="Gray">Gray</option>
                                <option value="Red">Red</option>
                                <option value="Blue">Blue</option>
                                <option value="Brown">Brown</option>
                                <option value="Green">Green</option>
                                <option value="Gold">Gold</option>
                                <option value="Yellow">Yellow</option>
                                <option value="Orange">Orange</option>
                                <option value="Purple">Purple</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">


                    <div class="col-md-6">


                        <div class="w-full file-upload-container">
                            <label class="flex flex-col w-full gap-2">
                                <span>ID card photo</span>
                                <div
                                    class="flex items-center justify-center w-full h-12 text-gray-600 border border-gray-800 border-dashed rounded-md cursor-pointer bg-gray4">
                                    <span>Upload Image</span>
                                </div>
                                <input type="file" class="hidden" accept="image/*" name="id_card_image_vehicle"
                                    id="imgInpIDCard">
                            </label>
                        </div>



                    </div>

                </div>

                <p>Milage info</p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="vehicle_milage"
                                placeholder="Vehicle Milage" name="vehicle_milage">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="last_service_milage"
                                placeholder="Last Service Milage" name="last_service_milage">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="due_service_milage"
                                placeholder="Due Service Milage" name="due_service_milage">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="service_milage_limit"
                                placeholder="Service Milage Limit" name="service_milage_limit">
                        </div>
                    </div>
                </div>



                
            </form>
        </div>
    </div>
</div>




