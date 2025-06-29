<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="api" id="api">
    <h2 class="mb-6 text-base font-medium">Api Settings</h2>
    <form action="{{route('save-api-settings')}}" method="post">
        @csrf
        <!-- Information Fields -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div>
                <label class="block mb-2 text-black1">API Key</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" name="api_key" value="{{$settings->api_settings['api_key']}}"  >
                @include('admin.includes.validation-error', ['input' => 'api_key'])
            </div>
            <div>
                <label class="block mb-2 text-black1">Max distance to accept in km</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" name="max_distance_accept" value="{{$settings->api_settings['max_distance_accept']}}"   >
                @include('admin.includes.validation-error', ['input' => 'max_distance_accept'])
            </div>
        </div>

        

        <!-- Action Buttons -->
        <div class="flex flex-col justify-center gap-3 mt-6 md:flex-row">
            
            <button type="submit"
                class="px-16 py-3 mr-4 font-bold text-white rounded-md border-gray1 bg-blue1">
                Save
            </button>
        </div>
    </form>
</div>