<div id="shift-drawer" data-drawer="Shifts"
        class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">
        <div class="flex flex-col h-screen overflow-scroll">
            <div class="flex items-center justify-between mb-6">
                <h5 class="text-xl font-bold text-blue-gray-700" id="title">
                    New Shift
                </h5>
                <button id="close-drawer" class="text-gray-500 close-drawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-2 p-8 overflow-scroll">
                <form id="shift-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col">
                        <div class="grid w-full gap-8 mt-5 lg:grid-cols-2">
                            <!-- Shift Name -->
                            <label class="flex flex-col w-full col-span-2 gap-2">
                                <span>Shift Name</span>

                                <input type="text" placeholder="Shift Name" id="shift_name" name="shift_name"
                                    class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />

                                    <span class="text-danger" id="shift_name_error"></span>

                                <input type="text" hidden id="shift_id" name="shift_id"
                                    class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />


                            </label>
                            <!-- From -->
                            <div class="form-group !mb-0 gap-2">
                                <span>From</span>
                                <div class="mt-2 custom-select2-search">
                                    <select id="shift_from" name="shift_from"
                                        class="w-full p-2 !mt-2 bg-white border border-gray-300 rounded-md shadow-sm outline-none form-control select2">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ isset($shift) && $shift->from == $i ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                    </select>
                                    <span class="text-danger" id="shift_from_error"></span>
                                </div>
                            </div>
                            <!-- AM / PM -->
                            <div class="form-group !mb-0 gap-2">
                                <span>AM / PM</span>
                                <div class="mt-2 custom-select2-search">
                                    <select id="shift_from_type" name="shift_from_type"
                                        class="w-full p-2 h-12 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city">
                                        <option value="PM"
                                            {{ isset($shift) && $shift->from_type == 'PM' ? 'selected' : '' }}>PM</option>
                                        <option value="AM"
                                            {{ isset($shift) && $shift->from_type == 'AM' ? 'selected' : '' }}>AM</option>
                                    </select>
                                    <span class="text-danger" id="shift_from_type_error"></span>
                                </div>
                            </div>

                            <!-- To -->
                            <div class="form-group !mb-0 gap-2">
                                <span>To</span>
                                <div class="mt-2 custom-select2-search">
                                    <select id="shift_to" name="shift_to"
                                        class="w-full p-2 h-12 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ isset($shift) && $shift->to == $i ? 'selected' : '' }}>
                                                {{ $i }}</option>
                                        @endfor
                                       
                                    </select>
                                    <span class="text-danger" id="shift_to_error"></span>
                                </div>
                            </div>

                            <!-- AM / PM -->
                            <div class="form-group !mb-0 gap-2">
                                <span>AM / PM</span>
                                <div class="mt-2 custom-select2-search">
                                    <select id="shift_to_type" name="shift_to_type"
                                        class="w-full p-2 h-12 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city">
                                        <option value="PM"
                                            {{ isset($shift) && $shift->to_type == 'PM' ? 'selected' : '' }}>PM</option>
                                        <option value="AM"
                                            {{ isset($shift) && $shift->to_type == 'AM' ? 'selected' : '' }}>AM</option>
                                    </select>
                                    <span class="text-danger" id="shift_to_type_error"></span>
                                </div>
                            </div>

                        </div>
                    </div>


                </form>


              

                <div class="flex items-center justify-center pt-16">
                    <button type="button" class="p-3 !px-20 text-xl text-white rounded-md bg-blue1"
                    id="save-shift-btn" >Save</button>
                </div>
            </div>
        </div>
    </div>