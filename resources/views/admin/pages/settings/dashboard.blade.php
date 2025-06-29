<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="dashboard" id="dashboard">
    <h2 class="mb-6 text-base font-medium">Dashboard Page Settings</h2>
    <form action="{{ route('save-dashboard-page') }}" method="post">
        @csrf
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div class="flex justify-between col-span-2">
                <label class="block mb-2 text-black1">Dashboard shows pending orders</label>
                <div class="switch-container">
                    <input type="checkbox" id="switch12" class="switch-checkbox"
                        value="{{ isset($settings->dashboard_page['show_pending_orders']) ? $settings->dashboard_page['show_pending_orders'] : 0 }}"
                        name="show_pending_orders"
                        {{ old('show_pending_orders', isset($settings->dashboard_page['show_pending_orders']) ? $settings->dashboard_page['show_pending_orders'] : 0) == 1 ? 'checked' : '' }} />
                    <label for="switch12" class="switch-label">
                        <span class="switch-button"></span>
                    </label>
                </div>
            </div>

            <div>
                <label>Orders sorting </label>
                <select
                    class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem] select2"
                    name="orders_sorting" style="width: 100%;">
                    <option value="" selected="selected" disabled>Orders sorting
                    </option>
                    <option
                        {{ old('show_All_filter', $settings->dashboard_page['orders_sorting'] ?? '') === 'Created' ? 'selected' : '' }}
                        value="Created">Created</option>
                    <option
                        {{ old('show_All_filter', $settings->dashboard_page['orders_sorting'] ?? '') === 'Completed' ? 'selected' : '' }}
                        value="Completed">Completed</option>

                </select>
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
