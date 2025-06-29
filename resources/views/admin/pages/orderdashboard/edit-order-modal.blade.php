<div class="modal fade" id="editOrderModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="rechargeModalLabel" style="display: block;" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content pxy-256">
            <div class="modal-header border-0 p-0">
                <h5 class="modal-title fw-bold fs-6" id="exampleModalLabel">Editing the Order #23230287
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
            <form id="client-branch-form">
                <input type="hidden" name="_token" value="B3LpmmO4xxqk6S8eUtU7NQac05p0n3Bv7IcgJiJ9" autocomplete="off">


                <div class="modal-body px-0 pt-0 row gx-3 gy-3 mt-3 h-500px overflow-y-scroll overflow-x-hidden">

                    <div class="col-12 d-flex align-items-center  mb-96 gap-2">
                        <p class="sectionTitle m-0">Details</p>
                        <div class="flex-grow-1 border-bottom"></div>
                    </div>

                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="23230287"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">Order ID</legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="KSA_89555323"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">Client Order ID</legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="-"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">Delivery Order ID</legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <div class="modalSelectBox jobStatus w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Job Status</label>
                            <select name="actionType" id="edit-job-status">
                                <option value="1">pending</option>
                                <option value="2">at pickup</option>
                                <option value="3">accepted</option>
                                <option value="4">cancelled</option>
                                <option value="5">at dropoff</option>
                                <option value="6">failed</option>
                                <option value="7">completed</option>
                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>

                    <div class="col-12 position-relative customTextarea">
                    <legend class="fs-96 gray-94 fw-bolder">
                            Cancellation reason
                            </legend>
                        <textarea placeholder="" class="br-8px fs-112 border-b4 p-112 w-100 mb-3 pickup-instructions outline-none">-</textarea>
                        <span class="error_name invalid-feedback">pp</span>
                    </div>


                    <div class="col-6">
                        <div class="modalSelectBox typeOne w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Type</label>
                            <select name="actionType" id="edit-order-type-one">
                                <option value="1">On-Demmand</option>
                                <option value="2">Consolidated</option>
                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="modalSelectBox typeTwo w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Type</label>
                            <select name="actionType" id="edit-order-type-two">
                                <option value="1">public</option>
                                <option value="2">private</option>
                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="1.5 km"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">Distance</legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <div
                            class="sectionGlobalForm clientOrders border-ce br-96 px-128 py-2 d-flex p-19px flex-row align-items-center justify-content-between">
                            <p class="sectionTitle fs-128 black-1a">Auto Dispatch</p>
                            <div class=" p-0 form-switch d-flex justify-content-between align-items-center">
                                <input class="form-check-input ms-0 position-relative client-active-toggle"
                                    type="checkbox" role="switch" id="client_active_input" name="c" checked="checked">
                            </div>
                        </div>
                    </div>


                    <div class="col-12 d-flex align-items-center mt-5  mb-96 gap-2">
                        <p class="sectionTitle m-0">Payment</p>
                        <div class="flex-grow-1 border-bottom"></div>
                    </div>

                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="23230287"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">
                                Delivery Fees (ATE)
                            </legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="KSA_89555323"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">
                                Operator Percentage (ATE)
                            </legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="-"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">
                                Operator Fees (ATE)
                            </legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <div class="modalSelectBox paymentMethod w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Payment Method</label>
                            <select name="actionType" id="edit-order-payment-method">
                                <option value="1">Cash</option>
                                <option value="2">Credit</option>
                                <option value="3">CCM</option>
                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="modalSelectBox paidStatus w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Paid</label>
                            <select name="actionType" id="order-paid">
                                <option value="1">collect</option>
                                <option value="2">paid</option>
                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>
                    <div class="col-12 d-flex align-items-center mt-5  mb-96 gap-2">
                        <p class="sectionTitle m-0">Pickup</p>
                        <div class="flex-grow-1 border-bottom"></div>
                    </div>

                    <div class="col-6">
                        <div class="modalSelectBox clientsLocation w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Clients</label>
                            <select name="actionType" id="edit-clients">
                                <option value="1">Api locations</option>
                                <option value="2">"BK" 10 Percent - Ar Rabia</option>

                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="modalSelectBox branchsLocation w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend positioned">Branch</label>
                            <select name="actionType" id="edit-branchs">
                                <option value="1">Location-2333</option>
                                <option value="2">Location-4667</option>
                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>

                    <div class="col-12 position-relative customTextarea">
                    <legend class="fs-96 gray-94 fw-bolder">
                            Pickup instructions
                            </legend>
                        <textarea placeholder="" class="br-8px fs-112 border-b4 p-112 w-100 mb-3 pickup-instructions outline-none">-</textarea>
                        <span class="error_name invalid-feedback">pp</span>
                    </div>

                    <div class="col-12 d-flex align-items-center mt-5  mb-96 gap-2">
                        <p class="sectionTitle m-0">Drop off</p>
                        <div class="flex-grow-1 border-bottom"></div>
                    </div>

                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="-"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">
                                Customer name
                            </legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>

                    <div class="col-6">
                        <fieldset class="floating-label-input border-ce">
                            <input type="text" name="branch_name" class="fs-112 black-1a fw-bolder" value="-"
                                required="">
                            <legend class="fs-96 gray-94 fw-bolder">
                            Customer phone
                            </legend>
                            <span class="error_name invalid-feedback">pp</span>
                        </fieldset>
                    </div>

                    <div class="col-12 ">
                        <div class="mapContainer">
                        </div>
                    </div>

                    <div class="col-12 position-relative customTextarea">
                    <legend class="fs-96 gray-94 fw-bolder">
                            Dropoff instructions
                            </legend>
                        <textarea placeholder="" class="br-8px fs-112 border-b4 p-112 w-100 mb-3 pickup-instructions outline-none">-</textarea>
                        <span class="error_name invalid-feedback">pp</span>
                    </div>

                    <div class="col-12 d-flex align-items-center mt-5  mb-96 gap-2">
                        <p class="sectionTitle m-0">Operator</p>
                        <div class="flex-grow-1 border-bottom"></div>
                    </div>

                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="wh-32 rounded-circle">
                                <img src="{{ asset('new/src/assets/images/user.jpg') }}" class="rounded-circle w-100 h-100 obj-position-center object-cover" alt="" width="100" height="100">
                            </div>
                            <div>
                                <p class="fs-128 gray-1a fw-bold m-0 text-slide-wrapper">
                                   <span class="text-slide w-60"> Said Mohamed Abdelkader
                                   </span>
                                </p>
                                <h4 class="fs-96 fw-bold gray-94 m-0">
                                    +966549149702
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="modalSelectBox operatorsList  w-100 d-flex flex-row-reverse position-relative">
                            <label for="template-name" class="customSelectLegend d-none">Operators</label>
                            <select  name="actionType" id="edit-operators">
                            <option value="1">Abdelrahman Ramadan Ahmed Dodar - (17877)</option>
                            <option value="2">Ahmed Fawzy Mohamed Gouda - (17876)</option>
                            <option value="3">Mazen Elsayed Sadek ElNoby - (17854)</option>
                            <option value="4">Farid Ahmed Abdelkader Soliman - (1745)</option>
                            <option value="5">Mohamed Tarek Hassan Mostafa - (17901)</option>
                            <option value="6">Youssef Khaled Ibrahim Salah - (17888)</option>
                            <option value="7">Omar Hossam Eldin Mahmoud - (17865)</option>
                            <option value="8">Salma Ahmed Hany Elsharkawy - (17832)</option>
                            <option value="9">Nourhan Mohamed Samir Reda - (17821)</option>
                            <option value="10">Karim Mahmoud Abdelrahman Galal - (17790)</option>
                            <option value="11">Hassan Tamer Youssef Ali - (17780)</option>
                            <option value="12">Laila Sameh Ahmed Mostafa - (17745)</option>

                            </select>
                            <span class="error_country invalid-feedback pl-2">pp</span>
                        </div>
                    </div>









                </div>
            </form>


        </div>
    </div>
</div>