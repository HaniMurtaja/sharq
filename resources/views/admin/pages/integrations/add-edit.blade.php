<div id="integrationDrawer" data-drawer="Integration"
    class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">
    <div class="flex flex-col h-screen overflow-scroll">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700" id="title-vehicle">
                New Integration Company
            </h5>
            <button id="close-drawer" class="text-gray-500 close-drawer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 p-8 overflow-scroll">
            <form method="post" enctype="multipart/form-data" id="integration-form">
                @csrf



                <div class="flex flex-col w-full mt-8">

                    <div class="grid w-full grid-cols-1 gap-8 mt-5 md:grid-cols-1">

                        <label class="flex flex-col w-full gap-2">
                            <span>Name</span>

                            <input type="text" id="name" placeholder="Name" name="name"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />

                            <input type="text" hidden id="integration_id" placeholder="Name" name="integration_id"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>


                        <div class="flex justify-between col-span-2">
                            <span>Has a cancellation reason</span>
                            <div class="switch-container">
                                <input type="checkbox" id="switch-has-cancel-reason" class="switch-checkbox"
                            value="0"
                            name="has_cancel_reason">
                                <label for="switch-has-cancel-reason" class="switch-label">
                                    <span class="switch-button"></span>
                                </label>
                            </div>
                        </div>

                        <br>
                        <br>

                    </div>
                    <br>
                    <br>

                    <br>
                    <br>

                    <div class="row">
                        <br>
                        <br>
                        <br>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Integration Type</label>
                                <select class="form-control select2" style="width: 100%;" name="client_type">
                                    <option value="" selected="selected" disabled>Integration Type</option>
                                     <option value="1">New Client</option>
                                     <option value="0">Old Client</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>OTP&AWB</label>
                                <select class="form-control select2" style="width: 100%;" name="otp_awb">
                                    <option value="" selected="selected" disabled>OTP&AWB</option>
                                    <option value="1">yes</option>
                                    <option value="0">no</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="flex items-center justify-center pt-16">
                        <button type="button" class="p-3 !px-20 !text-xl text-white rounded-md bg-blue1"
                            id="save-integration-btn">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
