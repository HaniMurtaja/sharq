<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="service" id="service">
    <h2 class="mb-6 text-base font-medium">Service</h2>
    <form action="{{ route('save-services') }}" method="post">
        @csrf

        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Service hours</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-service-hours55" class="switch-checkbox"
                        value="{{ isset($settings->services['service_hours_toggle']) ? $settings->services['service_hours_toggle'] : 0 }}"
                        name="service_hours_toggle"
                        {{ old('service_hours_toggle', isset($settings->services['service_hours_toggle']) ? $settings->services['service_hours_toggle'] : 0) == 1 ? 'checked' : '' }}>
                    <label for="switch-service-hours55" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div class="col-span-2">
                <label>Open Time</label>
                <div class="grid w-full grid-cols-3 gap-2">
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        id="company_vehicle_id" style="width: 100%;" name="open_hour">
                        <option value="">Hour</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}"
                                {{ old('open_hour', $settings->services['open_hour'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('open_hour')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        id="company_vehicle_id" style="width: 100%;" name="open_minute">
                        <option value="">Minute</option>
                        @for ($i = 0; $i < 60; $i++)
                            <option value="{{ $i }}"
                                {{ old('open_minute', $settings->services['open_minute'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('open_minute')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        id="company_vehicle_id" style="width: 100%;" name="open_period">
                        <option value="AM"
                            {{ old('open_period', $settings->services['open_period'] ?? '') == 'AM' ? 'selected' : '' }}>
                            AM</option>
                        <option value="PM"
                            {{ old('open_period', $settings->services['open_period'] ?? '') == 'PM' ? 'selected' : '' }}>
                            PM</option>
                    </select>
                    @error('open_period')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="col-span-2">
                <label>Close Time</label>
                <div class="grid w-full grid-cols-3 gap-2">
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        id="company_vehicle_id" style="width: 100%;" name="close_hour">
                        <option value="">Hour</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}"
                                {{ old('close_hour', $settings->services['close_hour'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('close_hour')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        id="company_vehicle_id" style="width: 100%;" name="close_minute">
                        <option value="">Minute</option>
                        @for ($i = 0; $i < 60; $i++)
                            <option value="{{ $i }}"
                                {{ old('close_minute', $settings->services['close_minute'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('close_minute')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        id="company_vehicle_id" style="width: 100%;" name="close_period">
                        <option value="AM"
                            {{ old('close_period', $settings->services['close_period'] ?? '') == 'AM' ? 'selected' : '' }}>
                            AM</option>
                        <option value="PM"
                            {{ old('close_period', $settings->services['close_period'] ?? '') == 'PM' ? 'selected' : '' }}>
                            PM</option>
                    </select>
                    @error('close_period')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block mb-2 text-black1">API Key</label>
                <textarea type="text" class="w-full p-2 bg-white border rounded-md border-gray5" rows="3" id="api_key"
                    name="api_key" placeholder="API Key">{{ old('api_key', $settings->services['api_key'] ?? '') }}</textarea>
                @error('api_key')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Operators service hours</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-service-hours" class="switch-checkbox"
                        value="{{ isset($settings->services['operators_service_hours_toggle']) ? $settings->services['operators_service_hours_toggle'] : 0 }}"
                        name="operators_service_hours_toggle"
                        {{ old('operators_service_hours_toggle', isset($settings->services['operators_service_hours_toggle']) ? $settings->services['operators_service_hours_toggle'] : 0) == 1 ? 'checked' : '' }}>
                    <label for="switch-service-hours" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div class="col-span-2">
                <label>Start Time</label>
                <div class="grid w-full grid-cols-3 gap-2">
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        name="start_hour">
                        <option value="">Hour</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}"
                                {{ old('start_hour', $settings->services['start_hour'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('start_hour')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        style="width: 100%;"name="start_minute">
                        <option value="">Minute</option>
                        @for ($i = 0; $i < 60; $i++)
                            <option value="{{ $i }}"
                                {{ old('start_minute', $settings->services['start_minute'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('start_minute')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        name="start_period">
                        <option value="AM"
                            {{ old('start_period', $settings->services['start_period'] ?? '') == 'AM' ? 'selected' : '' }}>
                            AM</option>
                        <option value="PM"
                            {{ old('start_period', $settings->services['start_period'] ?? '') == 'PM' ? 'selected' : '' }}>
                            PM</option>
                    </select>
                    @error('start_period')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="col-span-2">
                <label>End Time</label>
                <div class="grid w-full grid-cols-3 gap-2">
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        name="end_hour">
                        <option value="">Hour</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}"
                                {{ old('end_hour', $settings->services['end_hour'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('end_hour')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        name="end_minute">
                        <option value="">Minute</option>
                        @for ($i = 0; $i < 60; $i++)
                            <option value="{{ $i }}"
                                {{ old('end_minute', $settings->services['end_minute'] ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                    @error('end_minute')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                    <select class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                        name="end_period">
                        <option value="AM"
                            {{ old('end_period', $settings->services['end_period'] ?? '') == 'AM' ? 'selected' : '' }}>
                            AM</option>
                        <option value="PM"
                            {{ old('end_period', $settings->services['end_period'] ?? '') == 'PM' ? 'selected' : '' }}>
                            PM</option>
                    </select>
                    @error('end_period')
                        <span style="color: red">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Service availability</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-5555" class="switch-checkbox"
                        value="{{ isset($settings->services['service_availability_toggle']) ? $settings->services['service_availability_toggle'] : 0 }}"
                        name="service_availability_toggle"
                        {{ old('service_availability_toggle', isset($settings->services['service_availability_toggle']) ? $settings->services['service_availability_toggle'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-5555" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>


            <div>
                <label class="block mb-2 text-black1">Write Your Message</label>
                <textarea type="text" class="w-full p-2 bg-white border rounded-md border-gray5" rows="3"
                    id="write_message" name="write_message">{{ old('write_message', $settings->services['write_message'] ?? '') }}</textarea>
                @error('write_message')
                    <span style="color: red">{{ $message }}</span>
                @enderror
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
