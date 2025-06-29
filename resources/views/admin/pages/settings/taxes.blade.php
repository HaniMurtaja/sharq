<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="taxes" id="taxes">
    <h2 class="mb-6 text-base font-medium">Taxes</h2>
    <form action="{{ route('save-taxes') }}" method="post">
        @csrf
        <!-- Business Hours -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div class="col-span-2">
                <label class="block mb-2 text-black1">Income Tax</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" name="income_tax"
                    value="{{ old('income_tax', $settings->taxes['income_tax'] ?? '') }}">
                @error('income_tax')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-span-2">
                <div>
                    <label class="block mb-2 text-black1">Start Date</label>
                    <div class="grid grid-cols-3 gap-3">
                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="income_tax_start_year">
                            <option value="">Year</option>
                            @for ($i = 2024; $i >= 2004; $i--)
                                <option value="{{ $i }}"
                                    {{ old('income_tax_start_year', ($settings->taxes['income_tax_start_year'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('income_tax_start_year')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem] "
                            name="income_tax_start_month">
                            <option value="">Month</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                    {{ old('income_tax_start_month', ($settings->taxes['income_tax_start_month'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('income_tax_start_month')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="income_tax_start_day">
                            <option value="">Day</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}"
                                    {{ old('income_tax_start_day', ($settings->taxes['income_tax_start_day'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('income_tax_start_day')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-span-2">
                <div>
                    <label class="block mb-2 text-black1">End Date</label>
                    <div class="grid grid-cols-3 gap-3">
                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="income_tax_end_year">
                            <option value="">Year</option>
                            @for ($i = 2024; $i >= 2004; $i--)
                                <option value="{{ $i }}"
                                    {{ old('income_tax_end_year', ($settings->taxes['income_tax_end_year'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('income_tax_end_year')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="income_tax_end_month">
                            <option value="">Month</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                    {{ old('income_tax_end_month', ($settings->taxes['income_tax_end_month'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('income_tax_end_month')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="income_tax_end_day">
                            <option value="">Day</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}"
                                    {{ old('income_tax_end_day', ($settings->taxes['income_tax_end_day'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('income_tax_end_day')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-span-2">
                <label class="block mb-2 text-black1">Sales Tax</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" name="sales_tax"
                    value="{{ old('sales_tax', $settings->taxes['sales_tax'] ?? '') }}">
                @error('sales_tax')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-span-2">
                <div>
                    <label class="block mb-2 text-black1">Start Date</label>
                    <div class="grid grid-cols-3 gap-3">
                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="sales_tax_start_year">
                            <option value="">Year</option>
                            @for ($i = 2024; $i >= 2004; $i--)
                                <option value="{{ $i }}"
                                    {{ old('sales_tax_start_year', ($settings->taxes['sales_tax_start_year'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('sales_tax_start_year')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="sales_tax_start_month">
                            <option value="">Month</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                    {{ old('sales_tax_start_month', ($settings->taxes['sales_tax_start_month'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('sales_tax_start_month')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="sales_tax_start_day">
                            <option value="">Day</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}"
                                    {{ old('sales_tax_start_day', ($settings->taxes['sales_tax_start_day'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('sales_tax_start_day')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-span-2">
                <div>
                    <label class="block mb-2 text-black1">End Date</label>
                    <div class="grid grid-cols-3 gap-3">
                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="sales_tax_end_year">
                            <option value="">Year</option>
                            @for ($i = 2024; $i >= 2004; $i--)
                                <option value="{{ $i }}"
                                    {{ old('sales_tax_end_year', ($settings->taxes['sales_tax_end_year'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('sales_tax_end_year')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="sales_tax_end_month">
                            <option value="">Month</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}"
                                    {{ old('sales_tax_end_month', ($settings->taxes['sales_tax_end_month'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('sales_tax_end_month')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <select
                            class="form-control select2 shadow-none w-full border rounded-md border-gray5 h-[2.9rem]"
                            name="sales_tax_end_day">
                            <option value="">Day</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}"
                                    {{ old('sales_tax_end_day', ($settings->taxes['sales_tax_end_day'] ?? '') == $i ? 'selected' : '') }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                        @error('sales_tax_end_day')
                            <span style="color: red" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
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
