<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="business_hours"
                    id="business_hours">
                    <h2 class="mb-6 text-base font-medium">Business Hours</h2>
                    <form action="{{ route('save-business-hours') }}" method="post">
                        @csrf
                        <!-- Business Hours -->
                        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-black1">Start Time</label>
                                <select
                                    class="form-control select2 w-full border rounded-md border-gray5 h-[2.9rem] select2"
                                    name="start_time" style="width: 100%;">
                                    <option value="" disabled
                                        {{ old('start_time', $settings->business_hours['start_time'] ?? '') === '' ? 'selected' : '' }}>
                                        Select Start Time</option>
                                    @for ($hour = 0; $hour <= 23; $hour++)
                                        @for ($minute = 0; $minute <= 59; $minute++)
                                            @php
                                                // Format the minute to always be two digits
                                                $formattedMinute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                                                // Format the hour to always be two digits
                                                $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                                                $time = $formattedHour . ':' . $formattedMinute;
                                            @endphp
                                            <option value="{{ $time }}"
                                                {{ old('start_time', $settings->business_hours['start_time'] ?? '') === $time ? 'selected' : '' }}>
                                                {{ $time }}
                                            </option>
                                        @endfor
                                    @endfor

                                </select>
                                @error('start_time')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-2 text-black1">End Time</label>
                                <select class="form-control select2 w-full border rounded-md border-gray5 h-[2.9rem] select2" name="end_time" style="width: 100%;">
                            <option value="" disabled
                                {{ old('end_time', $settings->business_hours['end_time'] ?? '') === '' ? 'selected' : '' }}>
                                Select End Time</option>
                            @for ($hour = 0; $hour <= 23; $hour++)
                                @for ($minute = 0; $minute <= 59; $minute++)
                                    @php
                                        // Format the minute to always be two digits
                                        $formattedMinute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                                        // Format the hour to always be two digits
                                        $formattedHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                                        $time = $formattedHour . ':' . $formattedMinute;
                                    @endphp
                                    <option value="{{ $time }}"
                                        {{ old('end_time', $settings->business_hours['end_time'] ?? '') === $time ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endfor
                            @endfor

                        </select>
                        @error('end_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                            </div>
                            <div class="flex justify-between col-span-2">
                                <span>The shift will end tomorrow</span>
                                <div class="switch-container">
                                    <input type="checkbox" id="switch-auto-dispatch" class="switch-checkbox" 
                                value="{{ isset($settings->shift_end_tomorrow) ? $settings->shift_end_tomorrow : 0 }}"
                                name="shift_end_tomorrow"
                                {{ old('shift_end_tomorrow', isset($settings->shift_end_tomorrow) ? $settings->shift_end_tomorrow : 0) == 1 ? 'checked' : '' }}>
                                    <label for="switch-auto-dispatch" class="switch-label">
                                        <span class="switch-button"></span>
                                    </label>
                                </div>
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