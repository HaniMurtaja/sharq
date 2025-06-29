@extends('admin.layouts.app')

@section('content')

    <div class="flex flex-col p-6">

        <!-- Drawer Overlay -->
        <div id="drawer-overlay" data-drawer="Vehicles"
            class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 drawer-overlay"></div>


        <!-- Vehicles Drawer -->
        <div id="Vehicles" data-drawer="Vehicles"
            class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">

            <div class="flex flex-col h-screen overflow-scroll">
                <div class="flex items-center justify-between mb-6">
                    <h5 class="text-xl font-bold text-blue-gray-700">New Vehicle</h5>
                    <button id="close-drawer" class="text-gray-500 close-drawer" data-drawer="Users">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col gap-2 p-8">
                    <form id="regForm" style="margin-top: 0px">

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <!-- Profile Image -->
                            <div class="flex flex-col items-center justify-center col-span-2 mb-4">
                                <label
                                    class="flex items-center justify-center w-20 h-20 bg-gray-200 bg-center bg-cover rounded-full"
                                    for="file-upload" id="upload-label">
                                    <input type="file" class="hidden" id="file-upload" accept="image/*"
                                        name="profile_image" />
                                    <svg height="48" viewBox="0 0 48 48" width="48"
                                        xmlns="http://www.w3.org/2000/svg" id="user-icon">
                                        <path
                                            d="M24 8c-4.42 0-8 3.58-8 8 0 4.41 3.58 8 8 8s8-3.59 8-8c0-4.42-3.58-8-8-8zm0 20c-5.33 0-16 2.67-16 8v4h32v-4c0-5.33-10.67-8-16-8z"
                                            fill="gray" <!-- Set color here -->
                                            />
                                            <path d="M0 0h48v48h-48z" fill="none" />
                                    </svg>
                                </label>
                                <span>Vehicle image</span>
                            </div>

                            <!-- <div class="flex justify-between">
                                  <label class="block mb-2 text-black1">Auto Dispatch</label>
                                  <div class="switch-container">
                                      <input type="checkbox" id="switch-auto-dispatch" class="switch-checkbox" />
                                      <label for="switch-auto-dispatch" class="switch-label">
                                          <span class="switch-button"></span>
                                      </label>
                                  </div>
                              </div>

                            
                              <div class="flex justify-between">
                                  <label class="block mb-2 text-black1">Integration</label>
                                  <div class="switch-container">
                                      <input type="checkbox" id="switch-integration" class="switch-checkbox" />
                                      <label for="switch-integration" class="switch-label">
                                          <span class="switch-button"></span>
                                      </label>
                                  </div>
                              </div> -->

                            <!-- Name -->
                            <div>
                                <label for="name">Name</label>
                                <input type="text" id="name" placeholder="Name"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type">Type</label>
                                <select
                                    class="form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                    style="width: 100%;" name="type" id="type">
                                    <option value="" selected="selected" disabled>Type </option>
                                    <option value="1">test</option>
                                </select>
                            </div>

                            <!-- Phone number -->
                            <div>
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" placeholder="Phone Number"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                            </div>

                            <!-- VIN number -->
                            <div>
                                <label for="vin-number">VIN Number</label>
                                <input type="text" id="vin-number" placeholder="VIN Number"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                            </div>


                            <!-- Make -->
                            <div>
                                <label for="Make">Make</label>
                                <input type="text" id="Make" placeholder="Make"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                            </div>

                            <!-- Model -->
                            <div>
                                <label for="Model">Model</label>
                                <input type="text" id="Model" placeholder="Model"
                                    class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="year">Year</label>
                                <select
                                    class="form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                    style="width: 100%;" name="year" id="year">
                                    <option value="" selected="selected" disabled>Year </option>
                                    <option value="1">test</option>
                                </select>
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="Color">Color</label>
                                <select
                                    class="form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                    style="width: 100%;" name="Color" id="Color">
                                    <option value="" selected="selected" disabled>Color</option>
                                    <option value="1">test</option>
                                </select>
                            </div>

                            <div class="w-full col-span-2 file-upload-container">
                                <label class="flex flex-col w-full gap-2">
                                    <span>ID card photo</span>
                                    <div
                                        class="flex items-center justify-center w-full h-12 text-gray-600 border border-gray-800 border-dashed rounded-md cursor-pointer bg-gray4">
                                        <span>Upload Image</span>
                                    </div>
                                    <input type="file" class="hidden" name="license_front_image"
                                        id="imgInpFrontLicence" accept="image/*">
                                </label>
                            </div>

                            <div class="grid col-span-2 gap-5 grid-col-1 md:grid-cols-2">
                                <div class="col-span-2">
                                    <h4 class="text-xl font-bold">Milage info</h4>
                                </div>
                                <div>
                                    <label for="vehicle-milage">Vehicle Milage</label>
                                    <input type="text" id="vehicle-milage" placeholder="Vehicle Milage"
                                        class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                                </div>

                                <div>
                                    <label for="last-service-milage">Last Service Milage</label>
                                    <input type="text" id="last-service-milage" placeholder="Last Service Milage"
                                        class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                                </div>

                                <div>
                                    <label for="due-service-milage">Due Service Milage</label>
                                    <input type="text" id="due-service-milage" placeholder="Due Service Milage"
                                        class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                                </div>

                                <div>
                                    <label for="service-milage-limit">Service Milage Limit</label>
                                    <input type="text" id="service-milage-limit" placeholder="Service Milage Limit"
                                        class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" />
                                </div>
                            </div>



                            <div class="flex items-center justify-end col-span-2">
                                <button class="p-3 !px-20 !text-base text-white rounded-md bg-blue1"
                                    id="save-operator-btn">
                                    Save
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- End Users Drawer -->


        <div class="flex justify-end mb-3">
            <button type="button" data-tab="Vehicles" data-drawer="Vehicles"
                class="flex items-center justify-center w-full h-12 gap-3 px-4 py-2 text-white rounded-md operator_btns open-drawer md:w-48 bg-blue1 border-blue1">
                <img src="{{ asset('new/src/assets/icons/add-square.svg') }}" alt="" />
                <span>New</span>
            </button>
        </div>

        <div class="p-4 bg-white border rounded-lg border-gray1">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-4 border-b md:flex-row md:justify-between">
                <div class="flex flex-col mb-3">
                    <h3 class="mb-2 text-base font-medium text-black">
                        Vehicles
                    </h3>
                    <p class="text-xs text-gray6">200 vehicles</p>
                </div>
              
            </div>

            <div class="w-full overflow-x-auto">
                <table id="vehicles-table" class="w-full text-sm text-left text-gray-700 lg:table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">ID</th>

                            <th class="px-4 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

        
    </div>

    @include('admin.pages.vehicles.scripts')
@endsection
