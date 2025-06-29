{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

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

    .form-control,
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }
</style>

<div class="active tab-pane table-responsive p-0" style="height: 450px;" id="clients_menu">
    <div class="card-header">
        <h3 class="card-title"> New Client</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form id="client-form" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <input type="file" id="file-upload" class="file-upload" name="profile_photo"
                            accept="image/*">
                        <label for="file-upload" class="upload-label" id="upload-label">
                            <svg viewBox="0 0 24 24" id="user-icon">
                                <path
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                        </label>
                    </div>
                </div>



            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <p style="display: inline">Auto Dispatch</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group text-right">
                        <!-- Use text-right class to align contents to the right -->
                        <label class="switch">
                            <input type="checkbox" id="auto_dispatch" class="status-toggle" value="0"
                                name="auto_dispatch">
                            <span class="slider round"></span>
                        </label>
                        <span id="locked_error" class="text-danger"></span>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <p style="display: inline">Integration</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group text-right">
                        <!-- Use text-right class to align contents to the right -->
                        <label class="switch">
                            <input type="checkbox" id="is_integration" class="status-toggle" value="0"
                                name="is_integration">
                            <span class="slider round"></span>
                        </label>
                        <span id="locked_error" class="text-danger"></span>
                    </div>
                </div>

            </div>



            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="firstName" name="name" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="lastName" name="phone"
                            placeholder="Phone Number">
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                        <span id="email_error" class="text-danger"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Password">
                        <span id="password_error" class="text-danger"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="country_id">
                            <option value="" selected="selected" disabled>Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"> {{ $country->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" name="city_id" style="width: 100%;">
                            <option value="" selected="selected" disabled>City</option>
                            @foreach ($all_cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="currency">
                            <option value="" selected="selected" disabled>Currency</option>
                            @foreach (App\Enum\Currency::values() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="defaultPreparationTime"
                            name="default_prepartion_time" placeholder="Default preparation time">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="minPreparationTime"
                            name="min_prepartion_time" placeholder="Min. preparation time">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="partialPay" name="partial_pay"
                            placeholder="Partial pay">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="note" name="note"
                            placeholder="Note">
                    </div>
                </div>
            </div>

            <p>Group</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="client_group_id">
                            <option value="" selected="selected" disabled>Client group</option>
                            @foreach ($client_groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="driver_group_id">
                            <option value="" selected="selected" disabled>Driver group</option>
                            @foreach ($driver_groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>




            <div class="row" id="integration-div" style="display: none">
                <div class="col-md-6">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="integration_id">
                            <option value="" selected="selected" disabled>Integration</option>
                            @foreach ($integrations as $integration)
                                <option value="{{ $integration->id }}">{{ $integration->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


            </div>




            <div class="row">
                <div class="col-md-3">
                    <button type="button" id="save-client-btn"
                        class="btn btn-block bg-gradient-primary btn-sm">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
 --}}











<section class="globalForm clientCreateForm">

    <div class="globalHeader">
        <h1 id = "client-title">New client</h1>
    </div>


    <form class="customForm sectionGlobalForm" id="client-form" enctype="multipart/form-data">
        @csrf
        <p class="sectionTitle">Inforamtion</p>
        <span class="visibility-hidden"></span>

        <!-- Button to open the modal -->
        <div class="position-relative">
            <input
                type="file"
                accept="image/*"
                name="profile_photo"
                id="fileInput"
                style="display: none;"
            >
            <button type="button" id="uploadButton" class="uploadImageBtn">
                <svg width="25.6px" height="25.6px" viewBox="0 0 32 32" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M16.0013 29.3327C23.3651 29.3327 29.3346 23.3631 29.3346 15.9993C29.3346 8.63555 23.3651 2.66602 16.0013 2.66602C8.63751 2.66602 2.66797 8.63555 2.66797 15.9993C2.66797 23.3631 8.63751 29.3327 16.0013 29.3327Z"
                        fill="#F46624" stroke="#C3521D" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M16.1622 17.04C16.0689 17.0267 15.9489 17.0267 15.8422 17.04C13.4956 16.96 11.6289 15.04 11.6289 12.68C11.6289 10.2667 13.5756 8.30667 16.0022 8.30667C18.4156 8.30667 20.3756 10.2667 20.3756 12.68C20.3622 15.04 18.5089 16.96 16.1622 17.04Z"
                        fill="white" stroke="#C3521D" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M24.989 25.84C22.6156 28.0133 19.469 29.3333 16.0023 29.3333C12.5356 29.3333 9.38896 28.0133 7.01562 25.84C7.14896 24.5867 7.94896 23.36 9.37562 22.4C13.029 19.9733 19.0023 19.9733 22.629 22.4C24.0556 23.36 24.8556 24.5867 24.989 25.84Z"
                        fill="white" stroke="#C3521D" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
                <svg width="25.6px" height="25.6px" viewBox="0 0 32 32" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="16" fill="white"></rect>
                    <path
                        d="M11.6334 24.3333H20.3667C22.6667 24.3333 23.5834 22.925 23.6917 21.2083L24.1251 14.325C24.2417 12.525 22.8084 11 21.0001 11C20.4917 11 20.0251 10.7083 19.7917 10.2583L19.1917 9.05001C18.8084 8.29167 17.8084 7.66667 16.9584 7.66667H15.0501C14.1917 7.66667 13.1917 8.29167 12.8084 9.05001L12.2084 10.2583C11.9751 10.7083 11.5084 11 11.0001 11C9.19172 11 7.75839 12.525 7.87506 14.325L8.30839 21.2083C8.40839 22.925 9.33339 24.3333 11.6334 24.3333Z"
                        stroke="#F46624" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M14.75 12.6667H17.25" stroke="#F46624" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M16 21C17.4916 21 18.7083 19.7833 18.7083 18.2917C18.7083 16.8 17.4916 15.5833 16 15.5833C14.5083 15.5833 13.2916 16.8 13.2916 18.2917C13.2916 19.7833 14.5083 21 16 21Z"
                        stroke="#F46624" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
            <div id="croppedImage"></div>
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




        <input name="id" id="id" hidden>
        <fieldset class="floating-label-input">
            <input type="text" id="account_number" name="account_number" value="" required />
            <legend>account number<span class="text-danger d-none">*</span></legend>
        </fieldset>
        <fieldset class="floating-label-input">
            <input type="text" id="name" name="name" value="" required />
            <legend>Name<span class="text-danger d-none">*</span></legend>
        </fieldset>

        <div class="modalSelectBox  phoneNewBranch w-100 d-flex flex-row-reverse position-relative">
            <input type="number" id="phone" name="phone" value=""  />
            <label for="template-name" class="customSelectLegend legendPhoneNumber positioned">Phone Number
                </label>
            <select class="phoneNumberCreateClient select2" id="">
                <option></option>
                <option value="1">+996</option>

            </select>


        </div>

        <fieldset class="floating-label-input">
            <input type="text" name="email" id="email" required />
            <legend>Email<span class="text-danger d-none">*</span></legend>

        </fieldset>
        <fieldset class="floating-label-input">
            <input type="password" name="password" id="password" required />
            <legend>Password<span class="text-danger d-none">*</span></legend>
        </fieldset>

        <div class="modalSelectBox countryNewBranch w-100 d-flex flex-row-reverse position-relative">
            <label for="template-name" class="customSelectLegend ">Country<span class="text-danger">*</span></label>
            <select class="countryAddress select2" name="country_id">
                <option></option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}"> {{ $country->name }} </option>
                @endforeach

            </select>
        </div>

        <div class="modalSelectBox cityNewBranch w-100 d-flex flex-row-reverse position-relative">
            <label for="template-name" class="customSelectLegend ">City<span class="text-danger">*</span></label>
            <select class="cityAddress select2" name="city_id">
                <option></option>
                @foreach ($all_cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach

            </select>
        </div>

        <div class="modalSelectBox currencySelect w-100 d-flex flex-row-reverse position-relative">
            <label for="template-name" class="customSelectLegend ">Currency<span
                    class="text-danger d-none">*</span></label>
            <select class="currency select2" name="currency">
                <option></option>
                @foreach (App\Enum\Currency::values() as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach

            </select>
        </div>
        <span class="visibilty-hidden"></span>

        <fieldset class="floating-label-input">
            <input type="number" id="defaultPreparationTime" name="default_prepartion_time" required />
            <legend>Default prepration time<span class="text-danger">*</span></legend>
        </fieldset>
        <fieldset class="floating-label-input">
            <input type="number" required id="minPreparationTime" name="min_prepartion_time" />
            <legend>Min. prepration time<span class="text-danger">*</span></legend>
        </fieldset>
        <fieldset class="floating-label-input">
            <input type="number" id="partialPay" name="partial_pay" required />
            <legend>Parial pay<span class="text-danger d-none">*</span></legend>
            <span>%</span>
        </fieldset>
        <fieldset class="floating-label-input">
            <input type="text" required id="note" name="note" />
            <legend>Note<span class="text-danger d-none">*</span></legend>
        </fieldset>
        <fieldset class="floating-label-input">
            <input type="text" required id="price_order" name="price_order" />
            <legend>Price Of Order<span class="text-danger d-none">*</span></legend>
        </fieldset>

        <p class="sectionTitle">Group</p>
        <span class="visibilty-hidden"></span>

        <div class="modalSelectBox clientGroupCreateUser w-100 d-flex flex-row-reverse position-relative">

            <label for="template-name" class="customSelectLegend ">Client group <span
                    class="text-danger">*</span></label>
            <select class="clientGroup select2" name="client_group_id">
                <option></option>
                @foreach ($client_groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach

            </select>


        </div>

        <div class="modalSelectBox driverGroupCreateUser w-100 d-flex flex-row-reverse position-relative">
            <label for="template-name" class="customSelectLegend ">Driver group <span
                    class="text-danger">*</span></label>
            <select class="driverGroup select2" name="driver_group_id">
                <option></option>
                @foreach ($driver_groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="itemListDivider"></div>

        <div id="integration-div" style="display: none">
            <div class="modalSelectBox driverGroupCreateUser w-100 d-flex flex-row-reverse position-relative">
                <label for="template-name" class="customSelectLegend ">Integration Company<span
                        class="text-danger">*</span></label>
                <select class="driverGroup select2" name="integration_id">
                    <option value=""></option>
                    @foreach ($integrations as $integration)
                        <option value="{{ $integration->id }}">{{ $integration->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>





        <!-- Buttons -->
        <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
            <div>

                <button type="button" id="save-client-btn" class="templateSaveBtn">
                    Save
                </button>
            </div>
        </div>



    </form>
</section>
