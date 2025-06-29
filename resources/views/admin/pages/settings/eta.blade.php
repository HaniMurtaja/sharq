<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="eta" id="eta">
    <h2 class="mb-6 text-base font-medium">Eta Settings</h2>
    <form action="{{ route('save-eta-settings') }}" method="post">
        @csrf

        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">

            <div>
                <label class="block mb-2 text-black1">Broadcast delay (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" name="broadcast_delay"
                    value="{{ old('broadcast_delay', $settings->eta['broadcast_delay'] ?? '') }}">
                @error('broadcast_delay')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Acceptance Buffer (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="acceptance_buffer"
                    value="{{ old('acceptance_buffer', $settings->eta['acceptance_buffer'] ?? '') }}">
                @error('acceptance_buffer')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Pickup buffer time (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="pickup_buffer_time"
                    value="{{ old('pickup_buffer_time', $settings->eta['pickup_buffer_time'] ?? '') }}">
                @error('pickup_buffer_time')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Pickup handling time (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="pickup_handling_time"
                    value="{{ old('pickup_handling_time', $settings->eta['pickup_handling_time'] ?? '') }}">
                @error('pickup_handling_time')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Drop-off buffer time (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="dropoff_buffer_time"
                    value="{{ old('dropoff_buffer_time', $settings->eta['dropoff_buffer_time'] ?? '') }}">
                @error('dropoff_buffer_time')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Drop-off handling time (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="dropoff_handling_time"
                    value="{{ old('dropoff_handling_time', $settings->eta['dropoff_handling_time'] ?? '') }}">
                @error('dropoff_handling_time')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-span-2">
                <h2 class="text-base font-medium">Targets</h2>
            </div>


            <div>
                <label class="block mb-2 text-black1">Acceptance time threshold (Sec)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="acceptance_time_threshold"
                    value="{{ old('acceptance_time_threshold', $settings->eta['acceptance_time_threshold'] ?? '') }}">
                @error('acceptance_time_threshold')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Broadcast time before (Min)</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="broadcast_time_before"
                    value="{{ old('broadcast_time_before', $settings->eta['broadcast_time_before'] ?? '') }}">
                @error('broadcast_time_before')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Default arrive to pickup time</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="default_arrive_to_pickup_time"
                    value="{{ old('default_arrive_to_pickup_time', $settings->eta['default_arrive_to_pickup_time'] ?? '') }}">
                @error('default_arrive_to_pickup_time')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Default arrive to drop-off time</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="default_arrive_to_dropoff_time"
                    value="{{ old('default_arrive_to_dropoff_time', $settings->eta['default_arrive_to_dropoff_time'] ?? '') }}">
                @error('default_arrive_to_dropoff_time')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
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
