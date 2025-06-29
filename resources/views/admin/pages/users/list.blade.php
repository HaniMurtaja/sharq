<div class="tab_content" data-tab="Users">
    <div class="p-4 bg-white border rounded-lg border-gray1">
        <!-- Navigation Tabs -->
        <div class="flex flex-col items-center justify-center mb-4 border-b md:flex-row md:justify-between">
            <div class="flex flex-col mb-3">
                <h3 class="mb-2 text-base font-medium text-black">Users</h3>
                <p class="text-xs text-gray6">200 Users</p>
            </div>

        </div>

        <div class="w-full overflow-x-auto">

            <div class="col-md-12 text-end mt-4">
                <form id="exportForm" method="GET" style="display:inline;">
                    @csrf
                    {{-- @method('POST') --}}

                   


                    <div class="flex items-center justify-end col-span-2 md:justify-end">
                        <button class="pxy-828  br-96 bg-red-a3 fs-112 fw-semibold" type="submit">


                            <span>Export</span>
                        </button>
                    </div>
                </form>


            </div>


            <table id="users-table" class="w-full text-sm text-left text-gray-700 lg:table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-medium">First Name</th>
                        <th class="px-4 py-3 font-medium">Last Name</th>
                        <th class="px-4 py-3 font-medium">Email</th>

                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>
