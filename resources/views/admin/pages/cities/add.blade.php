<!-- <style>
    .form-switch .form-check-input:checked {
    background-color: #a30133 !important ;
    border: none;
}
</style> -->
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<div id="drawer_city" data-drawer="Cities"
    class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">

    <div class="flex flex-col h-screen overflow-scroll">
        <div class="flex items-center justify-between mb-6">
            <h5 class="text-xl font-bold text-blue-gray-700" id="city_title"> New City </h5>
            <button id="close-drawer" class="text-gray-500 close-drawer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col gap-2 p-8">
            <form style="margin-top: 0px" id="city-form">
                @csrf

                <div class="d-flex justify-content-end align-items-center gap-4 create-user-options">
                    <div class=" p-0 form-switch d-flex justify-content-between align-items-center">
                        <label class="form-check-label" for="auto_dispatch">Auto Dispatch</label>
                        <input class="form-check-input position-relative m-0 ml-3" type="checkbox" role="switch"
                            id="auto_dispatch" value="0" name="auto_dispatch">
                    </div>

                </div>


                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div class="col-span-2">
                        <div>
                            <label for="branch-group-name">City Name</label>
                            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                id="city_name" name="city_name" placeholder="City Name">
                            <input id="city_id" name="city_id" hidden>
                            <span class="text-danger" id="city_name_error"></span>
                        </div>
                    </div>



                    <div class="col-span-2">
                        <div>
                            <label for="branch-group-name">Country</label>
                            <select
                                class="form-control shadow-none custom-select2-search w-full groups border rounded-md border-gray5 h-[2.9rem] px-3  select2"
                                id="country_id" name="country_id" style="width: 100%;">
                                <option value="" selected="selected" disabled>Country</option>
                                @foreach ($all_countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach

                            </select>
                            <span class="text-danger" id="country_id_error"></span>
                        </div>
                    </div>



                    <label class="flex flex-col w-full gap-1">


                        <input type="text" name="lat" style="width: 100%" placeholder="Latitude"
                            id="lat_order_hidden"
                            class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        <span class="text-danger" id="lat_error"></span>
                    </label>





                    <label class="flex flex-col w-full gap-1">


                        <input type="text" name="lng" style="width: 100%" placeholder="Longtude"
                            id="long_order_hidden"
                            class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                        <span class="text-danger" id="lng_error"></span>
                    </label>


                    <div class="col-span-2">

                        <input type="text" placeholder="Search Box" id="search-link"
                            class="w-full h-12 p-2 px-4 border rounded-lg controls border-gray1 focus:outline-none focus:border-mainColor">

                        <div wire:ignore id="formMap" style="height: 300px;"></div>
                    </div>


                    <div class="flex items-center justify-center col-span-2 mt-3">
                        <button type="button" class="p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                            id="save-city-btn">
                            Save
                        </button>
                    </div>

                </div>





            </form>
        </div>
    </div>
</div>
