 <!-- Clients -->
 <div class="client-tab-content" data-tab="Clients">
    <!-- Navigation Tabs -->
    <div class="flex flex-col items-center mb-4 border-b md:flex-row md:justify-between">
        <div class="flex flex-col mb-3">
            <h3 class="mb-2 text-base font-medium text-black">
                Clients
            </h3>
            <p class="text-xs text-gray6">200 Clients</p>
        </div>
        <!-- Search -->
        <div
            class="flex items-center justify-start gap-3 p-3 px-4 mb-3 bg-white border rounded-full md:w-96 border-gray1">
            <!-- Icon -->
            <svg width="22" height="23" viewBox="0 0 22 23" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M10.5416 3.02075C6.11186 3.02075 2.52081 6.6118 2.52081 11.0416C2.52081 15.4714 6.11186 19.0624 10.5416 19.0624C14.9714 19.0624 18.5625 15.4714 18.5625 11.0416C18.5625 6.6118 14.9714 3.02075 10.5416 3.02075ZM1.14581 11.0416C1.14581 5.85241 5.35247 1.64575 10.5416 1.64575C15.7308 1.64575 19.9375 5.85241 19.9375 11.0416C19.9375 16.2308 15.7308 20.4374 10.5416 20.4374C5.35247 20.4374 1.14581 16.2308 1.14581 11.0416Z"
                    fill="#A30133" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M17.8472 18.3471C18.1157 18.0786 18.551 18.0786 18.8194 18.3471L20.6528 20.1804C20.9213 20.4489 20.9213 20.8842 20.6528 21.1527C20.3843 21.4212 19.949 21.4212 19.6805 21.1527L17.8472 19.3194C17.5787 19.0509 17.5787 18.6156 17.8472 18.3471Z"
                    fill="#A30133" />
            </svg>

            <!-- Input -->
            <input type="text" placeholder="Search here..." class="outline-none" />
        </div>
    </div>
    <!-- Table -->
    <div class="w-full overflow-x-auto">
        <table class="w-full text-sm  text-gray-700 lg:table-fixed" id="clients-table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-32 px-4 py-3 font-medium md:w-1/5">
                        Name
                    </th>
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">Total Order</th>
                    <th class="px-4 py-3 font-medium">Total Balance</th>
                    <th class="px-4 py-3 font-medium">Country</th>
                    <th class="px-4 py-3 font-medium">City</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody >
                

                <!-- Additional rows as needed -->
            </tbody>
        </table>
    </div>
</div>