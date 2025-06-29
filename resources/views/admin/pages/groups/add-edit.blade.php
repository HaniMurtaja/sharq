<div id="group-drawer" data-drawer="Groups"
        class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">
        <div class="flex flex-col h-screen overflow-scroll">
            <div class="flex items-center justify-between mb-6">
                <h5 class="text-xl font-bold text-blue-gray-700" id="title-group">
                    New Group
                </h5>
                <button id="close-drawer" class="text-gray-500 close-drawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-2 p-8 overflow-scroll">
                <form id="group-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid w-full gap-8 mt-5 lg:grid-cols-2">
                        <!-- Detail -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Detail</span>

                            <input type="text"
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                name="group_name" placeholder="Group Name">
                            <input hidden name="group_id" id="group_id">
                            <span class="text-danger" id="group_name_error"></span>
                        </label>
                        <!-- Minimum free per order -->
                        <label class="flex flex-col w-full gap-2">
                            <span>Minimum free per order</span>

                            <input type="text" placeholder=""
                                class="w-full h-12 p-2 px-4 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor"
                                name="min_feed_order">
                            <span class="text-danger" id="min_feed_order_error"></span>
                        </label>

                        <!-- Feeds per order -->
                        <div class="form-group !mb-0 gap-2">
                            <span>Feeds per order</span>
                            <div class="mt-2 custom-select2-search">
                                <select
                                    class="w-full p-2 h-12 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city"
                                    name="type_feed_order">
                                    <option value="" selected="selected" disabled>Type</option>
                                    @foreach (App\Enum\FeedType::cases() as $feedType)
                                        <option value="{{ $feedType->value }}">{{ $feedType->getLabel() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>


                    <div class="flex flex-col w-full mt-8">
                        <h3 class="mb-2 text-xl font-medium">Condition</h3>
                        <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-2">
                            <!-- Emergency contact name -->
                            <label class="flex flex-col w-full col-span-2 gap-2">
                                <span>Add % To Consider The Percentage Of Delivery Fee</span>
                                <div id="repeater">
                                    <div class="grid w-full grid-cols-[1fr_1fr_1fr_1fr_40px] gap-2">

                                        <select
                                            class="w-full p-2 h-12 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city"
                                            name="type[]">
                                            <option value="Between" selected="selected">Between</option>
                                            <option value=">">></option>
                                            <option value="<">
                                                < </option>
                                        </select>



                                        <input type="text" placeholder="From" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <input type="text" placeholder="To" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <input type="text" placeholder="Percentage" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <div class="flex items-center justify-center">
                                            <button type="button" class="text-xl add-row">+</button>
                                            <button type="button" class="text-xl text-red-600 delete-row hidden">×</button>

                                        </div>
                                    </div>
                                </div>



                            </label>

                            <!-- Feeds per order -->
                            <div class="form-group !mb-0 gap-2">
                                <span>Additional feeds per order</span>
                                <div class="mt-2 custom-select2-search">
                                    <select
                                        class="w-full p-2 h-12 !mt-2 bg-white border !border-gray-300 rounded-md shadow-sm outline-none form-control select2 city" name="additional_type_feed">
                                        <option value="" selected="selected" disabled>Type</option>
                                        @foreach (App\Enum\FeedType::cases() as $feedType)
                                            <option value="{{ $feedType->value }}">{{ $feedType->getLabel() }}</option>
                                        @endforeach
                                    </select>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="flex flex-col w-full mt-8">
                        <h3 class="mb-2 text-xl font-medium">Condition</h3>
                        <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-2">
                            <!-- Emergency contact name -->

                            <label class="flex flex-col w-full col-span-2 gap-2">
                                <span>Add % To Consider The Percentage Of Delivery Fee</span>
                                <div id="repeater2">
                                    <div class="grid w-full grid-cols-[1fr_1fr_1fr_1fr_40px] gap-2">
                                        <input type="text" placeholder="" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <input type="text" placeholder="From" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <input type="text" placeholder="To" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <input type="text" placeholder="Percentage" id="" name=""
                                            class="w-full h-12 p-2 px-2 border rounded-lg border-gray1 focus:outline-none focus:border-mainColor" />
                                        <div class="flex items-center justify-center">
                                            <button type="button" class="text-xl">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </label>



                        </div>
                    </div>

                </form>


               

                <div class="flex items-center justify-center pt-16">
                    <button type="button" class="p-3 !px-20 text-xl text-white rounded-md bg-blue1"
                    id="save-group-btn" >Save</button>
                </div>
            </div>
        </div>
    </div>