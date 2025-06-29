<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="customer_messages"
    id="customer_messages">
    <h2 class="mb-6 text-base font-medium">Customer Messages</h2>
    <form action="{{ route('save-customer-messages') }}" method="post">
        @csrf

        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">

            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">SMS</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch-sms" class="switch-checkbox"
                        value="{{ isset($settings->customer_messages['sms_enabled']) ? $settings->customer_messages['sms_enabled'] : 0 }}"
                        name="sms_enabled"
                        {{ old('sms_enabled', isset($settings->customer_messages['sms_enabled']) ? $settings->customer_messages['sms_enabled'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch-sms" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div class="col-span-2">
                <div>
                    <label class="block mb-2 text-black1">Triggers</label>
                    <div class="grid grid-cols-3 gap-3">
                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="triggers">
                            <option value="" selected="selected" disabled>Triggers</option>
                            <option
                                {{ isset($settings->customer_messages['triggers']) && $settings->customer_messages['triggers'] == 'Welcome Message' ? 'selected' : '' }}
                                value="Welcome Message">Welcome Message</option>

                        </select>
                        @error('triggers')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror


                    </div>
                </div>
            </div>



            <div>
                <label class="block mb-2 text-black1">Message (Arabic)</label>
                <textarea type="text" class="w-full p-2 bg-white border rounded-md border-gray5" rows="3" name="message_ar">{{ isset($settings->customer_messages['message_ar']) ? $settings->customer_messages['message_ar'] : '' }}</textarea>
                @error('message_ar')
                    <span style="color: red">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block mb-2 text-black1">Message (English)</label>
                <textarea type="text" class="w-full p-2 bg-white border rounded-md border-gray5" rows="3" name="message_en">{{ isset($settings->customer_messages['message_en']) ? $settings->customer_messages['message_en'] : '' }}</textarea>
                @error('message_en')
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
