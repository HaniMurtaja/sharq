<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="payment_gateway"
    id="payment_gateway">
    <h2 class="mb-6 text-base font-medium">Payment Gateway Settings</h2>
    <form action="{{ route('save-payment') }}" method="post">
        @csrf
        <!-- Business Hours -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div>
                <label class="block mb-2 text-black1">Payment token</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="paymentToken"
                    name="payment_token"
                    value="{{ old('payment_token', $settings->payment_gateway['payment_token'] ?? '') }}">
                
                    @error('payment_token')
                    <span style="color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div>
                <label class="block mb-2 text-black1">Merchant ID</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="merchantId"
                    name="merchant_id"
                    value="{{ old('merchant_id', $settings->payment_gateway['merchant_id'] ?? '') }}">
                @error('merchant_id')
                    <span style = "color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div>
                <label class="block mb-2 text-black1">Recurring Payment token</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    id="recurringPaymentToken" name="recurring_payment_token"
                    value="{{ old('recurring_payment_token', $settings->payment_gateway['recurring_payment_token'] ?? '') }}">
                @error('recurring_payment_token')
                    <span style = "color: red" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>
            <div>
                <label class="block mb-2 text-black1">Recurring Merchant ID</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    id="recurringMerchantId" name="recurring_merchant_id"
                    value="{{ old('recurring_merchant_id', $settings->payment_gateway['recurring_merchant_id'] ?? '') }}">
                @error('recurring_merchant_id')
                    <span style = "color: red" role="alert">
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
