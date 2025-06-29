<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="announcements"
    id="announcements">
    <h2 class="mb-6 text-base font-medium">Announcements</h2>
    <form action="{{ route('save-announcements') }}" method="post">
        @csrf
        <!-- Business Hours -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Foodics Connection</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch4848" class="switch-checkbox "
                        {{ isset($settings->announcements['announcements_enabled']) == 1 ? 'checked' : '0' }}
                        value="{{ isset($settings->announcements['announcements_enabled']) ? $settings->announcements['announcements_enabled'] : 0 }}"
                        name="announcements_enabled" />
                    <label for="switch4848" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <p>Message body</p>
                    <textarea type="text" class="form-control" name="announcement_message">{{ isset($settings->announcements['announcement_message']) ? $settings->announcements['announcement_message'] : '' }}</textarea>
                    @error('announcement_message')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
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
