<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="auto_dispatch"
    id="auto_dispatch">
    <h2 class="mb-6 text-base font-medium">Auto Dispatch Settings</h2>
    <form action="{{ route('save-auto-dispatch') }}" method="post">
        @csrf

        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-1 lg:grid-cols-2">

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Auto dispatch</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-auto-dispatch1" class="switch-checkbox"
                        value="{{ isset($settings->auto_dispatch['auto_dispatch']) ? $settings->auto_dispatch['auto_dispatch'] : 0 }}"
                        name="auto_dispatch"
                        {{ old('auto_dispatch', isset($settings->auto_dispatch['auto_dispatch']) ? $settings->auto_dispatch['auto_dispatch'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-auto-dispatch1" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div>
                <label>Dispatch radius in KM</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ isset($settings->auto_dispatch['dispatch_radius']) ? $settings->auto_dispatch['dispatch_radius'] : '' }}"
                    name="dispatch_radius" class="form-control" id="firstName">
                @error('dispatch_radius')
                    <span style="color: red">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label>Dispatch driver priority</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ isset($settings->auto_dispatch['dispatch_driver_priority']) ? $settings->auto_dispatch['dispatch_driver_priority'] : '' }}"
                    name="dispatch_driver_priority" class="form-control" id="lastName">
                @error('dispatch_driver_priority')
                    <span style="color: red">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label>Number of dispatching rounds</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ isset($settings->auto_dispatch['dispatching_rounds_no']) ? $settings->auto_dispatch['dispatching_rounds_no'] : '' }}"
                    name="dispatching_rounds_no" class="form-control" id="dispatching_rounds_no">
                @error('dispatching_rounds_no')
                    <span style="color: red">{{ $message }}</span>
                @enderror
            </div>

            <div>

            </div>

            <div class="flex justify-between">
                <label class="block mb-2 text-black1">Notify failed dispatching orders</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-notify-failed-dispatching-orders" class="switch-checkbox"
                        value="{{ isset($settings->auto_dispatch['notify_failed_dispatching_orders']) ? $settings->auto_dispatch['notify_failed_dispatching_orders'] : 0 }}"
                        name="notify_failed_dispatching_orders"
                        {{ old('notify_failed_dispatching_orders', isset($settings->auto_dispatch['notify_failed_dispatching_orders']) ? $settings->auto_dispatch['notify_failed_dispatching_orders'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-notify-failed-dispatching-orders" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between">
                <label class="block mb-2 text-black1">Enable clubbing</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-enable-clubbing" class="switch-checkbox"
                        value="{{ isset($settings->auto_dispatch['enable_clubbing']) ? $settings->auto_dispatch['enable_clubbing'] : 0 }}"
                        name="enable_clubbing"
                        {{ old('enable_clubbing', isset($settings->auto_dispatch['enable_clubbing']) ? $settings->auto_dispatch['enable_clubbing'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-enable-clubbing" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div>
                <label>Maximum driver orders</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    id="max_driver_orders" name="max_driver_orders" placeholder="" max="59" min="0"
                    value="{{ $settings->max_driver_orders }}">
                @error('max_driver_orders')
                    <div style="color: red">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label>Clubbing by</label>
                <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                    name="clubbing_by" style="width: 100%;">
                    <option value="" selected="selected" disabled>Clubbing by</option>
                    <option value="Welcome Message"
                        {{ isset($settings->auto_dispatch['clubbing_by']) && $settings->auto_dispatch['clubbing_by'] == 'Welcome Message' ? 'selected' : '' }}>
                        Welcome Message</option>
                    <!-- Add other options as needed -->
                </select>
                @error('clubbing_by')
                    <span style="color: red">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Dispatch to service providers</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-8884" class="switch-checkbox"
                        value="{{ isset($settings->auto_dispatch['dispatch_service_providers']) ? $settings->auto_dispatch['dispatch_service_providers'] : 0 }}"
                        name="dispatch_service_providers"
                        {{ old('dispatch_service_providers', isset($settings->auto_dispatch['dispatch_service_providers']) ? $settings->auto_dispatch['dispatch_service_providers'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-8884" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div>
                <label>Auto dispatch per city</label>
                <select
                    class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem] select2 days"
                    multiple="multiple" name="auto_dispatch_per_city[]" style="width: 100%;">
                    @foreach ($cities as $city)
                        <option @if (in_array($city->id, $settings->auto_dispatch_per_city)) selected @endif value="{{ $city->id }}">
                            {{ $city->name }} </option>
                    @endforeach
                </select>
                @error('clients')
                    <div style="color: red">{{ $message }}</div>
                @enderror
            </div>
            <br>

            <div class="col-span-2">
                <label>Max distance per city</label>
                <div id="repeater">
                    <div class="flex flex-col gap-3">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <select
                                    class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem] select2"
                                    name="max_distance_per_city[0][city]" style="width: 100%;">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">
                                            {{ $city->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex">
                                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                    name="max_distance_per_city[0][distance]" />
                            </div>
                            <div class="flex items-center gap-4">
                                <button type="button"
                                    class="flex items-center btn-add-cities justify-center w-8 h-8 text-xl text-blue-400 border rounded-full outline-none min-w-8 min-h-8 focus:outline-none">
                                    <i class="text-sm fas fa-plus"></i>
                                </button>
                                <button type="button"
                                    class="flex items-center btn-delete justify-center w-8 h-8 text-xl border rounded-full outline-none min-w-8 min-h-8 text-mainColor focus:outline-none">
                                    <i class="text-sm far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>




                    </div>
                </div>

            </div>

        </div>


        <!-- Action Buttons -->
        <div class="flex flex-col justify-center gap-3 mt-6 md:flex-row">

            <button type="submit" class="px-16 py-3 mr-4 font-bold text-white rounded-md border-gray1 bg-blue1">
                Save
            </button>
        </div>
    </form>
</div>




<script>
    $(document).ready(function() {
        let rowIndex = 0;

        function updateDeleteButtons(repeaterId) {
            const repeater = document.getElementById(repeaterId);
            const rows = repeater.querySelectorAll('.row');
            rows.forEach(function(row) {
                const deleteButton = row.querySelector('.btn-delete');
                deleteButton.disabled = rows.length === 1;
            });
        }

        function addRow(repeaterId, city = "", distance = "") {
            console.log('tst');

            rowIndex++;
            const repeater = document.getElementById(repeaterId);
            console.log(repeater);

            const newRow = document.createElement('div');
            newRow.classList.add('flex', 'flex-col', 'gap-3');
            console.log(newRow);

            newRow.innerHTML = `

                         <div class="grid grid-cols-3 gap-4">
                            <div>
                                <select
                                    class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem] select2"
                                   name="max_distance_per_city[${rowIndex}][city]" style="width: 100%;">
                                     @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" ${city == '{{ $city->id }}' ? 'selected' : ''}>
                                        {{ $city->name }}
                                    </option>
                            @endforeach
                                </select>
                            </div>
                            <div class="flex">
                                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                    name="max_distance_per_city[${rowIndex}][distance]" value="${distance}" />
                            </div>
                            <div class="flex items-center gap-4">
                                <button type="button"
                                    class="flex items-center btn-add-cities justify-center w-8 h-8 text-xl text-blue-400 border rounded-full outline-none min-w-8 min-h-8 focus:outline-none">
                                    <i class="text-sm fas fa-plus"></i>
                                </button>
                                <button type="button"
                                    class="flex items-center btn-delete justify-center w-8 h-8 text-xl border rounded-full outline-none min-w-8 min-h-8 text-mainColor focus:outline-none">
                                    <i class="text-sm far fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>


              
            `;

            repeater.appendChild(newRow);
            console.log(repeater.appendChild(newRow));

            $('.select2').select2({
                allowClear: true
            });

            newRow.querySelector('.btn-add-cities').addEventListener('click', function() {

                addRow(repeaterId);
                updateDeleteButtons(repeaterId);
            });
            newRow.querySelector('.btn-delete').addEventListener('click', function() {
                newRow.remove();
                updateDeleteButtons(repeaterId);
            });

            updateDeleteButtons(repeaterId);
        }

        function initializeRepeater(data) {
            const repeater = document.getElementById('repeater');
            repeater.innerHTML = ''; // Clear the initial row if data exists

            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    const item = data[key];
                    addRow('repeater', item.city, item.distance);
                }
            }
        }

        document.querySelectorAll('.btn-add-cities').forEach(function(button) {

            button.addEventListener('click', function() {
                console.log('clicked');

                const repeaterId = button.closest('[id^="repeater"]').id;
                addRow(repeaterId);
                updateDeleteButtons(repeaterId);
            });
        });

        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function() {
                const row = button.closest('.row');
                row.remove();
                updateDeleteButtons('repeater');
            });
        });

        // Load existing settings data into the repeater
        const settingsData = {!! json_encode($settings->max_distance_per_city) !!};
        if (settingsData && Object.keys(settingsData).length > 0) {
            initializeRepeater(settingsData);
        } else {
            updateDeleteButtons('repeater');
        }
    });
</script>
