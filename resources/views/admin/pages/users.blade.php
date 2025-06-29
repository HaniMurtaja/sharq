@extends('admin.layouts.app')
<style>
    .templateContent {
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #949494 #f0f0f0;
        padding: 19.2px;
        border-radius: 10px;
    }

    .templateContent h5 {
        font-size: 19.2px;
    }

    .templateContent::-webkit-scrollbar {
        width: 12px;
    }

    .templateContent::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 10px;
    }

    .templateContent::-webkit-scrollbar-thumb {
        background: #949494;
        border-radius: 10px;
        border: 2px solid #f0f0f0;
    }

    .templateContent::-webkit-scrollbar-thumb:hover {
        background: #787878;
    }

    #Templates {
        direction: rtl;
    }

    .infoText {
        font-size: 11.2px;
        color: #949494;
        font-weight: 500;
        /* margin-bottom: 1rem; */
    }

    .templateContent .custom-fieldset {
        position: relative;
        width: 50%;
    }

    .templateContent .custom-legend {
        position: absolute;
        top: -10px;
        left: 15px;
        background-color: white;
        padding: 0 5px;
        font-weight: bold;
        color: #949494;
        margin: 0;
        font-size: 9.6px;
        display: flex;
        flex-direction: row-reverse;
    }

    .templateContent .input-field {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 9.6px;
        font-size: 11.2px;
        direction: ltr;
    }

    .templateContent .input-field::focus .custom-legend {
        color: #585858;
    }

    .templateContent .input-field::focus {
        border-color: #585858;
    }

    .templateContent .text-danger {
        color: red;
    }

    .templateContent #regform {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 16px;
    }


    /* Collapse Container */
    .templateContent .collapse-container {
        direction: ltr;
        width: 100%;
        border-radius: 10px;

    }

    .templateContent .collapse-tab {
        padding: 8px 12.8px;
        border: 1px solid #D9D9D9;
        border-radius: .8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
    }

    .templateContent .collapse {
        border-radius: 0;
    }

    .collapse-container:has(.collapse.show) {
        border: 1px solid #000;
    }

    .templateContent .collapse-tab:hover {
        text-decoration: none;
        background-color: #f2f2f2;
    }

    .templateContent .collapseIcon {
        width: 19.2px;
        position: relative;
        top: -3px;
    }

    .templateContent .collapseIcon svg {
        width: 100%;
        height: 100%;
    }

    .templateContent .collapseNameIcon h4 {
        font-size: 12.8px;
        color: #000;
        font-weight: 600;
        margin-bottom: 0;
    }

    .templateContent .collapseNameIcon {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .templateContent .collapseBadge {
        font-size: 9.6px;
        color: #22ad2f;
        padding: .3rem 1.2rem;
        background-color: #e6f5e6;
        border-radius: .8rem;
    }

    .templateContent .editIcon {
        width: 16px;
        position: relative;
        top: 0px;

    }

    .templateContent .editIcon svg {
        width: 100%;
        height: 100%;
    }


    .templateContent .collapseArrowsBadges {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 8px;
    }

    .templateContent .collapse-tab:not(.collapsed) .downarrow svg {
        transform: rotate(180deg);
    }

    .templateContent .collapse-tab:not(.collapsed) {
        border-radius: 8px 8px;
        background-color: #F9F9F9;
    }

    .templateContent .collapse-container .card {
        border-radius: 0 0 8px 8px !important;
        border-top: 0;
        padding: 12.8px;
    }


    .templateContent .form-check-label {
        color: #6c6c6c;
        font-size: 11.2px;
    }

    .templateContent .form-check {
        border: .8px solid #f2f2f2;
        border-radius: 9.6px;
        padding: 0 12.8px !important;
        min-height: 32px;
    }

    .templateContent .form-switch .form-check-input:checked {
        background-color: orange !important;
        border: none;
    }

    .templateContent .form-check:hover {
        background-color: #f2f2f2;
    }

    .templateContent .form-check-input:focus {
        box-shadow: none;

    }

    .templatesActionBtns button {
        border: none;
        font-size: 11.2px;
        color: #fff;
        padding: 8px 28.8px;
        border-radius: 9.6px;
    }

    .templatesActionBtns .templateDeleteBtn {
        background-color: #ff4b36;
    }

    .templatesActionBtns .templateSaveBtn {
        background-color: #F46624;
    }

    .templatesActionBtns .templateDeleteBtn:hover {
        background-color: #f32a16;
    }

    .templatesActionBtns .templateSaveBtn:hover {
        background-color: #DC5C20;
    }

    .templatesActionBtns .templateCancelBtn {
        background-color: transparent;
        border: 1px solid #949494;
        color: #949494;
    }

    .templatesActionBtns .templateCancelBtn:hover {
        border: 1px solid #585858;
        color: #585858;
    }

    @media (max-width: 992px) {
        #Templates {
            z-index: 10000;
            width: 80%;
        }

    }

    @media (max-width: 540px) {

        .templatesActionBtns button {
            border: none;
            font-size: 10.2px;
            color: #fff;
            padding: 4px 6.8px;
            border-radius: 9.6px;
        }

        #Templates {
            width: 92%;
            padding-right: 0 !important;
        }

        .templateFormContainer {
            padding: 0 !important;
        }

        .templateContent .custom-fieldset {
            width: 100%;
        }

    }
</style>

@section('content')
    <div class="flex flex-col p-6">


        @include('admin.pages.users.add')
        <!-- End Users Drawer -->

        <!-- Templates Drawer -->
        @include('admin.pages.templates.add')
        <!-- End Templates Drawer -->


        <!-- Tabs and Button -->
        <div class="flex flex-col-reverse justify-between md:flex-row">
            <div class="flex mb-4 space-x-8 border-b operator_tabs">
                <button
                    class="w-full px-10 py-3 font-semibold border-b-2 border-mainColor text-mainColor operator_tab md:w-auto"
                    data-tab="Users" id="users">
                    Users
                </button>
                <button id="templates" class="w-full px-10 py-3 text-gray-600 operator_tab md:w-auto" data-tab="Templates">
                    Tempaltes
                </button>
            </div>

            <div class="flex space-x-4 ">
                <button type="button" data-tab="Users" data-drawer="Users" id="new-user"
                    class="flex items-center justify-center w-full h-12 gap-3 px-4 py-2 text-white rounded-md operator_btns open-drawer md:w-48 bg-blue1 border-blue1">
                    <img src="{{ asset('new/src/assets/icons/add-square.svg') }}" alt="" />
                    <span>New</span>
                </button>

                <button type="button" data-tab="Templates" data-drawer="Templates"
                    class="items-center justify-center hidden w-full h-12 gap-3 px-4 py-2 text-white rounded-md operator_btns open-drawer md:w-48 bg-blue1 border-blue1">
                    <img src="{{ asset('new/src/assets/icons/add-square.svg') }}" alt="" />
                    <span>New</span>
                </button>
            </div>
        </div>

        @include('admin.pages.users.list')

        @include('admin.pages.templates.list')

    </div>

    </div>

    @include('admin.pages.users.scripts')
@endsection
