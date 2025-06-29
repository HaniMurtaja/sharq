<div id="Templates" data-drawer="Templates"
    class="fixed top-0 right-0 z-50 min-h-full pr-4 pb-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">

    <div class="flex flex-col h-screen templateContent">
        <div class="flex items-center flex-row-reverse justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700" id="title-template">
                New Template
            </h5>
            <button id="close-drawer" class="text-gray-500 close-drawer" data-drawer="Users">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 px-3 templateFormContainer">
            <form id="regForm" style="margin-top: 0px;" enctype="multipart/form-data">
                @csrf

                <p class="infoText">
                    Information
                </p>

                <div class="custom-fieldset">
                    <label for="template-name" class="custom-legend">
                        Template Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="input-field" id="role_name" name="name" placeholder="Name">
                    <input hidden name="template_id" id="template_id">
                </div>

                <!-- Frist  Collapse container -->

                @foreach (App\Enum\PermissionGroups::cases() as $permission_group)
                    <div class="collapse-container">
                        <a class="collapse-tab collapsed" id="systemBillingTemplate" data-bs-toggle="collapse"
                            href="#{{ $permission_group->value }}" role="button" aria-expanded="false"
                            aria-controls="collapseExample">
                            <div class="collapseNameIcon">

                                <h4 id="group_name">
                                    {{ $permission_group->getLabel() }}
                                </h4>

                            </div>
                            <div class="collapseArrowsBadges">

                                <div class="editIcon downarrow">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                        </g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path
                                                d="M5.70711 9.71069C5.31658 10.1012 5.31658 10.7344 5.70711 11.1249L10.5993 16.0123C11.3805 16.7927 12.6463 16.7924 13.4271 16.0117L18.3174 11.1213C18.708 10.7308 18.708 10.0976 18.3174 9.70708C17.9269 9.31655 17.2937 9.31655 16.9032 9.70708L12.7176 13.8927C12.3271 14.2833 11.6939 14.2832 11.3034 13.8927L7.12132 9.71069C6.7308 9.32016 6.09763 9.32016 5.70711 9.71069Z"
                                                fill="#dadada"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <div class="collapse" id="{{ $permission_group->value }}">
                            <div class="card card-body">


                                @foreach ($permission_group->getPermissions() as $permission)
                                    <div
                                        class="form-check p-0 form-switch d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                            for="{{ $permission->value }}">{{ $permission->getLabel() }}</label>
                                        <input class="form-check-input position-relative" type="checkbox" role="switch"
                                            id="{{$permission->value}}" name="{{ $permission->value }}">
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                @endforeach





                <!-- Buttons -->
                <div class="templatesActionBtns w-100 d-flex justify-content-between align-items-center" dir="ltr">

                    <div>

                        <button type="button" class="templateSaveBtn" id="save-template-btn">
                            Save
                        </button>
                    </div>
                </div>








            </form>
        </div>
    </div>
</div>
