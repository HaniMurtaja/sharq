<div class="modal fade clientModalPopup" id="addNewUserModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="rechargeModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">Add new user</h5>
                <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div id="uniqueTabGroup" class="tab-group">
                    <!-- Tab Buttons -->
                    <div class="tab-buttons" role="group">
                        <button type="button" class="btn active" data-tab="newUser">New user</button>
                        <button type="button" class="btn" data-tab="existingUser">Existing user</button>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content mt-2">
                        <!-- New user -->


                        <div class="tab-pane" data-type="newUser" style="display: block;">
                            <form class="gap-4" id="client-user-form">
                                @csrf
                                <p class="sectionTitle">Inforamtion</p>
                                <span class="visibilty-hidden"></span>

                                <div
                                    class="modalSelectBox userTemplateNewUser w-100 d-flex flex-row-reverse position-relative">
                                    <label for="template-name" class="customSelectLegend ">User template <span
                                            class="text-danger">*</span></label>
                                    <select class="userTemplate" id="" name="role">
                                        <option></option>
                                        @foreach ($templates as $template)
                                        <option value="{{ $template->name }}"> {{ $template->name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="error_userTemplate invalid-feedback pl-2">pp</span>
                                </div>
                                <span class="visibilty-hidden"></span>

                                <fieldset class="floating-label-input">
                                    <input type="text" name="first_name" required />
                                    <legend>First Name<span class="text-danger">*</span></legend>
                                    <span class="error_last_name invalid-feedback" id="first_name_error"></span>
                                </fieldset>

                                <fieldset class="floating-label-input">
                                    <input type="text" name="last_name" required />
                                    <legend> Last Name<span class="text-danger">*</span></legend>
                                    <span class="error_first_name invalid-feedback" id="last_name_error"></span>
                                </fieldset>


                                <fieldset class="floating-label-input">
                                    <input type="email" name="email" required />
                                    <legend>Email<span class="text-danger">*</span></legend>
                                    <span class="error_email invalid-feedback" id="email_error"></span>
                                </fieldset>
                                <fieldset class="floating-label-input passwordField">
                                    <input type="password" value="" id="passwordInput" name="password" required />
                                    <legend>Password<span class="text-danger">*</span></legend>
                                    {{-- <svg id="togglePassword" width="16px" height="16px" viewBox="0 0 20 20"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9.99999 7.76667C8.76421 7.76667 7.76666 8.76422 7.76666 10C7.76666 11.2358 8.76421 12.2333 9.99999 12.2333C11.2358 12.2333 12.2333 11.2358 12.2333 10C12.2333 8.76422 11.2358 7.76667 9.99999 7.76667ZM6.26666 10C6.26666 7.93579 7.93578 6.26667 9.99999 6.26667C12.0642 6.26667 13.7333 7.93579 13.7333 10C13.7333 12.0642 12.0642 13.7333 9.99999 13.7333C7.93578 13.7333 6.26666 12.0642 6.26666 10Z"
                                                fill="#949494"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M1.77582 7.4303C3.78507 4.27194 6.74035 2.35001 9.99999 2.35001C13.2595 2.35001 16.2146 4.27174 18.2239 7.42981M1.77582 7.4303C1.30563 8.16721 1.09583 9.10429 1.09583 9.99584C1.09583 10.8872 1.30553 11.824 1.77551 12.5609C3.78476 15.7195 6.74018 17.6417 9.99999 17.6417C13.2595 17.6417 16.2146 15.7199 18.2239 12.5619C18.6943 11.8249 18.9042 10.8876 18.9042 9.99584C18.9042 9.10449 18.6945 8.16763 18.2245 7.4308M9.99999 3.85001C7.37647 3.85001 4.84855 5.39454 3.04114 8.23588L3.04052 8.23687C2.76093 8.67488 2.59583 9.31258 2.59583 9.99584C2.59583 10.6791 2.76093 11.3168 3.04052 11.7548L3.04114 11.7558C4.84855 14.5971 7.37647 16.1417 9.99999 16.1417C12.6235 16.1417 15.1514 14.5971 16.9588 11.7558L16.9595 11.7548C17.2391 11.3168 17.4042 10.6791 17.4042 9.99584C17.4042 9.31258 17.2391 8.67488 16.9595 8.23687L16.9588 8.23588C15.1514 5.39454 12.6235 3.85001 9.99999 3.85001Z"
                                                fill="#949494"></path>
                                        </svg>
                                        <button type="button" class="generagtePass">Generate</button> --}}
                                    <span class="error_passwordi invalid-feedback" id="password_error"></span>
                                </fieldset>






                                <div
                                    class="modalSelectBox  phoneNewBranch w-100 d-flex flex-row-reverse position-relative">
                                    <input type="number" value="" name="phone" required />
                                    <label for="template-name"
                                        class="customSelectLegend legendPhoneNumber positioned">Phone Number
                                        <span class="text-danger">*</span></label>
                                    <select class="select2insidemodal" id="">
                                        <option></option>
                                        <option value="1">+996</option>

                                    </select>
                                    <span class="error_branch_phone invalid-feedback pl-2" id="phone_error"></span>


                                </div>



                                <fieldset class="floating-label-input">
                                    <input type="text" name="mac_address" required />
                                    <legend>Mac Address<span class="text-danger">*</span></legend>
                                    <span class="error_email invalid-feedback" id="mac_address_error"></span>
                                </fieldset>

                                {{-- <p class="sectionTitle">Access</p>
                                    <span class="visibilty-hidden"></span>

                                    <div
                                        class="modalSelectBox grandAccessNewUser w-100 d-flex flex-row-reverse position-relative">
                                        <label for="template-name" class="customSelectLegend positioned">Grand access
                                            to
                                            <span class="text-danger">*</span></label>
                                        <select class="grandAccess" id="accessSelect">

                                            <option value="1">clients</option>
                                            <option value="2">branches</option>
                                        </select>
                                        <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                    </div>


                                    <div id="clientsData" class="dataContent position-relative" style="display: none;">
                                        <div
                                            class="modalSelectBox clientsNewUser w-100 d-flex flex-row-reverse position-relative">

                                            <select class="clientsNewUserSelect clientsUserSelect" multiple
                                                id="clientsAccessSelect"">
                                                <option></option>
                                                <option value=" 1">Shrouk</option>
                                                <option value="2">Maddena</option>
                                            </select>
                                            <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                        </div>

                                        <div id="selectedClientsBox" class="selectedClientsBox"
                                            style="display: none; margin-top: 10px;">
                                            <h4>Selected Clients:</h4>
                                            <ul id="selectedClientsList" class="selectedClientsList">

                                            </ul>
                                        </div>
                                    </div>

                                    <div id="branchesData" class="dataContent" style="display: none;">
                                        <div
                                            class="modalSelectBox clientBranchNewUser w-100 d-flex flex-row-reverse position-relative">
                                            <label for="template-name" class="customSelectLegend positioned">Client
                                                <span class="text-danger">*</span></label>
                                            <select class="clientBranch" id="">

                                                <option value="1">Client 1</option>
                                                <option value="2">client 2</option>
                                            </select>
                                            <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                        </div>
                                        <div class="position-relative w-100">


                                            <div
                                                class="modalSelectBox clientsNewUser w-100 d-flex flex-row-reverse position-relative">

                                                <select class="clientsNewUserSelect clientsUserSelect" multiple
                                                    id="branchesAccessSelect">
                                                    <option></option>
                                                    <option value=" 1">Shrouk</option>
                                                    <option value="2">Maddena</option>
                                                </select>
                                                <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                            </div>

                                            <div id="selectedClientsBox" class="selectedClientsBox"
                                                style="display: none; margin-top: 10px;">
                                                <h4>Selected Clients:</h4>
                                                <ul id="selectedClientsList" class="selectedClientsList">

                                                </ul>
                                            </div>
                                        </div>
                                    </div> --}}

                                <!-- Buttons -->
                                <div class="templatesActionBtns mt-5 w-100 d-flex justify-content-end align-items-center"
                                    dir="ltr">
                                    <div>
                                        <button type="button" class="templateCancelBtn" aria-label="Close"
                                            data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button type="button" id="save-client-user-btn" class="templateSaveBtn bg-red-a3">
                                            Save changes
                                        </button>
                                    </div>
                                </div>



                            </form>

                        </div>

                        <!-- Existing user -->
                        <div class="tab-pane" data-type="existingUser" style="display: none;">

                            <form class="gap-4" id="client-exist-user-form">
                                @csrf
                                <p class="sectionTitle">Existing user</p>
                                <span class="visibilty-hidden"></span>

                                <div
                                    class="modalSelectBox userTemplateExistingUser w-100 d-flex flex-row-reverse position-relative">
                                    <label for="template-name" class="customSelectLegend ">User <span
                                            class="text-danger">*</span></label>
                                    <select class="userTemplateExisting" id="user_id" name="user_id">
                                        <option></option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}"> {{ $user->full_name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="error_grand_access invalid-feedback pl-2" id="user_id_error"></span>
                                </div>
                                <span class="visibilty-hidden"></span>

                                {{-- <p class="sectionTitle">Access</p>
                                    <span class="visibilty-hidden"></span>

                                    <div
                                        class="modalSelectBox grandAccessExistingUser w-100 d-flex flex-row-reverse position-relative">
                                        <label for="template-name" class="customSelectLegend positioned">Grand access to
                                            <span class="text-danger">*</span></label>
                                        <select class="grandAccessExisting" id="accessSelectExisiting">

                                            <option value="3">clients</option>
                                            <option value="4">branches</option>
                                        </select>
                                        <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                    </div>


                                    <div id="clientsDataExisting" class="dataContent position-relative"
                                        style="display: none;">
                                        <div
                                            class="modalSelectBox clientsExistingUser w-100 d-flex flex-row-reverse position-relative">

                                            <select class="clientsExistingUserSelect clientsUserSelect" multiple
                                                id="clientsExistingAccessSelect"">
                                                <option></option>
                                                <option value=" 1">Shrouk</option>
                                                <option value="2">Maddena</option>
                                            </select>
                                            <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                        </div>

                                        <div id="selectedClientsBoxExisting" class="selectedClientsBox"
                                            style="display: none; margin-top: 10px;">
                                            <h4>Selected Clients:</h4>
                                            <ul id="selectedClientsListExisting" class="selectedClientsList">

                                            </ul>
                                        </div>
                                    </div>

                                    <div id="branchesDataExisting" class="dataContent" style="display: none;">
                                        <div
                                            class="modalSelectBox clientBranchExistingUser w-100 d-flex flex-row-reverse position-relative">
                                            <label for="template-name" class="customSelectLegend positioned">Client <span
                                                    class="text-danger">*</span></label>
                                            <select class="clientBranchExisting" id="">

                                                <option value="1">Client 1</option>
                                                <option value="2">client 2</option>
                                            </select>
                                            <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                        </div>

                                        <div class="position-relative w-100">

                                            <div
                                                class="modalSelectBox clientsNewUser w-100 d-flex flex-row-reverse position-relative">

                                                <select class="clientsNewUserSelect clientsUserSelect" multiple
                                                    id="branchesAccessSelectExisitng"">
                                                    <option></option>
                                                    <option value=" 1">Shrouk</option>
                                                    <option value="2">Maddena</option>
                                                </select>
                                                <span class="error_grand_access invalid-feedback pl-2">pp</span>
                                            </div>

                                            <div id="selectedClientsBox" class="selectedClientsBox"
                                                style="display: none; margin-top: 10px;">
                                                <h4>Selected Clients:</h4>
                                                <ul id="selectedClientsList" class="selectedClientsList">

                                                </ul>
                                            </div>
                                        </div>


                                    </div> --}}

                                <!-- Buttons -->
                                <div class="templatesActionBtns mt-5 w-100 d-flex justify-content-end align-items-center"
                                    dir="ltr">
                                    <div>
                                        <button type="button" class="templateCancelBtn" aria-label="Close"
                                            data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button id="save-client-exist-user-btn" type="button" class="templateSaveBtn bg-red-a3">
                                            Save changes
                                        </button>
                                    </div>
                                </div>



                            </form>

                        </div>
                    </div>
                </div>






            </div>

        </div>
    </div>
</div>





<div class="modal fade clientModalPopup" id="editUserModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="editUserModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit user</h5>
                <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>
                </button>
            </div>

            <div class="modal-body">


                <form class="gap-4 edituserModal" id="edit-user-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <p class="sectionTitle">Profile</p>
                    <span class="visibilty-hidden"></span>

                    <div class="position-relative">
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
                                <path d="M14.75 12.6667H17.25" stroke="#a30133" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path
                                    d="M16 21C17.4916 21 18.7083 19.7833 18.7083 18.2917C18.7083 16.8 17.4916 15.5833 16 15.5833C14.5083 15.5833 13.2916 16.8 13.2916 18.2917C13.2916 19.7833 14.5083 21 16 21Z"
                                    stroke="#a30133" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                        </button>
                        <div id="croppedImage"></div>
                    </div>
                    <span class="visibilty-hidden"></span>
                    <p class="sectionTitle">Inforamtion</p>
                    <span class="visibilty-hidden"></span>

                    <div class="modalSelectBox userTemplateNewUser w-100 d-flex flex-row-reverse position-relative">
                        <label for="template-name" class="customSelectLegend ">User template <span
                                class="text-danger">*</span></label>
                        <select class="userTemplate" name="role">
                            <option></option>
                            @foreach ($templates as $template)
                            <option value="{{ $template->name }}"> {{ $template->name }} </option>
                            @endforeach
                        </select>
                        <span class="error_userTemplate invalid-feedback pl-2">pp</span>
                    </div>
                    <span class="visibilty-hidden"></span>
                    <input name="edit_user_client_id" hidden>
                    <fieldset class="floating-label-input">
                        <input type="text" value="" name="first_name" required />
                        <legend>First Name<span class="text-danger">*</span></legend>
                        <span class="error_first_name invalid-feedback">pp</span>
                    </fieldset>
                    <fieldset class="floating-label-input">
                        <input type="text" value="" name="last_name" required />
                        <legend>Last Name<span class="text-danger">*</span></legend>
                        <span class="error_last_name invalid-feedback">pp</span>
                    </fieldset>
                    <fieldset class="floating-label-input">
                        <input type="email" name="email" value="" required />
                        <legend>Email<span class="text-danger">*</span></legend>
                        <span class="error_email invalid-feedback">pp</span>
                    </fieldset>
                    <fieldset class="floating-label-input passwordField">
                        <input type="password" value="" name="password" id="passwordInput" required />
                        <legend>Password<span class="text-danger">*</span></legend>

                        <span class="error_passwordi invalid-feedback">pp</span>
                    </fieldset>
                    <div class="modalSelectBox  phoneNewBranch w-100 d-flex flex-row-reverse position-relative">
                        <input type="number" value="" name="phone" required />
                        <label for="template-name" class="customSelectLegend legendPhoneNumber positioned">Phone
                            Number
                            <span class="text-danger">*</span></label>
                        <select class="select2insidemodal" id="">
                            <option></option>
                            <option value="1">+996</option>

                        </select>
                        <span class="error_branch_phone invalid-feedback pl-2" id="phone_error"></span>


                    </div>


                    <fieldset class="floating-label-input">
                        <input type="text" name="mac_address" required />
                        <legend>Mac Address<span class="text-danger">*</span></legend>
                        <span class="error_email invalid-feedback" id="mac_address_error"></span>
                    </fieldset>

                    <!-- Buttons -->
                    <div class="templatesActionBtns mt-5 w-100 d-flex justify-content-between" dir="ltr">
                        <button id="client-user-delete-btn" type="button" class="templateDeleteBtn">
                            Delete
                        </button>
                        <div class="d-flex justify-content-end align-items-center">

                            <button type="button" class="templateCancelBtn me-2" aria-label="Close"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" id="edit-client-user-btn" class="templateSaveBtn bg-red-a3">
                                Save changes
                            </button>
                        </div>
                    </div>



                </form>








            </div>

        </div>
    </div>
</div>





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>

<script>
$(document).ready(function() {






    let rowIndex = 0;
    $('#myModal').on('shown.bs.modal', function() {
        $('#myInput').trigger('focus')
        $('.select2').select2();
    })
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });


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

    function addRow(repeaterId) {
        rowIndex++;

        var repeater = document.getElementById(repeaterId);
        var newRow = document.createElement('div');
        newRow.classList.add('row', 'mb-2');

        newRow.innerHTML = `
             <div class="col-md-12 d-flex align-items-center">
                <div class="form-group">
                    <select class="form-control select2" name="business_hours[${rowIndex}][day]" style="width: 100%;">
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
                <div class="form-group">
                    <select class="form-control select2" name="business_hours[${rowIndex}][start]" style="width: 100%;">
                        <option selected disabled>Start</option>
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
                <div class="form-group">
                    <select class="form-control select2" name="business_hours[${rowIndex}][end]" style="width: 100%;">
                        <option selected disabled>End</option>
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
                <button type="button" class="btn btn-add mx-1" style="color: green;">
                                    <!-- <i class="fas fa-plus" style="font-size: 0.8em;"></i> -->
                                    <img src="/new/src/assets/icons/add-dark.svg" alt="" >
                                </button>
                                <button type="button" class="btn btn-delete" style="color: red;">
                                    <!-- <i class="fas fa-trash" style="font-size: 0.8em;"></i> -->
                                    <img src="/new/src/assets/icons/delete.svg" alt="">
                                </button>
            </div>
                         `;

        repeater.appendChild(newRow);
        $('.select2').select2({
            allowClear: true
        });
        // Add event listeners for new buttons
        newRow.querySelector('.btn-add').addEventListener('click', function() {
            addRow(repeaterId);

            updateDeleteButtons(repeaterId);

        });
        newRow.querySelector('.btn-delete').addEventListener('click', function() {
            newRow.remove();
            updateDeleteButtons(repeaterId);
        });

        updateDeleteButtons(repeaterId);
    }

    document.querySelectorAll('.btn-add').forEach(function(button) {
        button.addEventListener('click', function() {
            var repeaterId = this.closest('[id^="repeater"]').id;
            addRow(repeaterId);
            updateDeleteButtons(repeaterId);
        });
    });
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = button.closest('.row');
            var repeaterId = row.closest('[id^="repeater"]').id;
            row.remove();
            updateDeleteButtons(repeaterId);
        });
    });

    // Tabs handleing
    // Tabs in modal add new user (new user and exisiting user)
        $(document).on('click', '#uniqueTabGroup .btn[data-tab]', function() {
            const targetType = $(this).data('tab');
            if ($(`#uniqueTabGroup .tab-pane[data-type="${targetType}"]`).is(':visible')) {
                $(this).removeClass('active');
                $(`#uniqueTabGroup .tab-pane[data-type="${targetType}"]`).hide();
            } else {

                $('#uniqueTabGroup .btn').removeClass('active');
                $('#uniqueTabGroup .tab-pane').hide();


                $(this).addClass('active');
                $(`#uniqueTabGroup .tab-pane[data-type="${targetType}"]`).show();
            }
        });

    // Select 2

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

    $(document).on('click', '#create-client-user', function() {
            // Create a new Bootstrap Modal instance
            var myModal = new bootstrap.Modal(document.getElementById('addNewUserModal'));
            myModal.show(); // Show the modal

            // Initialize Select2 for static elements inside the modal
            $('#addNewUserModal').on('shown.bs.modal', function() {



                initializeSelectModal(".select2insidemodal",
                    "#addNewUserModal .modal-body .phoneNewBranch", "Code");


                initializeSelectModal(".userTemplate",
                    "#addNewUserModal .modal-body .userTemplateNewUser", "User template");


                initializeSelectModal(".grandAccess",
                    "#addNewUserModal .modal-body .grandAccessNewUser", "");
                initializeSelectModal(".clientBranch",
                    "#addNewUserModal .modal-body .clientBranchNewUser",
                    "Client");
                initializeSelectModal(".branchesBranch",
                    "#addNewUserModal .modal-body .branchesBranchNewUser",
                    "Branches");
                $(".clientsNewUserSelect").select2({
                    placeholder: "Clients",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })

                initializeSelectModal(".userTemplateExisting",
                    "#addNewUserModal .modal-body .userTemplateExistingUser",
                    "User");
                initializeSelectModal(".grandAccessExisting",
                    "#addNewUserModal .modal-body .grandAccessExistingUser", "");
                initializeSelectModal(".clientBranchExisting",
                    "#addNewUserModal .modal-body .clientBranchExistingUser",
                    "Client");
                initializeSelectModal(".branchesBranchExisting",
                    "#addNewUserModal .modal-body .branchesBranchExistingUser",
                    "Branches");
                $(".clientsExistingUserSelect").select2({
                    placeholder: "Clients",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })
                $("#branchesAccessSelect").select2({
                    placeholder: "Branches",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })
                $("#branchesAccessSelectExisitng").select2({
                    placeholder: "Branches",
                    minimumResultsForSearch: Infinity,
                    allowClear: false,
                    width: '100%',
                    closeOnSelect: false,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var checkbox = $(
                            '<input type="checkbox" style="margin-right: 8px;" />'
                        );
                        var dataMulti = $(
                            '<span class="select2MultiOption">' +
                            data
                            .text + '</span>');
                        if (data.selected) {
                            checkbox.prop("checked", true);
                        }

                        var span = $(
                                '<span class="d-flex align-items-center">'
                            )
                            .append(
                                checkbox).append(dataMulti);

                        return span;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                })

            });
            $(".clientsUserSelect").on("select2:select", function(e) {
                $(".select2-search__field").attr("placeholder", "Search Clients");
            });

            $(".clientsUserSelect").on("select2:opening", function(e) {
                $(".select2-search__field").attr("placeholder", "Search Clients");
            });
        });

});
</script>
