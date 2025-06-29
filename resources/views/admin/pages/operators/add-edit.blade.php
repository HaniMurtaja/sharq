<div id="drawer" data-drawer="Individuals"
    class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">
    <div class="flex flex-col h-screen overflow-scroll">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700">
                New Individual
            </h5>
            <button id="close-drawer" class="text-gray-500 close-drawer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 p-8 overflow-scroll">
            <form id="operator-form" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="flex flex-col">
                    <h3 class="mb-4 text-xl font-medium">Information</h3>
                    <label
                        class="flex items-center justify-center w-20 h-20 bg-gray-200 bg-center bg-cover rounded-full"
                        for="file-upload" id="upload-label">
                        <input type="file" class="hidden" id="file-upload" accept="image/*" name="profile_image" />
                        <svg height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"
                            id="user-icon">
                            <path
                                d="M24 8c-4.42 0-8 3.58-8 8 0 4.41 3.58 8 8 8s8-3.59 8-8c0-4.42-3.58-8-8-8zm0 20c-5.33 0-16 2.67-16 8v4h32v-4c0-5.33-10.67-8-16-8z"
                                fill="gray" <!-- Set color here -->
                                />
                                <path d="M0 0h48v48h-48z" fill="none" />
                        </svg>
                    </label>
                    <div class="grid w-full gap-8 mt-5 lg:grid-cols-2">
                        <!-- First Name -->
                        <label class="flex flex-col w-full gap-2">
                            <input
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                hidden name="operator_id" id="operator_id">
                            <span>First Name</span>

                            <input type="text" placeholder="" id="firstName" name="first_name"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>
                        <!-- Last Name -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Last Name</span>

                            <input type="text" placeholder="" id="lastName" name="last_name"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>
                        <!-- Phone -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Phone</span>

                            <input type="text" placeholder="" id="phone" name="phone"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>

                        <!-- City -->

                        <div class="form-group !mb-0 gap-2">
                            <span>City</span>
                            <div class="mt-3 custom-select2-search">
                                <select name="city[]" multiple
                                    class="w-full p-3 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <label class="flex flex-col w-full gap-1">
                            <span>Email</span>

                            <input type="text" id="email" name="email"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>

                        <!-- Password -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Password</span>

                            <input type="password" placeholder="" id="password" name="password"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>


                        <!-- Birth Date -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Birth Date</span>

                            <input type="date" placeholder="" name="birth_date" id="birth_date"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>




                    </div>
                </div>

              
                <div class="flex flex-col w-full mt-8">
                    <h3 class="mb-2 text-xl font-medium">More Details</h3>
                    <div class="grid w-full grid-cols-1 gap-8 mt-5 md:grid-cols-2">
                        <!-- Emergency contact name -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Emergency contact name</span>

                            <input type="text" placeholder="" id="emergency_contact_name"
                                name="emergency_contact_name"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>
                        <!-- Emergency contact phone -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Emergency contact phone</span>

                            <input type="text" placeholder="" id="emergency_contact_phone"
                                name="emergency_contact_phone"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>

                        <!-- License Front -->

                        <!-- IBan -->
                        <label class="flex flex-col w-full gap-1">
                            <span>IBan</span>

                            <input type="text" placeholder="" id="iban" name="iban"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>




                        <!-- Password -->



                        <div class="w-full col-span-2 h-72">
                            <input id="pac-input"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                type="text" placeholder="Search Box" />

                            <div wire:ignore id="formMap" style="height: 350px;"></div>

                            <input type="hidden" name="lat" id="lat_order_hidden">
                            <input type="hidden" name="lng" id="long_order_hidden">
                        </div>

                    </div>
                </div>

                <div class="flex flex-col mt-8">
                    <h3 class="mb-2 text-xl font-medium">Group & Shift</h3>
                    <div class="grid grid-cols-1 gap-8 mt-5 lg:grid-cols-2">
                        <!-- Group -->
                        <div class="form-group !mb-0">
                            <span>Group</span>
                            <div class="mt-2 custom-select2-search">
                                <select
                                    class="w-full p-3 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2"
                                    id="operator_group_id" name="group_id">
                                    @foreach ($all_groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="form-group !mb-0">
                            <span>Branch group</span>
                            <div class="mt-2 custom-select2-search">
                                <select name="branch_group_id"
                                    class="w-full p-3 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2">
                                    @foreach ($all_groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group !mb-0">
                            <span>Shifts</span>
                            <div class="mt-2 custom-select2-search">
                                <select id="operator-shift-id" name="shift_id"
                                    class="w-full p-3 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2">
                                    @foreach ($all_shifts as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group !mb-0">
                            <span>Davs off</span>
                            <div class="mt-2 custom-select2">
                                <select multiple="multiple" name="days_off[]"
                                    class="w-full p-3 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2 days">
                                    <option value="Sunday">Sunday</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group !mb-0">
                            <span>Contract Type</span>
                            <div class="mt-2 custom-select2-search">
                                <select name="jop_type" id = "jop_type"
                                    class="w-full p-3 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control  select2">
                                    @foreach (App\Enum\JopType::cases() as $jopType)
                                        <option value="{{ $jopType->value }}">{{ $jopType->getLabel() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <label class="flex flex-col w-full gap-2">
                            <span>Price Per Order</span>

                            <input type="" placeholder="" id="order_value" name="order_value"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        </label>



                        <div class="form-group !mb-0">
                            <span>Vehicle Type</span>
                            <div class="mt-2 custom-select2-search">
                                <select name="car_type" id="car_type"
                                    class="w-full p-2 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2 custom-select-height">
                                    <option></option>
                                    <option value="company">Company Vehicle</option>
                                    <option value="driver">Driver Vehicle</option>
                                </select>
                            </div>
                        </div>


                    </div>
                </div>



                <div id="company-vehicles" style="display: none">
                    <div class="flex flex-col mt-8">

                        <div class="grid grid-cols-1 gap-8 mt-5 lg:grid-cols-2">
                            <!-- Group -->
                            <div class="form-group !mb-0">

                                <div class="mt-2 custom-select2-search">
                                    <select class="form-control select2" id="company_vehicle_id" style="width: 100%;"
                                        name="company_vehicle_id">
                                        <option value="" selected="selected" disabled>Vehicle</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>









                        </div>
                    </div>
                </div>






                <div id="driver-vehicle" style="display: none">

                    @include('admin.pages.operators.driver-vehicle')
                </div>

                <div class="flex items-center justify-center pt-16">
                    <button type="button" class="p-3 !px-20 !text-xl text-white rounded-md bg-blue1"
                        id="save-operator-btn">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
