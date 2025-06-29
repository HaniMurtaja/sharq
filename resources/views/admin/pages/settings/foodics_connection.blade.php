<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="foodics_connection"
    id="foodics_connection">
    <h2 class="mb-6 text-base font-medium">Foodics Connection</h2>
    <form action="{{ route('save-foodics') }}" method="post">
        @csrf
        <!-- Business Hours -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Foodics Connection</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch" class="switch-checkbox"
                        value="{{ isset($settings->foodics_connection['foodics_connection']) ? $settings->foodics_connection['foodics_connection'] : 0 }}"
                        name="foodics_connection"
                        {{ old('foodics_connection', isset($settings->foodics_connection['foodics_connection']) ? $settings->foodics_connection['foodics_connection'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
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
