<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="dispatching" id="dispatching">
    <h2 class="mb-6 text-base font-medium">Dispatching Settings</h2>
    <form action="{{ route('save-dispatching') }}" method="post">
        @csrf
        <!-- Business Hours -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Auto-Dispatch</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-auto-dispatch888" name="auto_dispatch"
                        {{ old('auto_dispatch', isset($settings->dispatching['auto_dispatch']) ? $settings->dispatching['auto_dispatch'] : 0) == 1 ? 'checked' : '' }}
                        class="switch-checkbox" />
                    <label for="switch-auto-dispatch888" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>
            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Dispatch to Service Providers </label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-dispatch-to-service-providers" class="switch-checkbox"
                        name="dispatch_service_providers"
                        {{ old('dispatch_service_providers', isset($settings->dispatching['dispatch_service_providers']) ? $settings->dispatching['dispatch_service_providers'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-dispatch-to-service-providers" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>
            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Repeat Rounds</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-repeat-rounds" name="repeat_rounds"
                        {{ old('repeat_rounds', isset($settings->dispatching['repeat_rounds']) ? $settings->dispatching['repeat_rounds'] : 0) == 1 ? 'checked' : '' }}
                        class="switch-checkbox" />
                    <label for="switch-repeat-rounds" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>


            <div>
                <label class="block mb-2 text-black1">Number of Rounds</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="number_of_rounds"
                    value="{{ old('number_of_rounds', $settings->dispatching['number_of_rounds'] ?? '') }}">
                @error('number_of_rounds')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Max. km to Dispatch</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    name="max_km_to_dispatch"
                    value="{{ old('max_km_to_dispatch', $settings->dispatching['max_km_to_dispatch'] ?? '') }}">
                @error('max_km_to_dispatch')
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
