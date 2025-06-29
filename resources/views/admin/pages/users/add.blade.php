<!-- Drawer Overlay -->
<div id="drawer-overlay" data-drawer="Users" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 drawer-overlay">
</div>


<!-- Users Drawer -->
<div id="usersDrawer" data-drawer="Users"
    class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">

    <div class="flex flex-col h-screen overflow-scroll">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700" id="title-user">New User</h5>
            <button id="close-drawer" class="text-gray-500 close-drawer" data-drawer="Users">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 p-8">

            <form style="margin-top: 0px" id="user-form" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <h3 class="mb-4 text-xl font-medium">Information</h3>

                    <div class="flex justify-between col-span-2">
                        <label class="block mb-2 text-black1">Locked</label>
                        <div class="switch-container">
                            <input type="checkbox" id="locked" name="locked" class="switch-checkbox" />
                            <label for="locked" class="switch-label">
                                <span class="switch-button"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Profile Image -->
                    <div class="flex flex-col justify-start col-span-2">
                        <label
                            class="flex items-center justify-center w-20 h-20 bg-gray-200 bg-center bg-cover rounded-full"
                            for="file-upload" id="upload-label">
                            <input type="file" class="hidden" id="file-upload" accept="image/*"
                                name="profile_photo" />
                            <svg height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"
                                id="user-icon">
                                <path
                                    d="M24 8c-4.42 0-8 3.58-8 8 0 4.41 3.58 8 8 8s8-3.59 8-8c0-4.42-3.58-8-8-8zm0 20c-5.33 0-16 2.67-16 8v4h32v-4c0-5.33-10.67-8-16-8z"
                                    fill="gray" <!-- Set color here -->
                                    />
                                    <path d="M0 0h48v48h-48z" fill="none" />
                            </svg>
                        </label>
                    </div>

                    <!-- First Name -->
                    <div>
                        <label for="first-name">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="First Name"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last-name">Last Name</label>
                        <input type="text" id= "last_name" name="last_name" placeholder="Last Name"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>

                    <!-- Operators Group -->
                    <div>
                        <label for="operators-group">Operators Group</label>
                        <select
                            class="form-control shadow-none custom-select2-search w-full groups border rounded-md border-gray5 h-[2.9rem] px-3 select2"
                            multiple="multiple" style="width: 100%;" name="groups[]">

                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}"> {{ $group->name }} </option>
                            @endforeach

                        </select>
                    </div>
                    <!-- Operators Group -->


                    <div>
                        <label for="mac-address">Country</label>
                        <select
                            class="form-control shadow-none custom-select2-search w-full country_id border rounded-md border-gray5 h-[2.9rem] px-3 select2"
                            style="width: 100%;" id="country_id" name="country_id">
                            <option disabled selected>Select Country</option>
                            @foreach ($countries as $country)
                          
                                <option value="{{ $country->id }}"
                                   >{{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="errorcity_ids"></div>
                    </div>

                    <div>
                        <label for="mac-address">City</label>
                        <select
                            class="form-control shadow-none custom-select2-search w-full city_ids border rounded-md border-gray5 h-[2.9rem] px-3 select2"
                            multiple="multiple" style="width: 100%;" id="city_ids" name="city_ids[]">
                            @foreach ($citys as $name => $ids)
                                <option value="{{ $ids }}"> {{ $name }} </option>
                            @endforeach
                        </select>
                        <div class="errorcity_ids"></div>
                    </div>

                    <div>
                        <label for="operators-group">User Type</label>
                        <select
                            class="form-control shadow-none custom-select2-search user_role w-full groups border rounded-md border-gray5 h-[2.9rem] px-3 select2"
                            style="width: 100%;" name="user_role" id="user_role">

                            <option selected disabled>Select User Type</option>
                            @foreach (App\Enum\UserRole::cases() as $user_role)
                                <option value="{{ $user_role->value }}">{{ $user_role->getLabel() }}</option>
                            @endforeach

                        </select>
                    </div>
                    <!-- Marketplace access -->
                    <div class="flex justify-between col-span-2">
                        <label class="block mb-2 text-black1">Marketplace access</label>
                        <div class="switch-container">
                            <input type="checkbox" id="marketplace_access" name="marketplace_access"
                                class="switch-checkbox" />
                            <label for="marketplace_access" class="switch-label">
                                <span class="switch-button"></span>
                            </label>
                        </div>
                    </div>


                    <!-- Password -->
                    <div>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="***********"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>

                    <div>
                        <label for="operators-group">Access</label>
                        <select
                            class="form-control shadow-none custom-select2-search w-full groups border rounded-md border-gray5 h-[2.9rem] px-3 select2"
                            style="width: 100%;" name="role">
                            <option selected disabled>Grant access to</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}"> {{ $role->name }} </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- MAC Address -->
                    <div>
                        <label for="mac-address">MAC Address</label>
                        <input type="text" id="mac_address" name="mac_address" placeholder="MAC Address"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>

                    <!-- Sim Card -->
                    <div>
                        <label for="sim-card">S/N</label>
                        <input type="text" id="sn" name="sn" placeholder="S/N"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>
                    <input hidden name="user_id" id="user_id">

                    <!-- S/N -->
                    <div>
                        <label for="s-n">Sim Card</label>
                        <input type="text" id="sim_card" name="sim_card"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>


                    <!-- Request per second -->
                    <div>
                        <label for="request-per-second">Request per second</label>
                        <input type="text" id="request_per_second" name="request_per_second"
                            placeholder="Request per second"
                            class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                    </div>


                    <div class="flex items-center justify-end col-span-2">
                        <button type="button" class="p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                            id="save-user-btn">
                            Save
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('.groups').select2({
            placeholder: "Operators Group",
            allowClear: true
        });
    });
    $(document).ready(function() {
        $('.city_ids').select2({
            placeholder: "City",
            allowClear: true
        });
    })
</script>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

<script>
    $(document).ready(function() {
        $('.user_role').on('change', function() {
            if ($(this).val() == '4') {
                $('#city_ids').attr('required', 'required');
            } else {
                $('#city_ids').removeAttr('required');
            }
        });
    });
</script>
