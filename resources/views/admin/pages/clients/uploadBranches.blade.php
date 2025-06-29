<div class="modal fade " id="uploadBranchModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="rechargeModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">Upload branches</h5>
                <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>

                </button>
            </div>
            {{-- <button class="NewButton">Edit</button> --}}
            {{-- <form id="uploadBranchesForm" enctype="multipart/form-data">
            <input type="file" name="branches_file" id="branches_file" accept=".csv" />
            <input type="hidden" id="client_id_for_uplaod_branches" name="client_id_for_uplaod_branches" />
            <button type="button" class="uploadBranchesButton" id="uploadBranchesButton">Upload Branches</button>
        </form> --}}
            <form id="uploadBranchesForm">
                <div class="modal-body d-flex flex-column gap-3">
                    <div class="mb-3">
                        <input type="hidden" id="client_id_for_uplaod_branches" name="client_id_for_uplaod_branches" />
                        <label for="formFileSm" class="sectionTitle mb-3">Choose file to upload branches (.csv /
                            .xlsx)</label>
                        <input class="form-control form-control-sm" id="formFileSm" type="file" name="branches_file"
                            accept=".csv, .xlsx">
                    </div>

                    <p class="sectionTitle">Types</p>

                    <div class="d-flex flex-row gx-3 align-item-center">
                        <div class="col-md-6 col-12 p-0">
                            <div
                                class="modalSelectBox typeUploadBranch w-100 d-flex flex-row-reverse position-relative">
                                <label for="template-name" class="customSelectLegend positioned">Type</label>
                                <select class="typeBranch" name="upload_type" id="upload_type">
                                    <option value="1">Public</option>
                                    <option value="2">Wasfety</option>


                                </select>
                                <span class="error_country invalid-feedback pl-2">pp</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 d-flex align-items-center">
                            <a href="{{ asset('templates/public_template.xlsx') }}"
                                class="sectionTitle text-primary publicSheet" download>
                                Download public sheet ⤓
                            </a>
                            <a href="{{ asset('templates/wasfety_template.xlsx') }}"
                                class="sectionTitle text-primary wasfetySheet d-none" download>
                                Download wasfety sheet ⤓
                            </a>
                        </div>

                    </div>

                    {{-- locations --}}
                    <p class="sectionTitle">Locations</p>
                    <div class="modalSelectBox countryUploadBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Country</label>
                        <select class="counryAddress" name="country" id="branch-country2">
                            <option></option>
                            <option value="1">Saudi Arabia</option>

                        </select>
                        <span class="error_country invalid-feedback pl-2">pp</span>
                    </div>

                    <div class="modalSelectBox cityUploadBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">City<span
                                class="text-danger">*</span></label>
                        <select class="cityAddress" id="branch-city2" name="city_id">
                            <option></option>
                            @foreach ($all_cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach

                        </select>
                        <span class="error_city_id invalid-feedback pl-2">pp</span>
                    </div>

                    <div class="modalSelectBox areaUploadBranch w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">Area</label>
                        <select class="areaAddress" name="area_id" id="branch-area2" disabled>
                            <option></option>
                            @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach

                        </select>
                        <span class="error_area_id invalid-feedback pl-2">pp</span>
                    </div>

                    <!-- Buttons -->
                    <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                        <div>
                            <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" id="uploadBranchesButton" class="templateSaveBtn bg-red-a3">
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

    function initClientUploadBranchesSelect() {
        $('#uploadBranchModal').on('shown.bs.modal', function() {
            initializeSelectModal(".counryAddress",
                "#uploadBranchModal .modal-body .countryUploadBranch", "Country");
            initializeSelectModal(".cityAddress",
                "#uploadBranchModal .modal-body .cityUploadBranch", "City");
            initializeSelectModal(".areaAddress",
                "#uploadBranchModal .modal-body .areaUploadBranch", "Area");



            initializeSelectModal(".typeBranch",
                "#uploadBranchModal .modal-body .typeUploadBranch", "Type");

            let typeSelect = $("#uploadBranchModal .modal-body .typeUploadBranch");

            if (typeSelect.length === 0) {
                console.error("Type select element not found!");
                return;
            }

            function toggleSheets() {
                let selectedText = typeSelect.find("option:selected").text().trim();

                if (selectedText === "Public") {
                    $(".publicSheet").removeClass("d-none");
                    $(".wasfetySheet").addClass("d-none");
                } else if (selectedText === "Wasfety") {
                    $(".wasfetySheet").removeClass("d-none");
                    $(".publicSheet").addClass("d-none");
                }
            }

            toggleSheets();

            typeSelect.on("change", function() {
                toggleSheets();
            });



        });
    }

    $(document).on('click', '#upload-client-branch', function() {

        $('#uploadBranchesForm')[0].reset();
        initClientUploadBranchesSelect();
        $('#branch-area2').prop('disabled', true);
    });
})
</script>
