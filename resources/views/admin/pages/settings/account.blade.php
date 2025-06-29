<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="account" id="account">
    <h2 class="mb-6 text-base font-medium">Information</h2>
    <form action="{{ route('save-account') }}" method="post">
        @csrf
        <!-- Information Fields -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div>
                <label class="block mb-2 text-black1">First Name</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ $settings->account['first_name'] }}" name="first_name" id="firstName"
                    placeholder="First Name">
                @include('admin.includes.validation-error', ['input' => 'first_name'])
            </div>
            <div>
                <label class="block mb-2 text-black1">Last Name</label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ $settings->account['last_name'] }}" name="last_name" id="lastName"
                    placeholder="Last Name">
                @include('admin.includes.validation-error', ['input' => 'last_name'])
            </div>
            <div>
                <label class="block mb-2 text-black1">Email </label>
                <input type="email" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ $settings->account['email'] }}" name="email" id="email" placeholder="Email">
                @include('admin.includes.validation-error', ['input' => 'email'])
            </div>
        </div>

        <!-- Billing Detail Fields -->
        <h2 class="mb-6 text-base font-medium">Billing Detail</h2>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
                <label class="block mb-2 text-black1">
                    Billing VAT No.
                </label>
                <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3"
                    value="{{ $settings->account['billing_vAT_no'] }}" name="billing_vAT_no" id="billingVATNo"
                    placeholder="Billing VAT No">
                @include('admin.includes.validation-error', ['input' => 'billing_vAT_no'])
            </div>
        </div>
        <div>
            <label class="block mb-2 text-black1">
                Billing Name
            </label>
            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="billingName"
                name="billing_name" value="{{ $settings->account['billing_name'] }}" placeholder="Billing Name">
            @include('admin.includes.validation-error', ['input' => 'billing_name'])
        </div>
        <div>
            <label class="block mb-2 text-black1">
                Street Name
            </label>
            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" name="street_name"
                value="{{ $settings->account['street_name'] }}" id="streetName" placeholder="Street Name">
            @include('admin.includes.validation-error', ['input' => 'street_name'])
        </div>
        <div>
            <label class="block mb-2 text-black1">
                Billing Building No.
            </label>
            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="billingBuildingNo"
                name="billing_bulding_no" value="{{ $settings->account['billing_bulding_no'] }}"
                placeholder="Billing Building No">
            @include('admin.includes.validation-error', ['input' => 'billing_bulding_no'])
        </div>
        <div>
            <label class="block mb-2 text-black1">
                Billing District
            </label>
            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="billingDistrict"
                value="{{ $settings->account['billing_district'] }}" name="billing_district"
                placeholder="Billing District">
            @include('admin.includes.validation-error', ['input' => 'billing_district'])
        </div>
        <div>
            <label class="block mb-2 text-black1">
                Billing City
            </label>
            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="billingCity"
                name="billing_city" value="{{ $settings->account['billing_city'] }}" placeholder="Billing City">
            @include('admin.includes.validation-error', ['input' => 'billing_city'])
        </div>
        <div>
            <label class="block mb-2 text-black1">
                Billing Email
            </label>
            <input type="text" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="billingEmail"
                name="billing_email" value="{{ $settings->account['billing_email'] }}" placeholder="Billing Email">
            @include('admin.includes.validation-error', ['input' => 'billing_email'])
        </div>


        <div class="flex flex-col justify-center gap-3 mt-6 md:flex-row">

            <button type="submit" class="px-16 py-3 mr-4 font-bold text-white rounded-md border-gray1 bg-blue1">
                Save
            </button>
        </div>
    </form>

    <!-- Action Buttons -->

</div>
