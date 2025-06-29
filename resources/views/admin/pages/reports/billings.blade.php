@extends('admin.layouts.app')
<!-- <link rel="stylesheet" href="{{ asset('new/src/css/globalLayout.css') }}" /> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/billing.css') }}" />


@section('content')
    <!-- operator Action Modal -->
    <div class="modal fade " id="addTransactionModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="addTransactionModal">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-0 my-2  border-0">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">
                        Add Transaction
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

                <form id="actionTypeForm">
                    <div class="modal-body px-0">
                        <div class="row gy-3">
                            <div class="col-md-6 col-12">
                                <div class="modalSelectBox action-type w-100 d-flex flex-row-reverse position-relative">
                                    <label for="template-name" class="customSelectLegend ">Action type</label>
                                    <select name="actionType" id="action-type">
                                        <option></option>
                                        <option value="1">Amount to company</option>
                                        <option value="2">Amount to driver</option>


                                    </select>
                                    <span class="error_country invalid-feedback pl-2">pp</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <fieldset class="floating-label-input">
                                    <input type="number" name="action_amount" value="" required="">
                                    <legend>Action amount<span class="text-danger">*</span></legend>
                                    <span class="error_name invalid-feedback">pp</span>
                                </fieldset>
                            </div>
                        </div>
                        <p class="gray-c8 fs-96 mt-3 mb-96 fw-semibold">
                            Action Reason
                        </p>
                        <textarea placeholder="Action Reason" class="br-8px fs-112 border-b4 p-112 w-100 mb-3"></textarea>





                        <!-- Buttons -->
                        <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                            <div>
                                <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="button" id="uploadBranchesButton" class="templateSaveBtn">
                                    Send and Save
                                </button>
                            </div>
                        </div>
                    </div>


                </form>


            </div>
        </div>
    </div>

    <!-- operator order Action Modal -->
    <div class="modal fade " id="orderActionModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="orderActionModal">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-0 my-2  border-0">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">
                        Order Action
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

                <form id="actionOrderForm">
                    <div class="modal-body px-0">
                        <div class="row gy-3">

                            <div class="col-md-6 col-12 billable">
                                <p class="gray-c8 fs-96 fw-semibold">
                                    Billable
                                </p>
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="form-check mb-0 d-flex align-items-end">
                                        <input class="form-check-input" type="radio" name="billable" id="yesBillable">
                                        <label for="yesBillable" class="fs-112 black-58 mb-0">Yes</label>
                                    </div>
                                    <div class="form-check mb-0 d-flex align-items-end">
                                        <input class="form-check-input" type="radio" name="billable" id="noBillable">
                                        <label for="noBillable" class="fs-112 black-58 mb-0">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div
                                    class="modalSelectBox orderResponsible w-100 d-flex flex-row-reverse position-relative">
                                    <label for="template-name" class="customSelectLegend ">Responsible</label>
                                    <select name="orderResponsible" id="order-responsible">
                                        <option></option>
                                        <option value="1">Shop</option>
                                        <option value="2">Driver</option>
                                        <option value="3">Customer</option>
                                        <option value="4">System</option>


                                    </select>
                                    <span class="error_country invalid-feedback pl-2">pp</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="modalSelectBox action-type w-100 d-flex flex-row-reverse position-relative">
                                    <label for="template-name" class="customSelectLegend ">Action type</label>
                                    <select name="actionTypeOrder" id="action-type-order">
                                        <option></option>
                                        <option value="1">Amount to company</option>
                                        <option value="2">Amount to driver</option>


                                    </select>
                                    <span class="error_country invalid-feedback pl-2">pp</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <fieldset class="floating-label-input">
                                    <input type="number" name="action_amount" value="" required="">
                                    <legend>Action amount<span class="text-danger">*</span></legend>
                                    <span class="error_name invalid-feedback">pp</span>
                                </fieldset>
                            </div>
                        </div>
                        <p class="gray-c8 fs-96 mt-3 mb-96 fw-semibold">
                            Action Reason
                        </p>
                        <textarea placeholder="Action Reason" class="br-8px fs-112 border-b4 p-112 w-100 mb-3"></textarea>





                        <!-- Buttons -->
                        <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center"
                            dir="ltr">
                            <div>
                                <button type="button" class="templateCancelBtn" aria-label="Close"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="button" id="uploadBranchesButton" class="templateSaveBtn">
                                    Send and Save
                                </button>
                            </div>
                        </div>
                    </div>


                </form>


            </div>
        </div>
    </div>

    <!-- Operator Detail Modal -->
    <div class="modal fade " id="billDetailModal" aria-labelledby="billDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2 border-0">
                    <h5 class="modal-title fw-bold">
                        Ahmed Abdalla - Billings
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
                <div class="modal-body px-0">
                    <!-- Header of modal -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="row d-flex px-192  w-100">

                            <div class="col-2 flex  br-64 gap-128 align-items-center  p-0 bg-white ">
                                <div class="flex items-center  border-ce rounded-5 w-8 h-8 rounded-full">
                                    <img src="{{ asset('new/src/assets/images/user.jpg') }}" width="100"
                                        height="100"
                                        class="w-100 h-100 object-cover obj-position-center rounded-full" />
                                </div>
                                <div class="flex flex-col">
                                    <p class="fs-128 gray-1a fw-bold m-0 text-slide-wrapper">
                                        <span class="text-slide">Client Name</span>
                                    </p>
                                    <h4 class="fs-96 fw-bold gray-94 m-0">
                                        +966537647815
                                    </h4>

                                </div>


                            </div>

                            <div class="col-2 flex  br-64 gap-128 align-items-center  p-0 bg-white ">
                                <div class="flex items-center  border-ce p-2 rounded-5">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.92548 10.4166C5.24965 10.5936 4.63768 10.9153 4.13533 11.3558C3.35535 12.0383 2.83984 13.0049 2.83984 14.1097C2.83984 14.2051 2.84381 14.2992 2.85156 14.3922V18.8894C2.85156 21.1205 4.9014 22.7494 7.18158 22.7494C9.46465 22.7494 11.5016 21.118 11.5016 18.8894V14.11C11.5016 13.0079 10.9878 12.03 10.2055 11.3455L10.2011 11.3418C9.41291 10.6627 8.34848 10.25 7.18158 10.25C6.74992 10.25 6.32724 10.3078 5.92548 10.4166ZM5.68979 12.1015C4.86105 12.5336 4.35156 13.3085 4.35156 14.11V14.3204C4.38231 14.5941 4.47257 14.8489 4.61085 15.0804C5.0383 15.7727 5.97611 16.2997 7.1698 16.2997C8.3683 16.2997 9.30252 15.7793 9.72786 15.0819C9.90158 14.7916 9.99982 14.4647 9.99982 14.1097C9.99982 13.4927 9.71501 12.917 9.20968 12.4959L9.20022 12.488C8.68845 12.0471 7.97289 11.7598 7.15979 11.7598C6.612 11.7598 6.11211 11.8854 5.68979 12.1015ZM9.99979 16.9398C9.22226 17.4984 8.22087 17.7997 7.1698 17.7997C6.12251 17.7997 5.12774 17.4967 4.3536 16.9452C4.4013 17.943 5.28485 18.8385 6.66777 19.0175C6.83205 19.0384 7.00349 19.0494 7.18158 19.0494C8.85601 19.0494 9.94973 18.0604 9.99979 16.9398ZM4.60511 19.855C5.16318 20.1938 5.81481 20.4161 6.49952 20.5052C6.72419 20.5349 6.95227 20.55 7.18158 20.55C8.10692 20.55 9.01017 20.3055 9.7495 19.8559C9.32142 20.6477 8.38086 21.2494 7.18158 21.2494C5.98028 21.2494 5.03545 20.6464 4.60511 19.855Z"
                                            fill="black"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.30405 4.30128C6.52398 4.26744 6.75575 4.25 7 4.25H16C16.2446 4.25 16.4468 4.2598 16.6267 4.28978C16.6389 4.29181 16.6511 4.29354 16.6634 4.29497C17.7851 4.42541 18.6691 4.89822 19.2747 5.59999C19.8817 6.30336 20.25 7.2847 20.25 8.5V9.19922H18.9202C18.1626 9.19922 17.4612 9.49736 16.9495 10.0192C16.6294 10.3338 16.3854 10.7266 16.2477 11.1635C16.2446 11.1736 16.2415 11.1836 16.2384 11.1937C16.1381 11.5248 16.0987 11.8806 16.133 12.2467C16.2623 13.7693 17.6304 14.8 19.04 14.8H20.25V15.5C20.25 16.8296 19.8101 17.8793 19.0947 18.5947C18.3793 19.3101 17.3296 19.75 16 19.75H13.5C13.0858 19.75 12.75 20.0858 12.75 20.5C12.75 20.9142 13.0858 21.25 13.5 21.25H16C17.6704 21.25 19.1207 20.6899 20.1553 19.6553C21.1899 18.6207 21.75 17.1704 21.75 15.5V14.6194C22.341 14.3324 22.7502 13.7321 22.7502 13.0292V10.9692C22.7502 10.2663 22.341 9.666 21.75 9.37903V8.5C21.75 6.9753 21.2833 5.63163 20.4103 4.62C19.5395 3.6109 18.305 2.97974 16.8545 2.80711C16.5608 2.75975 16.2697 2.75 16 2.75H7C6.68789 2.75 6.38313 2.77204 6.08633 2.81713C4.64812 2.9999 3.42737 3.63519 2.56796 4.64349C1.70803 5.65239 1.25 6.98713 1.25 8.5V10.5C1.25 10.9142 1.58579 11.25 2 11.25C2.41421 11.25 2.75 10.9142 2.75 10.5V8.5C2.75 7.29287 3.11197 6.31761 3.70955 5.61651C4.30559 4.91721 5.17606 4.44312 6.28339 4.30416L6.30405 4.30128ZM17.6774 11.6177C17.7403 11.4159 17.8533 11.2337 18.0039 11.0867L18.0173 11.0732C18.2451 10.8392 18.5609 10.7 18.92 10.7H20.993C21.149 10.7149 21.2502 10.8388 21.2502 10.9692V13.0292C21.2502 13.1624 21.1447 13.2888 20.983 13.2992H19.0402C18.2911 13.2992 17.6806 12.7519 17.6277 12.1169L17.6269 12.1081C17.6107 11.938 17.6294 11.7721 17.6774 11.6177Z"
                                            fill="black"></path>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <p class="fs-128 gray-1a fw-bold m-0">

                                        0
                                    </p>
                                    <h4 class="fs-96 fw-bold gray-94 m-0">
                                        Total balance
                                    </h4>

                                </div>


                            </div>

                            <div class="col-2 flex br-64 gap-128 align-items-center p-0 bg-white ">
                                <div class="flex items-center justify-center border-ce p-2 rounded-5">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M2.75 9C2.75 6.57397 3.2398 5.07086 4.15533 4.15533C5.07086 3.2398 6.57397 2.75 9 2.75H15C17.426 2.75 18.9291 3.2398 19.8447 4.15533C20.7602 5.07086 21.25 6.57397 21.25 9V15C21.25 17.426 20.7602 18.9291 19.8447 19.8447C18.9291 20.7602 17.426 21.25 15 21.25H9C6.57397 21.25 5.07086 20.7602 4.15533 19.8447C3.2398 18.9291 2.75 17.426 2.75 15V9ZM9 1.25C6.42603 1.25 4.42914 1.7602 3.09467 3.09467C1.7602 4.42914 1.25 6.42603 1.25 9V15C1.25 17.574 1.7602 19.5709 3.09467 20.9053C4.42914 22.2398 6.42603 22.75 9 22.75H15C17.574 22.75 19.5709 22.2398 20.9053 20.9053C22.2398 19.5709 22.75 17.574 22.75 15V9C22.75 6.42603 22.2398 4.42914 20.9053 3.09467C19.5709 1.7602 17.574 1.25 15 1.25H9ZM15.6406 9.2608C15.9335 8.96791 15.9335 8.49303 15.6406 8.20014C15.3477 7.90725 14.8729 7.90725 14.58 8.20014L8.03998 14.7401C7.74709 15.033 7.74709 15.5079 8.03998 15.8008C8.33288 16.0937 8.80775 16.0937 9.10064 15.8008L15.6406 9.2608ZM8.98001 8.66016C8.71491 8.66016 8.5 8.87507 8.5 9.14017C8.5 9.40524 8.71489 9.62015 8.98001 9.62015C9.24511 9.62015 9.45999 9.40526 9.45999 9.14017C9.45999 8.87505 9.24508 8.66016 8.98001 8.66016ZM7 9.14017C7 8.04664 7.88649 7.16016 8.98001 7.16016C10.0736 7.16016 10.96 8.04667 10.96 9.14017C10.96 10.2337 10.0735 11.1201 8.98001 11.1201C7.88651 11.1201 7 10.2337 7 9.14017ZM15.04 14.8608C15.04 14.5957 15.2549 14.3809 15.52 14.3809C15.7851 14.3809 16 14.5958 16 14.8608C16 15.1259 15.7851 15.3409 15.52 15.3409C15.2549 15.3409 15.04 15.126 15.04 14.8608ZM15.52 12.8809C14.4265 12.8809 13.54 13.7673 13.54 14.8608C13.54 15.9543 14.4265 16.8409 15.52 16.8409C16.6135 16.8409 17.5 15.9544 17.5 14.8608C17.5 13.7673 16.6135 12.8809 15.52 12.8809Z"
                                            fill="#292D32"></path>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <p class="fs-128 gray-1a fw-bold m-0">

                                        0
                                    </p>
                                    <h4 class="fs-96 fw-bold gray-94 m-0 whitespace-nowrap">
                                        Total balance after tax
                                    </h4>

                                </div>


                            </div>


                        </div>

                        <div class="d-flex gap-3 align-items-center">

                            <button
                                class="white-space-no border-0 outline-none bg-white fs-112 fw-semiBold black-58 d-flex gap-1">
                                <svg width="16px" height="16px" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.49998 14.1666V9.16663L5.83331 10.8333" stroke="#585858" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M7.5 9.16663L9.16667 10.8333" stroke="#585858" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M18.3334 8.33329V12.5C18.3334 16.6666 16.6667 18.3333 12.5 18.3333H7.50002C3.33335 18.3333 1.66669 16.6666 1.66669 12.5V7.49996C1.66669 3.33329 3.33335 1.66663 7.50002 1.66663H11.6667"
                                        stroke="#585858" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path
                                        d="M18.3334 8.33329H15C12.5 8.33329 11.6667 7.49996 11.6667 4.99996V1.66663L18.3334 8.33329Z"
                                        stroke="#585858" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                </svg>
                                Export to XLS
                            </button>
                            <button class="white-space-no fs-112 fw-semibold border-left ps-2 red-color "
                                data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                + Add Transaction
                            </button>
                        </div>


                    </div>

                    <!-- Table of data -->

                    <table id="detail-billing-table" class="w-full">
                        <thead class="">
                            <tr>
                                <th class="px-4 py-3 font-medium">Order time</th>
                                <th class="px-4 py-3 font-medium">ID</th>
                                <th class="px-4 py-3 font-medium">Delivery fees</th>
                                <th class="px-4 py-3 font-medium">Action amount</th>
                                <th class="px-4 py-3 font-medium">Paid</th>
                                <th class="px-4 py-3 font-medium">Collaction AMT</th>
                                <th class="px-4 py-3 font-medium">Balance</th>
                                <th class="px-4 py-3 font-medium">After tax</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Action</th>
                            </tr>


                        </thead>
                        <tbody>

                        </tbody>
                    </table>





                </div>


            </div>
        </div>
    </div>

    <!-- Cod Detail Modal -->
    <div class="modal fade " id="codDetailModal" aria-labelledby="codDetailModal" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2 border-0">
                    <h5 class="modal-title fw-bold">
                        Ahmed Abdalla - Billings
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
                <div class="modal-body px-0">
                    <!-- Header of modal -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="row d-flex px-192  w-100">

                            <div class="col-2 flex  br-64 gap-128 align-items-center  p-0 bg-white ">
                                <div class="flex items-center  border-ce rounded-5 w-8 h-8 rounded-full">
                                    <img src="{{ asset('new/src/assets/images/user.jpg') }}" width="100"
                                        height="100"
                                        class="w-100 h-100 object-cover obj-position-center rounded-full" />
                                </div>
                                <div class="flex flex-col">
                                    <p class="fs-128 gray-1a fw-bold m-0 text-slide-wrapper">
                                        <span class="text-slide">Client Name</span>
                                    </p>
                                    <h4 class="fs-96 fw-bold gray-94 m-0">
                                        +966537647815
                                    </h4>

                                </div>


                            </div>

                            <div class="col-2 flex  br-64 gap-128 align-items-center  p-0 bg-white ">
                                <div class="flex items-center  border-ce p-2 rounded-5">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M5.92548 10.4166C5.24965 10.5936 4.63768 10.9153 4.13533 11.3558C3.35535 12.0383 2.83984 13.0049 2.83984 14.1097C2.83984 14.2051 2.84381 14.2992 2.85156 14.3922V18.8894C2.85156 21.1205 4.9014 22.7494 7.18158 22.7494C9.46465 22.7494 11.5016 21.118 11.5016 18.8894V14.11C11.5016 13.0079 10.9878 12.03 10.2055 11.3455L10.2011 11.3418C9.41291 10.6627 8.34848 10.25 7.18158 10.25C6.74992 10.25 6.32724 10.3078 5.92548 10.4166ZM5.68979 12.1015C4.86105 12.5336 4.35156 13.3085 4.35156 14.11V14.3204C4.38231 14.5941 4.47257 14.8489 4.61085 15.0804C5.0383 15.7727 5.97611 16.2997 7.1698 16.2997C8.3683 16.2997 9.30252 15.7793 9.72786 15.0819C9.90158 14.7916 9.99982 14.4647 9.99982 14.1097C9.99982 13.4927 9.71501 12.917 9.20968 12.4959L9.20022 12.488C8.68845 12.0471 7.97289 11.7598 7.15979 11.7598C6.612 11.7598 6.11211 11.8854 5.68979 12.1015ZM9.99979 16.9398C9.22226 17.4984 8.22087 17.7997 7.1698 17.7997C6.12251 17.7997 5.12774 17.4967 4.3536 16.9452C4.4013 17.943 5.28485 18.8385 6.66777 19.0175C6.83205 19.0384 7.00349 19.0494 7.18158 19.0494C8.85601 19.0494 9.94973 18.0604 9.99979 16.9398ZM4.60511 19.855C5.16318 20.1938 5.81481 20.4161 6.49952 20.5052C6.72419 20.5349 6.95227 20.55 7.18158 20.55C8.10692 20.55 9.01017 20.3055 9.7495 19.8559C9.32142 20.6477 8.38086 21.2494 7.18158 21.2494C5.98028 21.2494 5.03545 20.6464 4.60511 19.855Z"
                                            fill="black"></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M6.30405 4.30128C6.52398 4.26744 6.75575 4.25 7 4.25H16C16.2446 4.25 16.4468 4.2598 16.6267 4.28978C16.6389 4.29181 16.6511 4.29354 16.6634 4.29497C17.7851 4.42541 18.6691 4.89822 19.2747 5.59999C19.8817 6.30336 20.25 7.2847 20.25 8.5V9.19922H18.9202C18.1626 9.19922 17.4612 9.49736 16.9495 10.0192C16.6294 10.3338 16.3854 10.7266 16.2477 11.1635C16.2446 11.1736 16.2415 11.1836 16.2384 11.1937C16.1381 11.5248 16.0987 11.8806 16.133 12.2467C16.2623 13.7693 17.6304 14.8 19.04 14.8H20.25V15.5C20.25 16.8296 19.8101 17.8793 19.0947 18.5947C18.3793 19.3101 17.3296 19.75 16 19.75H13.5C13.0858 19.75 12.75 20.0858 12.75 20.5C12.75 20.9142 13.0858 21.25 13.5 21.25H16C17.6704 21.25 19.1207 20.6899 20.1553 19.6553C21.1899 18.6207 21.75 17.1704 21.75 15.5V14.6194C22.341 14.3324 22.7502 13.7321 22.7502 13.0292V10.9692C22.7502 10.2663 22.341 9.666 21.75 9.37903V8.5C21.75 6.9753 21.2833 5.63163 20.4103 4.62C19.5395 3.6109 18.305 2.97974 16.8545 2.80711C16.5608 2.75975 16.2697 2.75 16 2.75H7C6.68789 2.75 6.38313 2.77204 6.08633 2.81713C4.64812 2.9999 3.42737 3.63519 2.56796 4.64349C1.70803 5.65239 1.25 6.98713 1.25 8.5V10.5C1.25 10.9142 1.58579 11.25 2 11.25C2.41421 11.25 2.75 10.9142 2.75 10.5V8.5C2.75 7.29287 3.11197 6.31761 3.70955 5.61651C4.30559 4.91721 5.17606 4.44312 6.28339 4.30416L6.30405 4.30128ZM17.6774 11.6177C17.7403 11.4159 17.8533 11.2337 18.0039 11.0867L18.0173 11.0732C18.2451 10.8392 18.5609 10.7 18.92 10.7H20.993C21.149 10.7149 21.2502 10.8388 21.2502 10.9692V13.0292C21.2502 13.1624 21.1447 13.2888 20.983 13.2992H19.0402C18.2911 13.2992 17.6806 12.7519 17.6277 12.1169L17.6269 12.1081C17.6107 11.938 17.6294 11.7721 17.6774 11.6177Z"
                                            fill="black"></path>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <p class="fs-128 gray-1a fw-bold m-0">

                                        0
                                    </p>
                                    <h4 class="fs-96 fw-bold gray-94 m-0">
                                        Total balance
                                    </h4>

                                </div>


                            </div>


                        </div>

                        <div class="d-flex gap-3 align-items-center">

                            <button class="white-space-no fs-112 fw-semibold red-color " data-bs-toggle="modal"
                                data-bs-target="#codPaymentModal">
                                + Add Payment
                            </button>
                        </div>


                    </div>

                    <!-- Table of data -->

                    <table id="cod-billing-table" class="w-full">
                        <thead class="">
                            <tr>
                                <th class="px-4 py-3 font-medium">Date / Time</th>
                                <th class="px-4 py-3 font-medium">Order count</th>
                                <th class="px-4 py-3 font-medium">Amount</th>
                                <th class="px-4 py-3 font-medium">Paid amount</th>
                                <th class="px-4 py-3 font-medium">Remaining amount</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Detail</th>
                            </tr>


                        </thead>
                        <tbody>

                        </tbody>
                    </table>





                </div>


            </div>
        </div>
    </div>

    <!-- Cod payment Modal -->
    <div class="modal fade " id="codPaymentModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="addTransactionModal">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-0 my-2  border-0">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">
                        Add Payment to ayman mohamed alhussain
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

                <form id="codPaymentForm">
                    <div class="modal-body px-0">
                        <div class="row gy-3">
                            <div class="col-md-6 col-12">
                                <div class="modalSelectBox action-type w-100 d-flex flex-row-reverse position-relative">
                                    <label for="template-name" class="customSelectLegend ">Action type</label>
                                    <select name="actionType" id="action-type-cod">
                                        <option></option>
                                        <option value="1">Amount to company</option>
                                        <option value="2">Amount to driver</option>


                                    </select>
                                    <span class="error_country invalid-feedback pl-2">pp</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <fieldset class="floating-label-input">
                                    <input type="number" name="action_amount" value="" required="">
                                    <legend>Action amount<span class="text-danger">*</span></legend>
                                    <span class="error_name invalid-feedback">pp</span>
                                </fieldset>
                            </div>
                        </div>
                        <p class="gray-c8 fs-96 mt-3 mb-96 fw-semibold">
                            Action Reason
                        </p>
                        <textarea placeholder="Action Reason" class="br-8px fs-112 border-b4 p-112 w-100 mb-3"></textarea>





                        <!-- Buttons -->
                        <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center"
                            dir="ltr">
                            <div>
                                <button type="button" class="templateCancelBtn" aria-label="Close"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="button" id="uploadBranchesButton" class="templateSaveBtn">
                                    Send and Save
                                </button>
                            </div>
                        </div>
                    </div>


                </form>


            </div>
        </div>
    </div>



    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">
        <!-- Tabs and Button -->
        <div class="flex flex-column justify-between md:flex-row gap-192">
            <p id="tabDescription" class="text-black fs-192 fw-bold">Operator Billings</p>
            <div class="flex mb-4 space-x-8 border-b operator_billings_tabs">
                <button
                    class="pxy-828 fs-96 gray-94 font-semibold border-b-2 text-mainColor border-mainColor operator_billings_tab"
                    data-tab="Operator Billings" id="operator_billings">
                    Operator Billings
                </button>
                <button id="cod_billings" class="pxy-828 fs-96 gray-94 text-gray-600 operator_billings_tab"
                    data-tab="COD Billings">
                    COD Billings
                </button>
            </div>


        </div>

        <!-- Operator Billings -->
        <div class="operator_billings_tab_content" data-tab="Operator Billings">
            <!-- Balance setions -->
            <div class="row d-flex gap-3 px-192 mb-176">
                <div class="col-lg-3 col-12 flex  br-64 gap-128 align-items-center  p-128 bg-white ">
                    <div class="flex items-center  border-ce p-2 rounded-5">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.92548 10.4166C5.24965 10.5936 4.63768 10.9153 4.13533 11.3558C3.35535 12.0383 2.83984 13.0049 2.83984 14.1097C2.83984 14.2051 2.84381 14.2992 2.85156 14.3922V18.8894C2.85156 21.1205 4.9014 22.7494 7.18158 22.7494C9.46465 22.7494 11.5016 21.118 11.5016 18.8894V14.11C11.5016 13.0079 10.9878 12.03 10.2055 11.3455L10.2011 11.3418C9.41291 10.6627 8.34848 10.25 7.18158 10.25C6.74992 10.25 6.32724 10.3078 5.92548 10.4166ZM5.68979 12.1015C4.86105 12.5336 4.35156 13.3085 4.35156 14.11V14.3204C4.38231 14.5941 4.47257 14.8489 4.61085 15.0804C5.0383 15.7727 5.97611 16.2997 7.1698 16.2997C8.3683 16.2997 9.30252 15.7793 9.72786 15.0819C9.90158 14.7916 9.99982 14.4647 9.99982 14.1097C9.99982 13.4927 9.71501 12.917 9.20968 12.4959L9.20022 12.488C8.68845 12.0471 7.97289 11.7598 7.15979 11.7598C6.612 11.7598 6.11211 11.8854 5.68979 12.1015ZM9.99979 16.9398C9.22226 17.4984 8.22087 17.7997 7.1698 17.7997C6.12251 17.7997 5.12774 17.4967 4.3536 16.9452C4.4013 17.943 5.28485 18.8385 6.66777 19.0175C6.83205 19.0384 7.00349 19.0494 7.18158 19.0494C8.85601 19.0494 9.94973 18.0604 9.99979 16.9398ZM4.60511 19.855C5.16318 20.1938 5.81481 20.4161 6.49952 20.5052C6.72419 20.5349 6.95227 20.55 7.18158 20.55C8.10692 20.55 9.01017 20.3055 9.7495 19.8559C9.32142 20.6477 8.38086 21.2494 7.18158 21.2494C5.98028 21.2494 5.03545 20.6464 4.60511 19.855Z"
                                fill="black"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.30405 4.30128C6.52398 4.26744 6.75575 4.25 7 4.25H16C16.2446 4.25 16.4468 4.2598 16.6267 4.28978C16.6389 4.29181 16.6511 4.29354 16.6634 4.29497C17.7851 4.42541 18.6691 4.89822 19.2747 5.59999C19.8817 6.30336 20.25 7.2847 20.25 8.5V9.19922H18.9202C18.1626 9.19922 17.4612 9.49736 16.9495 10.0192C16.6294 10.3338 16.3854 10.7266 16.2477 11.1635C16.2446 11.1736 16.2415 11.1836 16.2384 11.1937C16.1381 11.5248 16.0987 11.8806 16.133 12.2467C16.2623 13.7693 17.6304 14.8 19.04 14.8H20.25V15.5C20.25 16.8296 19.8101 17.8793 19.0947 18.5947C18.3793 19.3101 17.3296 19.75 16 19.75H13.5C13.0858 19.75 12.75 20.0858 12.75 20.5C12.75 20.9142 13.0858 21.25 13.5 21.25H16C17.6704 21.25 19.1207 20.6899 20.1553 19.6553C21.1899 18.6207 21.75 17.1704 21.75 15.5V14.6194C22.341 14.3324 22.7502 13.7321 22.7502 13.0292V10.9692C22.7502 10.2663 22.341 9.666 21.75 9.37903V8.5C21.75 6.9753 21.2833 5.63163 20.4103 4.62C19.5395 3.6109 18.305 2.97974 16.8545 2.80711C16.5608 2.75975 16.2697 2.75 16 2.75H7C6.68789 2.75 6.38313 2.77204 6.08633 2.81713C4.64812 2.9999 3.42737 3.63519 2.56796 4.64349C1.70803 5.65239 1.25 6.98713 1.25 8.5V10.5C1.25 10.9142 1.58579 11.25 2 11.25C2.41421 11.25 2.75 10.9142 2.75 10.5V8.5C2.75 7.29287 3.11197 6.31761 3.70955 5.61651C4.30559 4.91721 5.17606 4.44312 6.28339 4.30416L6.30405 4.30128ZM17.6774 11.6177C17.7403 11.4159 17.8533 11.2337 18.0039 11.0867L18.0173 11.0732C18.2451 10.8392 18.5609 10.7 18.92 10.7H20.993C21.149 10.7149 21.2502 10.8388 21.2502 10.9692V13.0292C21.2502 13.1624 21.1447 13.2888 20.983 13.2992H19.0402C18.2911 13.2992 17.6806 12.7519 17.6277 12.1169L17.6269 12.1081C17.6107 11.938 17.6294 11.7721 17.6774 11.6177Z"
                                fill="black"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">

                            {{ $total_balance }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Total balance
                        </h4>

                    </div>


                </div>

                <div class="col-lg-3 col-12 flex br-64 gap-128 align-items-center p-128 bg-white ">
                    <div class="flex items-center justify-center border-ce p-2 rounded-5">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.75 9C2.75 6.57397 3.2398 5.07086 4.15533 4.15533C5.07086 3.2398 6.57397 2.75 9 2.75H15C17.426 2.75 18.9291 3.2398 19.8447 4.15533C20.7602 5.07086 21.25 6.57397 21.25 9V15C21.25 17.426 20.7602 18.9291 19.8447 19.8447C18.9291 20.7602 17.426 21.25 15 21.25H9C6.57397 21.25 5.07086 20.7602 4.15533 19.8447C3.2398 18.9291 2.75 17.426 2.75 15V9ZM9 1.25C6.42603 1.25 4.42914 1.7602 3.09467 3.09467C1.7602 4.42914 1.25 6.42603 1.25 9V15C1.25 17.574 1.7602 19.5709 3.09467 20.9053C4.42914 22.2398 6.42603 22.75 9 22.75H15C17.574 22.75 19.5709 22.2398 20.9053 20.9053C22.2398 19.5709 22.75 17.574 22.75 15V9C22.75 6.42603 22.2398 4.42914 20.9053 3.09467C19.5709 1.7602 17.574 1.25 15 1.25H9ZM15.6406 9.2608C15.9335 8.96791 15.9335 8.49303 15.6406 8.20014C15.3477 7.90725 14.8729 7.90725 14.58 8.20014L8.03998 14.7401C7.74709 15.033 7.74709 15.5079 8.03998 15.8008C8.33288 16.0937 8.80775 16.0937 9.10064 15.8008L15.6406 9.2608ZM8.98001 8.66016C8.71491 8.66016 8.5 8.87507 8.5 9.14017C8.5 9.40524 8.71489 9.62015 8.98001 9.62015C9.24511 9.62015 9.45999 9.40526 9.45999 9.14017C9.45999 8.87505 9.24508 8.66016 8.98001 8.66016ZM7 9.14017C7 8.04664 7.88649 7.16016 8.98001 7.16016C10.0736 7.16016 10.96 8.04667 10.96 9.14017C10.96 10.2337 10.0735 11.1201 8.98001 11.1201C7.88651 11.1201 7 10.2337 7 9.14017ZM15.04 14.8608C15.04 14.5957 15.2549 14.3809 15.52 14.3809C15.7851 14.3809 16 14.5958 16 14.8608C16 15.1259 15.7851 15.3409 15.52 15.3409C15.2549 15.3409 15.04 15.126 15.04 14.8608ZM15.52 12.8809C14.4265 12.8809 13.54 13.7673 13.54 14.8608C13.54 15.9543 14.4265 16.8409 15.52 16.8409C16.6135 16.8409 17.5 15.9544 17.5 14.8608C17.5 13.7673 16.6135 12.8809 15.52 12.8809Z"
                                fill="#292D32"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">

                            {{ $total_balance_after_tax }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Total balance after tax
                        </h4>

                    </div>


                </div>


            </div>

            <div class="p-4 bg-white br-96 d-flex flex-column gap-lg-3">
                <!-- Navigation Tabs -->
                <div class="flex flex-col py-2 position-absolute billingText">
                    <p class="gray-94 fs-112 fw-semiBold m-0 h-40">
                        Billings
                    </p>

                </div>
                <!-- Table -->
                <div class="w-full overflow-x-auto position-relative">
                    <button
                        class="border-0 outline-none bg-white fs-112 fw-semiBold black-58 d-flex gap-1 position-absolute exportBtn">
                        <svg width="16px" height="16px" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.49998 14.1666V9.16663L5.83331 10.8333" stroke="#585858" stroke-width="1.2"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.5 9.16663L9.16667 10.8333" stroke="#585858" stroke-width="1.2"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M18.3334 8.33329V12.5C18.3334 16.6666 16.6667 18.3333 12.5 18.3333H7.50002C3.33335 18.3333 1.66669 16.6666 1.66669 12.5V7.49996C1.66669 3.33329 3.33335 1.66663 7.50002 1.66663H11.6667"
                                stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path
                                d="M18.3334 8.33329H15C12.5 8.33329 11.6667 7.49996 11.6667 4.99996V1.66663L18.3334 8.33329Z"
                                stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                        Export to XLS
                    </button>
                    <table id="operator-billings-table" class="w-full">
                        <thead class="">
                            <tr>
                                <th></th>
                                <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                                    Driver
                                </th>
                                <th class="px-4 py-3 font-medium">Orders Count</th>
                                <th class="px-4 py-3 font-medium">Service fees</th>
                                <th class="px-4 py-3 font-medium">Operator fees</th>
                                <th class="px-4 py-3 font-medium">Transactions</th>
                                <th class="px-4 py-3 font-medium">Balance</th>
                                <th class="px-4 py-3 font-medium">After tax</th>
                                <th class="px-4 py-3 font-medium">Details</th>
                                <th class="px-4 py-3 font-medium">Action</th>
                            </tr>


                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- COD Billings -->
        <div class="hidden operator_billings_tab_content" data-tab="COD Billings">
            <!-- Balance setions -->
            <div class="row d-flex gap-3 px-192 mb-176">
                <div class="col-lg-3 col-12 flex  br-64 gap-128 align-items-center  p-128 bg-white ">
                    <div class="flex items-center  border-ce p-2 rounded-5">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M5.92548 10.4166C5.24965 10.5936 4.63768 10.9153 4.13533 11.3558C3.35535 12.0383 2.83984 13.0049 2.83984 14.1097C2.83984 14.2051 2.84381 14.2992 2.85156 14.3922V18.8894C2.85156 21.1205 4.9014 22.7494 7.18158 22.7494C9.46465 22.7494 11.5016 21.118 11.5016 18.8894V14.11C11.5016 13.0079 10.9878 12.03 10.2055 11.3455L10.2011 11.3418C9.41291 10.6627 8.34848 10.25 7.18158 10.25C6.74992 10.25 6.32724 10.3078 5.92548 10.4166ZM5.68979 12.1015C4.86105 12.5336 4.35156 13.3085 4.35156 14.11V14.3204C4.38231 14.5941 4.47257 14.8489 4.61085 15.0804C5.0383 15.7727 5.97611 16.2997 7.1698 16.2997C8.3683 16.2997 9.30252 15.7793 9.72786 15.0819C9.90158 14.7916 9.99982 14.4647 9.99982 14.1097C9.99982 13.4927 9.71501 12.917 9.20968 12.4959L9.20022 12.488C8.68845 12.0471 7.97289 11.7598 7.15979 11.7598C6.612 11.7598 6.11211 11.8854 5.68979 12.1015ZM9.99979 16.9398C9.22226 17.4984 8.22087 17.7997 7.1698 17.7997C6.12251 17.7997 5.12774 17.4967 4.3536 16.9452C4.4013 17.943 5.28485 18.8385 6.66777 19.0175C6.83205 19.0384 7.00349 19.0494 7.18158 19.0494C8.85601 19.0494 9.94973 18.0604 9.99979 16.9398ZM4.60511 19.855C5.16318 20.1938 5.81481 20.4161 6.49952 20.5052C6.72419 20.5349 6.95227 20.55 7.18158 20.55C8.10692 20.55 9.01017 20.3055 9.7495 19.8559C9.32142 20.6477 8.38086 21.2494 7.18158 21.2494C5.98028 21.2494 5.03545 20.6464 4.60511 19.855Z"
                                fill="black"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.30405 4.30128C6.52398 4.26744 6.75575 4.25 7 4.25H16C16.2446 4.25 16.4468 4.2598 16.6267 4.28978C16.6389 4.29181 16.6511 4.29354 16.6634 4.29497C17.7851 4.42541 18.6691 4.89822 19.2747 5.59999C19.8817 6.30336 20.25 7.2847 20.25 8.5V9.19922H18.9202C18.1626 9.19922 17.4612 9.49736 16.9495 10.0192C16.6294 10.3338 16.3854 10.7266 16.2477 11.1635C16.2446 11.1736 16.2415 11.1836 16.2384 11.1937C16.1381 11.5248 16.0987 11.8806 16.133 12.2467C16.2623 13.7693 17.6304 14.8 19.04 14.8H20.25V15.5C20.25 16.8296 19.8101 17.8793 19.0947 18.5947C18.3793 19.3101 17.3296 19.75 16 19.75H13.5C13.0858 19.75 12.75 20.0858 12.75 20.5C12.75 20.9142 13.0858 21.25 13.5 21.25H16C17.6704 21.25 19.1207 20.6899 20.1553 19.6553C21.1899 18.6207 21.75 17.1704 21.75 15.5V14.6194C22.341 14.3324 22.7502 13.7321 22.7502 13.0292V10.9692C22.7502 10.2663 22.341 9.666 21.75 9.37903V8.5C21.75 6.9753 21.2833 5.63163 20.4103 4.62C19.5395 3.6109 18.305 2.97974 16.8545 2.80711C16.5608 2.75975 16.2697 2.75 16 2.75H7C6.68789 2.75 6.38313 2.77204 6.08633 2.81713C4.64812 2.9999 3.42737 3.63519 2.56796 4.64349C1.70803 5.65239 1.25 6.98713 1.25 8.5V10.5C1.25 10.9142 1.58579 11.25 2 11.25C2.41421 11.25 2.75 10.9142 2.75 10.5V8.5C2.75 7.29287 3.11197 6.31761 3.70955 5.61651C4.30559 4.91721 5.17606 4.44312 6.28339 4.30416L6.30405 4.30128ZM17.6774 11.6177C17.7403 11.4159 17.8533 11.2337 18.0039 11.0867L18.0173 11.0732C18.2451 10.8392 18.5609 10.7 18.92 10.7H20.993C21.149 10.7149 21.2502 10.8388 21.2502 10.9692V13.0292C21.2502 13.1624 21.1447 13.2888 20.983 13.2992H19.0402C18.2911 13.2992 17.6806 12.7519 17.6277 12.1169L17.6269 12.1081C17.6107 11.938 17.6294 11.7721 17.6774 11.6177Z"
                                fill="black"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">

                            {{ $total_balance }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Total balance
                        </h4>

                    </div>


                </div>


            </div>

            <div class="p-4 bg-white br-96 d-flex flex-column gap-3">
                <!-- Navigation Tabs -->
                <div class="flex flex-col py-2 position-absolute billingText">
                    <p class="gray-94 fs-112 fw-semiBold m-0 h-40 ">
                        Billings
                    </p>

                </div>
                <!-- Table -->
                <div class="w-full overflow-x-auto position-relative">

                    <table id="cod-billings-table" class="w-full">
                        <thead class="">
                            <tr>
                                <th></th>
                                <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                                    Driver
                                </th>
                                <th class="px-4 py-3 font-medium">Balance</th>
                                <th class="px-4 py-3 font-medium">Details</th>

                            </tr>


                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    var id = 123; // Ensure `id` is globally available

    $(document).ready(function() {
        console.log('begin');


        $(".operator_billings_tab").click(function() {
            let selectedTab = $(this).data("tab");
            console.log(selectedTab)
            if (selectedTab === "Operator Billings") {
                $("#tabDescription").text("Operator Billings");
            } else if (selectedTab === "COD Billings") {
                $("#tabDescription").text("COD Billings");
            }
        });


        $("#operator-billings-table").DataTable({
            processing: true,
            serverSide: false, // Disable server-side for dummy data
            data: [{
                    img: '',
                    driver: "Mohamed Abdelhamid",
                    phone: "+1234567890",
                    id: "12345",
                    order_count: 10,
                    service_fees: "$50",
                    operator_fees: "$20",
                    transactions: "5",
                    balance: "$200",
                    after_tax: "$180",
                    details: "",
                    action: "",
                },

            ],
            columns: [{
                    data: "img",
                    title: "",
                    orderable: false,
                    render: function(data, type, row) {
                        return `<img src="{{ asset('new/src/assets/images/user.jpg') }}"
                            alt="No Image" class="w-8 h-8 rounded-full object-cover">`;
                    }
                },
                {
                    data: "driver",
                    title: "Driver",
                    render: function(data, type, row) {
                        return `<div">
                                <p class="fs-128 black-58 mb-1 text-slide-wrapper">
                                <span class="text-slide">${row.driver}</span>
                                </p>
                                <p class="fs-96 gray-94 m-0">${row.phone}</p>
                            </div>`;
                    }
                },
                {
                    data: "id",
                    title: "Orders Count",
                    type: "num"
                },
                {
                    data: "order_count",
                    title: "Service Fees",
                    type: "num"
                },
                {
                    data: "service_fees",
                    title: "Operator Fees",
                    type: "num"
                },
                {
                    data: "operator_fees",
                    title: "Transactions",
                    type: "num"
                },
                {
                    data: "transactions",
                    title: "Balance",
                    type: "num"
                },
                {
                    data: "balance",
                    title: "After Tax",
                    type: "num"
                },
                {
                    data: "details",
                    title: "Details",
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button class='btn-details' data-bs-toggle="modal" data-bs-target="#billDetailModal">
                <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.9998 2.9707C8.15186 2.9707 4.64826 5.2393 2.25734 8.99767C1.71214 9.85208 1.46484 10.9467 1.46484 11.9957C1.46484 13.0445 1.71205 14.1389 2.25703 14.9932C4.64795 18.7519 8.1517 21.0207 11.9998 21.0207C15.848 21.0207 19.3517 18.7519 21.7427 14.9932C22.2876 14.1389 22.5348 13.0445 22.5348 11.9957C22.5348 10.9469 22.2876 9.8525 21.7427 8.99816C19.3517 5.2395 15.848 2.9707 11.9998 2.9707ZM3.52266 9.80325C5.71174 6.3619 8.78799 4.4707 11.9998 4.4707C15.2117 4.4707 18.288 6.3619 20.477 9.80325L20.4777 9.80423C20.8322 10.3597 21.0348 11.1549 21.0348 11.9957C21.0348 12.8365 20.8322 13.6317 20.4777 14.1872L20.477 14.1882C18.288 17.6295 15.2117 19.5207 11.9998 19.5207C8.78799 19.5207 5.71174 17.6295 3.52266 14.1882L3.52204 14.1872C3.16745 13.6317 2.96484 12.8365 2.96484 11.9957C2.96484 11.1549 3.16745 10.3597 3.52204 9.80423L3.52266 9.80325ZM9.17188 11.9999C9.17188 10.4341 10.4361 9.16992 12.0019 9.16992C13.5677 9.16992 14.8319 10.4341 14.8319 11.9999C14.8319 13.5657 13.5677 14.8299 12.0019 14.8299C10.4361 14.8299 9.17188 13.5657 9.17188 11.9999ZM12.0019 7.66992C9.60766 7.66992 7.67188 9.60571 7.67188 11.9999C7.67188 14.3941 9.60766 16.3299 12.0019 16.3299C14.3961 16.3299 16.3319 14.3941 16.3319 11.9999C16.3319 9.60571 14.3961 7.66992 12.0019 7.66992Z" fill="#585858"></path></svg>
                </button>`;
                    }
                },
                {
                    data: "action",
                    title: "Action",
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        <button class='btn-edit d-flex align-items-center justify-content-center gap-1 w-100' data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.35347 3.43444C8.77209 3.18539 9.3358 3.32411 9.60347 3.77006L9.70933 3.95289L9.71021 3.95443C10.2402 4.87813 11.0439 5.50739 12.0012 5.50739C12.958 5.50739 13.7642 4.87847 14.2991 3.95567L14.4065 3.77006C14.6742 3.32411 15.2379 3.18548 15.6565 3.43454L17.3979 4.43107C17.9451 4.74379 18.1336 5.45246 17.8208 5.99433C17.286 6.91731 17.1423 7.92886 17.6202 8.75811C18.0981 9.58741 19.0452 9.96989 20.11 9.96989C20.7384 9.96989 21.26 10.4867 21.26 11.1199V12.8799C21.26 13.5082 20.7432 14.0299 20.11 14.0299C19.0452 14.0299 18.0981 14.4124 17.6202 15.2417C17.1426 16.0704 17.2858 17.0812 17.8198 18.0037C18.1351 18.5577 17.9434 19.257 17.3975 19.5689L15.6674 20.5588L15.6565 20.5653C15.2379 20.8144 14.6742 20.6757 14.4065 20.2297L14.3007 20.0469L14.3 20.0456C13.77 19.1218 12.9662 18.4924 12.0087 18.4924C11.052 18.4924 10.2458 19.1213 9.71093 20.0441L9.60345 20.2298C9.33578 20.6757 8.77214 20.8143 8.35352 20.5652L6.6121 19.5687C6.06491 19.256 5.87642 18.5475 6.18909 18.0056C6.72398 17.0826 6.86773 16.071 6.38982 15.2417C5.91192 14.4124 4.96479 14.0299 3.9 14.0299C3.26678 14.0299 2.75 13.5082 2.75 12.8799V11.1199C2.75 10.4915 3.26678 9.96989 3.9 9.96989C4.96479 9.96989 5.91192 9.58741 6.38982 8.75811C6.86771 7.92884 6.72399 6.91727 6.18916 5.99426C5.87639 5.45241 6.06528 4.74354 6.61251 4.43084L8.34256 3.44093L8.35347 3.43444ZM6.18916 5.99426C6.18907 5.9941 6.18897 5.99395 6.18888 5.99379L5.54 6.36989L6.1894 5.99468C6.18932 5.99454 6.18924 5.9944 6.18916 5.99426ZM10.8946 3.00642C10.2226 1.87692 8.75191 1.45644 7.59248 2.1418L5.86749 3.12894C4.59518 3.85627 4.16378 5.48714 4.8906 6.7451L4.89112 6.74599C5.26606 7.39286 5.20721 7.80608 5.09018 8.00917C4.97308 8.21237 4.64521 8.46989 3.9 8.46989C2.43322 8.46989 1.25 9.66824 1.25 11.1199V12.8799C1.25 14.3315 2.43322 15.5299 3.9 15.5299C4.64521 15.5299 4.97308 15.7874 5.09018 15.9906C5.20721 16.1937 5.26606 16.6069 4.89112 17.2538L4.8906 17.2547C4.16378 18.5126 4.59558 20.1437 5.8679 20.8711L7.59257 21.858C8.75199 22.5433 10.2226 22.1229 10.8946 20.9934L11.0091 20.7957C11.3841 20.1487 11.773 19.9924 12.0087 19.9924C12.2434 19.9924 12.6293 20.1474 12.9993 20.7929L13.0009 20.7957L13.1109 20.9857L13.1154 20.9934C13.7874 22.1228 15.258 22.5433 16.4174 21.858L18.1425 20.8708C19.4157 20.143 19.8444 18.5234 19.1212 17.2578L19.1189 17.2538C18.7439 16.6069 18.8028 16.1937 18.9198 15.9906C19.0369 15.7874 19.3648 15.5299 20.11 15.5299C21.5768 15.5299 22.76 14.3315 22.76 12.8799V11.1199C22.76 9.65311 21.5616 8.46989 20.11 8.46989C19.3648 8.46989 19.0369 8.21237 18.9198 8.00916C18.8028 7.80608 18.7439 7.39286 19.1189 6.74599L19.1194 6.7451C19.8463 5.48701 19.4147 3.85592 18.1421 3.12871L16.4175 2.14181C15.2581 1.45644 13.7874 1.87688 13.1154 3.00639L13.0009 3.20411C12.6259 3.85103 12.237 4.00739 12.0012 4.00739C11.7666 4.00739 11.3807 3.85235 11.0107 3.20689L11.0091 3.20411L10.8991 3.01409L10.8946 3.00642ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12ZM12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25Z" fill="#585858"></path></svg>
                    <span class="fs-8px black-58">Add transaction</span>
                </button>
                    `
                    }

                },
            ],
            pageLength: 5,
            order: [
                [1, "asc"]
            ],
            lengthChange: false,
            "initComplete": function() {
                $('.dt-search input').attr('placeholder', 'Search here...');
            }
        });

        $("#cod-billings-table").DataTable({
            processing: true,
            serverSide: false, // Disable server-side for dummy data
            data: [{
                    img: '',
                    driver: "Mohamed Abdelhamid",
                    balance: "$200",
                    details: "",

                },

            ],
            columns: [{
                    data: "img",
                    title: "",
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                    <img src="{{ asset('new/src/assets/images/user.jpg') }}"
                            alt="No Image" class="w-8 h-8 rounded-full object-cover">
                    `;
                    }
                },
                {
                    data: "driver",
                    title: "Driver",
                    render: function(data, type, row) {
                        return `<div">
                                <p class="fs-128 black-58 mb-1 text-slide-wrapper">
                                <span class="text-slide">${row.driver}</span>
                                </p>
                                <p class="fs-96 gray-94 m-0">${row.phone}</p>
                            </div>`;
                    }
                },
                {
                    data: "balance",
                    title: "Balance",
                    type: "num"
                },
                {
                    data: "details",
                    title: "Details",
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        <button class='btn-details' data-bs-toggle="modal" data-bs-target="#codDetailModal">
                <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.9998 2.9707C8.15186 2.9707 4.64826 5.2393 2.25734 8.99767C1.71214 9.85208 1.46484 10.9467 1.46484 11.9957C1.46484 13.0445 1.71205 14.1389 2.25703 14.9932C4.64795 18.7519 8.1517 21.0207 11.9998 21.0207C15.848 21.0207 19.3517 18.7519 21.7427 14.9932C22.2876 14.1389 22.5348 13.0445 22.5348 11.9957C22.5348 10.9469 22.2876 9.8525 21.7427 8.99816C19.3517 5.2395 15.848 2.9707 11.9998 2.9707ZM3.52266 9.80325C5.71174 6.3619 8.78799 4.4707 11.9998 4.4707C15.2117 4.4707 18.288 6.3619 20.477 9.80325L20.4777 9.80423C20.8322 10.3597 21.0348 11.1549 21.0348 11.9957C21.0348 12.8365 20.8322 13.6317 20.4777 14.1872L20.477 14.1882C18.288 17.6295 15.2117 19.5207 11.9998 19.5207C8.78799 19.5207 5.71174 17.6295 3.52266 14.1882L3.52204 14.1872C3.16745 13.6317 2.96484 12.8365 2.96484 11.9957C2.96484 11.1549 3.16745 10.3597 3.52204 9.80423L3.52266 9.80325ZM9.17188 11.9999C9.17188 10.4341 10.4361 9.16992 12.0019 9.16992C13.5677 9.16992 14.8319 10.4341 14.8319 11.9999C14.8319 13.5657 13.5677 14.8299 12.0019 14.8299C10.4361 14.8299 9.17188 13.5657 9.17188 11.9999ZM12.0019 7.66992C9.60766 7.66992 7.67188 9.60571 7.67188 11.9999C7.67188 14.3941 9.60766 16.3299 12.0019 16.3299C14.3961 16.3299 16.3319 14.3941 16.3319 11.9999C16.3319 9.60571 14.3961 7.66992 12.0019 7.66992Z" fill="#585858"></path></svg>
                </button>
                    `;
                    }
                }
            ],
            columnDefs: [{
                    width: "82px",
                    targets: 0
                } // Set the width for the first column
            ],
            autoWidth: false,
            pageLength: 5,
            order: [
                [1, "asc"]
            ],
            lengthChange: false,
            "initComplete": function() {
                $('.dt-search input').attr('placeholder', 'Search here...');
            }
        });

        $(document).on('shown.bs.modal', '#addTransactionModal', function() {
            $("#action-type").val(null).trigger("change.select2");
            $("#action-type").select2({
                dropdownParent: $("#addTransactionModal .modal-body .action-type"),
                placeholder: "Action type",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 1, // Ensures the search box is always visible
                language: {
                    searching: function() {
                        return "Searching...";
                    },
                    noResults: function() {
                        return "No matching reason found";
                    }
                }
            });
        });

        $(document).on('shown.bs.modal', '#orderActionModal', function() {
            $("#action-type-order").val(null).trigger("change.select2");
            $("#action-type-order").select2({
                dropdownParent: $("#orderActionModal .modal-body .action-type"),
                placeholder: "Action type",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 1,
                language: {
                    searching: function() {
                        return "Searching...";
                    },
                    noResults: function() {
                        return "No matching reason found";
                    }
                }
            });
            $("#order-responsible").val(null).trigger("change.select2");
            $("#order-responsible").select2({
                dropdownParent: $("#orderActionModal .modal-body .orderResponsible"),
                placeholder: "Responsible",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 1,
                language: {
                    searching: function() {
                        return "Searching...";
                    },
                    noResults: function() {
                        return "No matching reason found";
                    }
                }
            });
        });

        $(document).on('shown.bs.modal', '#codPaymentModal', function() {
            $("#action-type-cod").val(null).trigger("change.select2");
            $("#action-type-cod").select2({
                dropdownParent: $("#codPaymentModal .modal-body .action-type"),
                placeholder: "Action type",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 1, // Ensures the search box is always visible
                language: {
                    searching: function() {
                        return "Searching...";
                    },
                    noResults: function() {
                        return "No matching reason found";
                    }
                }
            });
        });



        $(document).on('shown.bs.modal', '#billDetailModal', function() {

            if ($('#detail-billing-table').length) {

                // Initialize DataTable
                $(document).ready(function() {
                    // Initialize DataTable
                    $('#detail-billing-table').DataTable({
                        processing: true,
                        serverSide: false, // Disable server-side processing for dummy data
                        data: [{
                                order_time: "2023-10-01 10:00",
                                id: "12345",
                                delivery_fees: "10",
                                action_amount: "50",
                                paid: "unpaied",
                                collection_amt: "10",
                                balance: "0",
                                after_tax: "45",
                                status: "Completed",
                                action: ""
                            },
                            {
                                order_time: "2023-10-02 11:00",
                                id: "67890",
                                delivery_fees: "15",
                                action_amount: "60",
                                paid: "unpaid",
                                collection_amt: "10",
                                balance: "0",
                                after_tax: "54",
                                status: "Pending",
                                action: ""
                            },
                            {
                                order_time: "2023-10-03 12:00",
                                id: "54321",
                                delivery_fees: "20",
                                action_amount: "70",
                                paid: "unpaid",
                                collection_amt: "10",
                                balance: "0",
                                after_tax: "63",
                                status: "Completed",
                                action: ""
                            }
                        ],
                        columns: [{
                                data: "order_time",
                                title: "Order time",
                                render: function(data, type, row) {
                                    return `
                                <p class="m-0 d-flex flex-column">
                                <span class="fs-112 black-58">01:05 AM</span>
                                <span class="fs-96 black-58">27/Jan/2022</span>
                                </p>
                                `;
                                }
                            },
                            {
                                data: "id",
                                title: "ID"
                            },
                            {
                                data: "delivery_fees",
                                title: "Delivery fees"
                            },
                            {
                                data: "action_amount",
                                title: "Action amount"
                            },
                            {
                                data: "paid",
                                title: "Paid",
                                render: function(data, type, row) {
                                    return `
                                <span class="red-color">${data}</span>
                                `;
                                }
                            },
                            {
                                data: "collection_amt",
                                title: "Collection AMT"
                            },
                            {
                                data: "balance",
                                title: "Balance",
                                render: function(data, type, row) {
                                    return `
                                <span class="greenColor">${data}</span>
                                `;
                                }
                            },
                            {
                                data: "after_tax",
                                title: "After tax",
                                render: function(data, type, row) {
                                    return `
                                <span class="greenColor">${data}</span>
                                `;
                                }
                            },
                            {
                                data: "status",
                                title: "Status"
                            },
                            {
                                data: "action",
                                title: "Action",
                                orderable: false,
                                render: function(data, type, row) {
                                    return `
                                <button class="border-0" data-bs-toggle="modal" data-bs-target="#orderActionModal">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.35347 3.43444C8.77209 3.18539 9.3358 3.32411 9.60347 3.77006L9.70933 3.95289L9.71021 3.95443C10.2402 4.87813 11.0439 5.50739 12.0012 5.50739C12.958 5.50739 13.7642 4.87847 14.2991 3.95567L14.4065 3.77006C14.6742 3.32411 15.2379 3.18548 15.6565 3.43454L17.3979 4.43107C17.9451 4.74379 18.1336 5.45246 17.8208 5.99433C17.286 6.91731 17.1423 7.92886 17.6202 8.75811C18.0981 9.58741 19.0452 9.96989 20.11 9.96989C20.7384 9.96989 21.26 10.4867 21.26 11.1199V12.8799C21.26 13.5082 20.7432 14.0299 20.11 14.0299C19.0452 14.0299 18.0981 14.4124 17.6202 15.2417C17.1426 16.0704 17.2858 17.0812 17.8198 18.0037C18.1351 18.5577 17.9434 19.257 17.3975 19.5689L15.6674 20.5588L15.6565 20.5653C15.2379 20.8144 14.6742 20.6757 14.4065 20.2297L14.3007 20.0469L14.3 20.0456C13.77 19.1218 12.9662 18.4924 12.0087 18.4924C11.052 18.4924 10.2458 19.1213 9.71093 20.0441L9.60345 20.2298C9.33578 20.6757 8.77214 20.8143 8.35352 20.5652L6.6121 19.5687C6.06491 19.256 5.87642 18.5475 6.18909 18.0056C6.72398 17.0826 6.86773 16.071 6.38982 15.2417C5.91192 14.4124 4.96479 14.0299 3.9 14.0299C3.26678 14.0299 2.75 13.5082 2.75 12.8799V11.1199C2.75 10.4915 3.26678 9.96989 3.9 9.96989C4.96479 9.96989 5.91192 9.58741 6.38982 8.75811C6.86771 7.92884 6.72399 6.91727 6.18916 5.99426C5.87639 5.45241 6.06528 4.74354 6.61251 4.43084L8.34256 3.44093L8.35347 3.43444ZM6.18916 5.99426C6.18907 5.9941 6.18897 5.99395 6.18888 5.99379L5.54 6.36989L6.1894 5.99468C6.18932 5.99454 6.18924 5.9944 6.18916 5.99426ZM10.8946 3.00642C10.2226 1.87692 8.75191 1.45644 7.59248 2.1418L5.86749 3.12894C4.59518 3.85627 4.16378 5.48714 4.8906 6.7451L4.89112 6.74599C5.26606 7.39286 5.20721 7.80608 5.09018 8.00917C4.97308 8.21237 4.64521 8.46989 3.9 8.46989C2.43322 8.46989 1.25 9.66824 1.25 11.1199V12.8799C1.25 14.3315 2.43322 15.5299 3.9 15.5299C4.64521 15.5299 4.97308 15.7874 5.09018 15.9906C5.20721 16.1937 5.26606 16.6069 4.89112 17.2538L4.8906 17.2547C4.16378 18.5126 4.59558 20.1437 5.8679 20.8711L7.59257 21.858C8.75199 22.5433 10.2226 22.1229 10.8946 20.9934L11.0091 20.7957C11.3841 20.1487 11.773 19.9924 12.0087 19.9924C12.2434 19.9924 12.6293 20.1474 12.9993 20.7929L13.0009 20.7957L13.1109 20.9857L13.1154 20.9934C13.7874 22.1228 15.258 22.5433 16.4174 21.858L18.1425 20.8708C19.4157 20.143 19.8444 18.5234 19.1212 17.2578L19.1189 17.2538C18.7439 16.6069 18.8028 16.1937 18.9198 15.9906C19.0369 15.7874 19.3648 15.5299 20.11 15.5299C21.5768 15.5299 22.76 14.3315 22.76 12.8799V11.1199C22.76 9.65311 21.5616 8.46989 20.11 8.46989C19.3648 8.46989 19.0369 8.21237 18.9198 8.00916C18.8028 7.80608 18.7439 7.39286 19.1189 6.74599L19.1194 6.7451C19.8463 5.48701 19.4147 3.85592 18.1421 3.12871L16.4175 2.14181C15.2581 1.45644 13.7874 1.87688 13.1154 3.00639L13.0009 3.20411C12.6259 3.85103 12.237 4.00739 12.0012 4.00739C11.7666 4.00739 11.3807 3.85235 11.0107 3.20689L11.0091 3.20411L10.8991 3.01409L10.8946 3.00642ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12ZM12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25Z" fill="#585858"></path></svg>
                                </button>
                                `;
                                }
                            }
                        ],
                        pageLength: 5, // Number of rows per page
                        lengthChange: false, // Disable "Show X entries" dropdown
                        ordering: true, // Enable sorting
                        searching: false, // Enable search functionality
                        paging: true, // Enable pagination
                        info: true // Show table information
                    });
                });
            } else {
                console.error("Table not found in the modal");
            }
        });

        $(document).on('hidden.bs.modal', '#billDetailModal', function() {
            console.log("Modal closed, destroying DataTable");

            if ($.fn.DataTable.isDataTable('#detail-billing-table')) {
                $('#detail-billing-table').DataTable().destroy();
                console.log("DataTable destroyed");
            }
        });

        $(document).on('shown.bs.modal', '#codDetailModal', function() {
            console.log("Modal fully shown, checking for table");

            if ($('#cod-billing-table').length) {


                // Initialize DataTable
                $(document).ready(function() {
                    // Initialize DataTable
                    $('#cod-billing-table').DataTable({
                        processing: true,
                        serverSide: false, // Disable server-side processing for dummy data
                        data: [{
                                order_time: "2023-10-01 10:00",
                                id: "12345",
                                delivery_fees: "10",
                                action_amount: "50",
                                paid: "unpaied",
                                collection_amt: "10",
                                balance: "0",
                                after_tax: "45",
                                status: "Completed",
                                action: ""
                            },

                        ],
                        columns: [{
                                data: "order_time",
                                title: "Data /Time",
                                render: function(data, type, row) {
                                    return `
                                <p class="m-0 d-flex flex-column">
                                <span class="fs-112 black-58">01:05 AM</span>
                                <span class="fs-96 black-58">27/Jan/2022</span>
                                </p>
                                `;
                                }
                            },
                            {
                                data: "id",
                                title: "Order count"
                            },
                            {
                                data: "delivery_fees",
                                title: "Amount"
                            },
                            {
                                data: "action_amount",
                                title: "Paid amount"
                            },
                            {
                                data: "paid",
                                title: "Remaining amount",
                                render: function(data, type, row) {
                                    return `
                                <span class="red-color">${data}</span>
                                `;
                                }
                            },
                            {
                                data: "status",
                                title: "Status"
                            },
                            {
                                data: "action",
                                title: "Detail",
                                orderable: false,
                                render: function(data, type, row) {
                                    return `
                                <button class="border-0">
                                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.35347 3.43444C8.77209 3.18539 9.3358 3.32411 9.60347 3.77006L9.70933 3.95289L9.71021 3.95443C10.2402 4.87813 11.0439 5.50739 12.0012 5.50739C12.958 5.50739 13.7642 4.87847 14.2991 3.95567L14.4065 3.77006C14.6742 3.32411 15.2379 3.18548 15.6565 3.43454L17.3979 4.43107C17.9451 4.74379 18.1336 5.45246 17.8208 5.99433C17.286 6.91731 17.1423 7.92886 17.6202 8.75811C18.0981 9.58741 19.0452 9.96989 20.11 9.96989C20.7384 9.96989 21.26 10.4867 21.26 11.1199V12.8799C21.26 13.5082 20.7432 14.0299 20.11 14.0299C19.0452 14.0299 18.0981 14.4124 17.6202 15.2417C17.1426 16.0704 17.2858 17.0812 17.8198 18.0037C18.1351 18.5577 17.9434 19.257 17.3975 19.5689L15.6674 20.5588L15.6565 20.5653C15.2379 20.8144 14.6742 20.6757 14.4065 20.2297L14.3007 20.0469L14.3 20.0456C13.77 19.1218 12.9662 18.4924 12.0087 18.4924C11.052 18.4924 10.2458 19.1213 9.71093 20.0441L9.60345 20.2298C9.33578 20.6757 8.77214 20.8143 8.35352 20.5652L6.6121 19.5687C6.06491 19.256 5.87642 18.5475 6.18909 18.0056C6.72398 17.0826 6.86773 16.071 6.38982 15.2417C5.91192 14.4124 4.96479 14.0299 3.9 14.0299C3.26678 14.0299 2.75 13.5082 2.75 12.8799V11.1199C2.75 10.4915 3.26678 9.96989 3.9 9.96989C4.96479 9.96989 5.91192 9.58741 6.38982 8.75811C6.86771 7.92884 6.72399 6.91727 6.18916 5.99426C5.87639 5.45241 6.06528 4.74354 6.61251 4.43084L8.34256 3.44093L8.35347 3.43444ZM6.18916 5.99426C6.18907 5.9941 6.18897 5.99395 6.18888 5.99379L5.54 6.36989L6.1894 5.99468C6.18932 5.99454 6.18924 5.9944 6.18916 5.99426ZM10.8946 3.00642C10.2226 1.87692 8.75191 1.45644 7.59248 2.1418L5.86749 3.12894C4.59518 3.85627 4.16378 5.48714 4.8906 6.7451L4.89112 6.74599C5.26606 7.39286 5.20721 7.80608 5.09018 8.00917C4.97308 8.21237 4.64521 8.46989 3.9 8.46989C2.43322 8.46989 1.25 9.66824 1.25 11.1199V12.8799C1.25 14.3315 2.43322 15.5299 3.9 15.5299C4.64521 15.5299 4.97308 15.7874 5.09018 15.9906C5.20721 16.1937 5.26606 16.6069 4.89112 17.2538L4.8906 17.2547C4.16378 18.5126 4.59558 20.1437 5.8679 20.8711L7.59257 21.858C8.75199 22.5433 10.2226 22.1229 10.8946 20.9934L11.0091 20.7957C11.3841 20.1487 11.773 19.9924 12.0087 19.9924C12.2434 19.9924 12.6293 20.1474 12.9993 20.7929L13.0009 20.7957L13.1109 20.9857L13.1154 20.9934C13.7874 22.1228 15.258 22.5433 16.4174 21.858L18.1425 20.8708C19.4157 20.143 19.8444 18.5234 19.1212 17.2578L19.1189 17.2538C18.7439 16.6069 18.8028 16.1937 18.9198 15.9906C19.0369 15.7874 19.3648 15.5299 20.11 15.5299C21.5768 15.5299 22.76 14.3315 22.76 12.8799V11.1199C22.76 9.65311 21.5616 8.46989 20.11 8.46989C19.3648 8.46989 19.0369 8.21237 18.9198 8.00916C18.8028 7.80608 18.7439 7.39286 19.1189 6.74599L19.1194 6.7451C19.8463 5.48701 19.4147 3.85592 18.1421 3.12871L16.4175 2.14181C15.2581 1.45644 13.7874 1.87688 13.1154 3.00639L13.0009 3.20411C12.6259 3.85103 12.237 4.00739 12.0012 4.00739C11.7666 4.00739 11.3807 3.85235 11.0107 3.20689L11.0091 3.20411L10.8991 3.01409L10.8946 3.00642ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12ZM12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25Z" fill="#585858"></path></svg>
                                </button>
                                `;
                                }
                            } // Disable sorting for action column
                        ],
                        pageLength: 5, // Number of rows per page
                        lengthChange: false, // Disable "Show X entries" dropdown
                        ordering: true, // Enable sorting
                        searching: false, // Enable search functionality
                        paging: true, // Enable pagination
                        info: true // Show table information
                    });
                });
            } else {
                console.error("Table not found in the modal");
            }
        });

        $(document).on('hidden.bs.modal', '#codDetailModal', function() {
            console.log("Modal closed, destroying DataTable");

            if ($.fn.DataTable.isDataTable('#cod-billing-table')) {
                $('#cod-billing-table').DataTable().destroy();
                console.log("DataTable destroyed");
            }
        });

        $(document).on("mouseenter", ".text-slide-wrapper", function() {
       
            const $textSlide = $(this).find(".text-slide");
            const textContent = $textSlide.text();
            console.log(textContent)
            const textLength = textContent.length;
            const $wrapper = $(this);

            const textWidth = $textSlide[0].scrollWidth;
            const wrapperWidth = $wrapper.outerWidth();

            const moveDistance = textWidth - wrapperWidth;
            const calcValue = `-${moveDistance}px`;

            const keyframes = `
                @keyframes textSlide {
                    0% {
                        transform: translateX(0); /* Start position */
                    }
                    50% {
                        transform: translateX(${calcValue}); /* Move to the last letter */
                    }
                    100% {
                        transform: translateX(0); /* Return to start */
                    }
                }`;
            // Check if the keyframes are already appended to avoid duplication
            if ($("style#textSlideKeyframe").length === 0) {
                $("head").append(`<style id="textSlideKeyframe">${keyframes}</style>`);
            } else {
                // If style already exists, update the keyframes
                $("style#textSlideKeyframe").html(keyframes);
            }



        });

    });









    //     initializeOperatorBillingsDataTable(id); // Initialize the DataTable

    //     $('#operator_billings').on('click', function(e) {
    //         e.preventDefault();


    //         initializeOperatorBillingsDataTable(id);
    //     });

    //     $('#cod_billings').on('click', function(e) {
    //         e.preventDefault();


    //         initializeCODBillingsDataTable(id);
    //     });
    // });

    // function initializeOperatorBillingsDataTable(id) {
    //     if ($.fn.DataTable.isDataTable('#operator-billings-table')) {
    //         $('#operator-billings-table').DataTable().destroy(); // Destroy existing table
    //     }

    //     $('#operator-billings-table').DataTable({
    //         "processing": true,
    //         "serverSide": true,
    //         "ajax": {
    //             "url": "{{ route('get-billings-report') }}",
    //             "type": "GET",
    //             "data": function(d) {
    //                 d.id = id;
    //             }
    //         },
    //         "columns": [{
    //                 "data": "driver"
    //             },
    //             {
    //                 "data": 'id'
    //             },
    //             {
    //                 "data": "order_count"
    //             },
    //             {
    //                 "data": "service_fees"
    //             },
    //             {
    //                 "data": "operator_fees"
    //             },
    //             {
    //                 "data": "balance"
    //             },
    //             {
    //                 "data": "after_tax"
    //             }
    //         ],
    //         "pageLength": 20,
    //         "lengthChange": false,
    //         "initComplete": function() {

    //         $('.dt-search input').attr('placeholder', 'Search here...');
    //     }
    //     });
    // }



    // function initializeCODBillingsDataTable(id) {
    //     if ($.fn.DataTable.isDataTable('#cod-billings-table')) {
    //         $('#cod-billings-table').DataTable().destroy(); // Destroy existing table
    //     }

    //     $('#cod-billings-table').DataTable({
    //         "processing": true,
    //         "serverSide": true,
    //         "ajax": {
    //             "url": "{{ route('get-cod-billings-report') }}",
    //             "type": "GET",
    //             "data": function(d) {
    //                 d.id = id;
    //             }
    //         },
    //         "columns": [{
    //                 "data": "driver"
    //             },

    //             {
    //                 "data": "balance"
    //             }
    //         ],
    //         "pageLength": 20,
    //         "lengthChange": false
    //     });
    // }
</script>
