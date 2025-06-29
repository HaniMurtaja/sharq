
<div id="drawer_area" data-drawer="Areas"
    class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">

    <div class="flex flex-col h-screen overflow-scroll">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700" id="area_title"> New Area </h5>
            <button id="close-drawer" class="text-gray-500 close-drawer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 p-8">
            <form style="margin-top: 0px" id="area-form">
                @csrf

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div class="col-span-2">
                        <div>
                            <label for="branch-group-name">Area Name</label>
                            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                id="area_name" name="area_name" placeholder="Area Name">
                            <input id="area_id" name="area_id" hidden>
                            <span class="text-danger" id="area_name_error"></span>
                        </div>
                    </div>



                    <div class="col-span-2">
                        <div>
                            <label for="branch-group-name">City</label>
                            <select
                                class="form-control shadow-none custom-select2-search w-full groups border rounded-md border-gray5 h-[2.9rem] px-3  select2"
                                id="city_id" name="city_id" style="width: 100%;">
                                <option value="" selected="selected" disabled>City</option>
                                @foreach ($all_cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach

                            </select>
                            <span class="text-danger" id="city_id_error"></span>
                        </div>
                    </div>



                    <div class="flex items-center justify-center col-span-2 mt-3">
                        <button type="button" class="p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                            id="save-area-btn">
                            Save
                        </button>
                    </div>

                </div>





            </form>
        </div>
    </div>
</div>
