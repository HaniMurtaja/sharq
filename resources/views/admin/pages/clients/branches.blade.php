<div class="modal fade clientModalPopup" id="addNewBranchModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="rechargeModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">Add new branch</h5>
                <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>

                </button>
            </div>
            <form id="client-branch-form">
                @csrf
                <div class="modal-body">

                    <p class="sectionTitle">Inforamtion</p>
                    <span class="visibilty-hidden"></span>
                    <input hidden id="branch_client_id" name="branch_client_id">
                    <input hidden value="" name="branch_id">
                    <input hidden value="" name="user_branch_id">

                    <fieldset class="floating-label-input">
                        <input type="text" name="branch_name" value="" required />
                        <legend>Name<span class="text-danger">*</span></legend>
                        <span class="error_name invalid-feedback">pp</span>
                    </fieldset>


                    <div class="modalSelectBox  phoneNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <input type="number" value="" name="branch_phone" />
                        <label for="template-name" class="customSelectLegend legendPhoneNumber positioned">Phone Number
                        </label>
                        <select class="select2insidemodal" id="">
                            <option></option>
                            <option value="1">+996</option>

                        </select>
                        <span class="error_branch_phone invalid-feedback pl-2">pp</span>


                    </div>

                    <fieldset class="floating-label-input">
                        <input type="text" id="branch_email" name="branch_email" />
                        <legend>Email</legend>
                        <span class="error_branch_email invalid-feedback">pp</span>

                    </fieldset>
                    <fieldset class="floating-label-input">
                        <input type="password" id="branch_password" name="branch_password" />
                        <legend>Password</legend>
                        <span class="error_branch_password invalid-feedback">pp</span>
                    </fieldset>


                    <p class="sectionTitle">Group</p>
                    <span class="visibilty-hidden"></span>

                    <div class="modalSelectBox branchNameNewBranch w-100 d-flex flex-row-reverse position-relative">

                        <label for="template-name" class="customSelectLegend ">Branch group </label>
                        <select class="branchGroup" name="client_group_id">
                            <option></option>
                            @foreach ($all_branches as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach

                        </select>
                        <span class="error_client_group_id invalid-feedback pl-2">pp</span>

                    </div>

                    <div class="modalSelectBox drivergroupNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Driver group </label>
                        <select class="driverGroup" name="driver_group_id">
                            <option></option>
                            @foreach ($driver_groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <span class="error_driver_group_id invalid-feedback pl-2">pp</span>
                    </div>

                    <p class="sectionTitle">Address</p>
                    <span class="visibilty-hidden"></span>
                    <fieldset class="floating-label-input">
                        <input type="text" name="search-link" id="search-link" />
                        <legend>Search with link</legend>
                    </fieldset>
                    <span class="visibilty-hidden"></span>



                    <div wire:ignore id="formMap"></div>

                    <fieldset class="floating-label-input">
                        <input type="text" name="lat" id="lat_order_hidden" value="" required />
                        <legend>Lattitude<span class="text-danger">*</span></legend>
                        <span class="error_lat invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <fieldset class="floating-label-input">
                        <input type="text" value="" name="lng" id="long_order_hidden" required />
                        <legend>Longitude<span class="text-danger">*</span></legend>
                        <span class="error_lng invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <div class="modalSelectBox countryNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Country</label>
                        <select class="counryAddress" name="country" id="branch-country">
                            <option></option>
                            <option value="1">Saudi Arabia</option>

                        </select>
                        <span class="error_country invalid-feedback pl-2">pp</span>
                    </div>

                    <div class="modalSelectBox cityNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">City<span
                                class="text-danger">*</span></label>
                        <select class="cityAddress" id="branch-city" name="city_id">
                            <option></option>
                            @foreach ($all_cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach

                        </select>
                        <span class="error_city_id invalid-feedback pl-2">pp</span>
                    </div>

                    <div class="modalSelectBox areaNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Area</label>
                        <select class="areaAddress" name="area_id" id="branch-area" disabled>
                            <option></option>
                            @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach

                        </select>
                        <span class="error_area_id invalid-feedback pl-2">pp</span>
                    </div>

                    <fieldset class="floating-label-input">
                        <input type="text" name="street" />
                        <legend>Street</legend>
                        <span class="error_street invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <fieldset class="floating-label-input">
                        <input type="number" name="landmark" />
                        <legend>Landmark</legend>
                        <span class="error_landmark invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <fieldset class="floating-label-input">
                        <input type="number" name="building" />
                        <legend>Building</legend>
                        <span class="error_building invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <fieldset class="floating-label-input">
                        <input type="number" name="floor" />
                        <legend>Floor</legend>
                        <span class="error_floor invalid-feedback pl-2">pp</span>
                    </fieldset>
                    <fieldset class="floating-label-input">
                        <input type="number" name="apartment" />
                        <legend>Apartment</legend>
                        <span class="error_apartment invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <div class="modalSelectBox interIdNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="pickup_id" class="customSelectLegend positioned">Integration ID</label>

                        <select class="interId" name="pickup_id" id="pickup_id">
                            <option></option>
                            <option value="1" selected="selected">Inter Id</option>
                            <option value="2">Custom Id</option>

                        </select>
                        <span class="error_pickup_id invalid-feedback pl-2">pp</span>
                    </div>

                    <fieldset class="floating-label-input customTextarea">
                        <textarea type="number" name="discription" value="" placeholder="Description"></textarea>
                        <span class="error_discription invalid-feedback pl-2">pp</span>
                    </fieldset>


                    <fieldset id="custom_id_fieldset" class="floating-label-input invisible">
                        <input type="text" name="custom_id" required />
                        <legend>Custom ID<span class="text-danger d-none">*</span></legend>
                        <span class="error_apartment invalid-feedback pl-2">pp</span>
                    </fieldset>

                    <span class="visibilty-hidden"></span>
                    <p class="sectionTitle">Branch bussiness hours</p>
                    <span class="visibilty-hidden"></span>
                    <div class="branchBussinessHoursContainer d-flex flex-column">
                        <div class="branchBussinessHours">
                            <div
                                class="modalSelectBox dayHoursNewBranch w-100 d-flex flex-row-reverse position-relative">
                                <label for="template-name" class="customSelectLegend ">Day</label>
                                <select class="dayBussinessHours" name="business_hours[0][day]">
                                    <option></option>
                                    <option selected disabled>Day</option>
                                    <option value="Sunday">Sunday</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>

                                </select>
                            </div>
                            <div
                                class="modalSelectBox startHoursNewBranch w-100 d-flex flex-row-reverse position-relative">
                                <label for="template-name" class="customSelectLegend ">Start</label>
                                <select class="startBussinessHours" name="business_hours[0][start]">
                                    <option></option>
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        for ($j = 0; $j < 60; $j += 30) {
                                            $time = sprintf('%02d:%02d', $i, $j);
                                            echo "<option value=\"$time\">$time</option>";
                                        }
                                    }
                                    ?>


                                </select>
                            </div>
                            <div
                                class="modalSelectBox endHoursNewBranch w-100 d-flex flex-row-reverse position-relative">
                                <label for="template-name" class="customSelectLegend ">End</label>
                                <select class="endBussinessHours" name="business_hours[0][end]">
                                    <option></option>
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        for ($j = 0; $j < 60; $j += 30) {
                                            $time = sprintf('%02d:%02d', $i, $j);
                                            echo "<option value=\"$time\">$time</option>";
                                        }
                                    }
                                    ?>

                                </select>
                            </div>

                            <div class="action-btn d-flex justify-content-between align-items-center">
                                <button class="addBussinessHours">
                                    <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z"
                                            fill="#a30133"></path>
                                    </svg>
                                </button>
                                <button class="removeBussinessHours">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z"
                                            fill="#949494"></path>
                                        <path
                                            d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z"
                                            fill="#949494"></path>
                                        <path
                                            d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z"
                                            fill="#949494"></path>
                                        <path
                                            d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z"
                                            fill="#949494"></path>
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </div>


                    <!-- Buttons -->
                    <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                        <div>
                            <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" id="save-client-branch-btn" class="templateSaveBtn bg-red-a3">
                                Save changes
                            </button>
                        </div>
                    </div>

                </div>
            </form>


        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function initializeSelectModal(selector, dropdownParent, placeholder) {
        $(selector).select2({
            dropdownParent: $(dropdownParent),
            placeholder: placeholder,
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0,
        }).on('select2:select', function() {
            $(dropdownParent + ' .customSelectLegend').addClass('positioned');
        });
    }


    function initClientBranchesSelect() {
        $('#addNewBranchModal').on('shown.bs.modal', function() {
            initializeSelectModal(".select2insidemodal",
                "#addNewBranchModal .modal-body .phoneNewBranch", "Code");
            initializeSelectModal(".branchGroup",
                "#addNewBranchModal .modal-body .branchNameNewBranch", "Branch Group");
            initializeSelectModal(".driverGroup",
                "#addNewBranchModal .modal-body .driverGroupNewBranch", "Driver Group");
            initializeSelectModal(".counryAddress",
                "#addNewBranchModal .modal-body .countryNewBranch", "Country");
            initializeSelectModal(".cityAddress",
                "#addNewBranchModal .modal-body .cityNewBranch", "City");
            initializeSelectModal(".areaAddress",
                "#addNewBranchModal .modal-body .areaNewBranch", "Area");
            initializeSelectModal(".interId",
                "#addNewBranchModal .modal-body .interIdNewBranch", "ID Type");
            initializeSelectModal(".dayBussinessHours",
                "#addNewBranchModal .modal-body .dayHoursNewBranch", "Day");
            initializeSelectModal(".startBussinessHours",
                "#addNewBranchModal .modal-body .startHoursNewBranch", "Start");
            initializeSelectModal(".endBussinessHours",
                "#addNewBranchModal .modal-body .endHoursNewBranch", "End");


        });
    }


    $(document).on('click', '#create-client-branch', function() {

        var myModal = new bootstrap.Modal(document.getElementById('addNewBranchModal'));
        myModal.show();
        initClientBranchesSelect();
        initClientBranchesRepeater();

    });

    let rowCount = 0;
    let rowCountInBranchModal = 0;
    $(document).on('click', '.addBussinessHours', function(e) {

        e.preventDefault();
        rowCountInBranchModal++;


        const newRow = `
        <div class="branchBussinessHours">
                    <div class="modalSelectBox dayHoursNewBranch${rowCountInBranchModal} w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Day<span
                                class="text-danger">*</span></label>
                        <select class="dayBussinessHours${rowCountInBranchModal}" name="business_hours[${rowCountInBranchModal}][day]" >
                            <option></option>
                            <option value="Sunday">Sunday</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>

                        </select>
                    </div>
                    <div class="modalSelectBox startHoursNewBranch${rowCountInBranchModal} w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Start<span
                                class="text-danger">*</span></label>
                        <select class="startBussinessHours${rowCountInBranchModal}"   name="business_hours[${rowCountInBranchModal}][start]" >
                            <option ></option>
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                for ($j = 0; $j < 60; $j += 30) {
                                    $time = sprintf('%02d:%02d', $i, $j);
                                    echo "<option value=\"$time\">$time</option>";
                                }
                            }
                            ?>


                        </select>
                    </div>
                    <div class="modalSelectBox endHoursNewBranch${rowCountInBranchModal} w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">End<span
                                class="text-danger">*</span></label>
                        <select class="endBussinessHours${rowCountInBranchModal}"   name="business_hours[${rowCountInBranchModal}][end]" >
                            <option></option>
                            <?php
                            for ($i = 0; $i < 24; $i++) {
                                for ($j = 0; $j < 60; $j += 30) {
                                    $time = sprintf('%02d:%02d', $i, $j);
                                    echo "<option value=\"$time\">$time</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="action-btn d-flex justify-content-between align-items-center">
                        <button type="button" class="addBussinessHours">
                            <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z"
                                    fill="#F46624"></path>
                            </svg>
                        </button>
                        <button type="button" class="removeBussinessHours">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z"
                                    fill="#949494"></path>
                                <path
                                    d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z"
                                    fill="#949494"></path>
                                <path
                                    d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z"
                                    fill="#949494"></path>
                                <path
                                    d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z"
                                    fill="#949494"></path>
                            </svg>
                        </button>
                    </div>

                </div>`;
        $('.branchBussinessHoursContainer').append(newRow); // Append the new row
        initializeSelectModal(".dayBussinessHours" + rowCountInBranchModal,
            `#addNewBranchModal .modal-body .dayHoursNewBranch${rowCountInBranchModal}`,
            "Day");
        initializeSelectModal(".startBussinessHours" + rowCountInBranchModal,
            "#addNewBranchModal .modal-body .startHoursNewBranch" +
            rowCountInBranchModal, "Start");
        initializeSelectModal(".endBussinessHours" + rowCountInBranchModal,
            "#addNewBranchModal .modal-body .endHoursNewBranch" +
            rowCountInBranchModal,
            "End");


    });

    $(document).on('click', '.removeBussinessHours', function() {
        const branchRows = $('.branchBussinessHours'); // Select all rows
        if ($(this).closest('.branchBussinessHours')[0] !== branchRows.first()[0]) {
            // If the clicked row is not the first one, remove it
            $(this).closest('.branchBussinessHours').remove();
        }
    });

})
</script>
