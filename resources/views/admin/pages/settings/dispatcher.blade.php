<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="dispatcher"
                    id="dispatcher">
                    <h2 class="mb-6 text-base font-medium">Dispatcher Page Settings</h2>
                    <form action="{{ route('save-dishpatcher-page') }}" method="post">
                        @csrf

                        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-black1">Orders sorting</label>

                                <select
                                    class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem] select2"
                                    name="orders_sorting" style="width: 100%;">
                                    <option value="" disabled
                                        {{ old('orders_sorting', $settings->dispatcher_page['orders_sorting'] ?? '') === '' ? 'selected' : '' }}>
                                        Orders sorting</option>
                                    <option value="Creation time"
                                        {{ old('orders_sorting', $settings->dispatcher_page['orders_sorting'] ?? '') === 'Creation time' ? 'selected' : '' }}>
                                        Creation time</option>
                                    <option value="Expected delivery"
                                        {{ old('orders_sorting', $settings->dispatcher_page['orders_sorting'] ?? '') === 'Expected delivery' ? 'selected' : '' }}>
                                        Expected delivery</option>
                                </select>
                                @error('orders_sorting')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

                            </div>

                            <div>
                                <label>Orders sorting direction</label>
                                <select
                                    class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]select2"
                                    name="orders_sorting_direction" style="width: 100%;">
                                    <option value="" disabled
                                        {{ old('orders_sorting_direction', $settings->dispatcher_page['orders_sorting_direction'] ?? '') === '' ? 'selected' : '' }}>
                                        Orders sorting direction</option>
                                    <option value="Ascending"
                                        {{ old('orders_sorting_direction', $settings->dispatcher_page['orders_sorting_direction'] ?? '') === 'Ascending' ? 'selected' : '' }}>
                                        Ascending</option>
                                    <option value="Descending"
                                        {{ old('orders_sorting_direction', $settings->dispatcher_page['orders_sorting_direction'] ?? '') === 'Descending' ? 'selected' : '' }}>
                                        Descending</option>
                                </select>
                                @error('orders_sorting_direction')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label>Show "All" filter</label>
                                <select
                                    class="form-control  shadow-none w-full select2 border rounded-md border-gray5 h-[2.9rem]"
                                    name="show_All_filter" style="width: 100%;">
                                    <option value="" disabled
                                        {{ old('show_All_filter', $settings->dispatcher_page['show_All_filter'] ?? '') === '' ? 'selected' : '' }}>
                                        Show "All" filter Off</option>
                                    <option value="On"
                                        {{ old('show_All_filter', $settings->dispatcher_page['show_All_filter'] ?? '') === 'On' ? 'selected' : '' }}>
                                        On</option>
                                    <option value="Off"
                                        {{ old('show_All_filter', $settings->dispatcher_page['show_All_filter'] ?? '') === 'Off' ? 'selected' : '' }}>
                                        Off</option>
                                </select>
                                @error('show_All_filter')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label>The time allowed to accept more than one order</label>
                                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                                    name="time_allowed_accept_more_than_order" max="59" min="0"
                                    value="{{ $settings->time_multi_order_assign }}" />
                                @error('time_allowed_accept_more_than_order')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="flex justify-between col-span-2">
                                <label class="block mb-2 text-black1">Play alert sound for new orders</label>
                                <div class="switch-container">
                                    <input type="checkbox" id="switch122" class="switch-checkbox"
                                    value="{{ isset($settings->dispatcher_page['new_orders_alert_sound']) ? $settings->dispatcher_page['new_orders_alert_sound'] : 0 }}"
                                name="new_orders_alert_sound"
                                {{ old('new_orders_alert_sound', isset($settings->dispatcher_page['new_orders_alert_sound']) ? $settings->dispatcher_page['new_orders_alert_sound'] : 0) == 1 ? 'checked' : '' }}>

                                    <label for="switch122" class="switch-label">
                                        <span class="switch-button"></span>
                                    </label>

                                    @error('new_orders_alert_sound')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
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