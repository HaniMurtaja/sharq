<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="privacy" id="privacy">
    <h2 class="mb-6 text-base font-medium">Privacy and Security</h2>
    <form action="{{route('save-privacy')}}" method="post">
        @csrf
        <!-- Information Fields -->
        <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-2">
            <div>
                <label class="block mb-2 text-black1">Current Password</label>
                <input type="password" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="" name="current_password" placeholder="Current Password">
                @error('current_password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block mb-2 text-black1">New Password</label>
                <input type="password" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="" name="new_password" placeholder="New Password">
                @error('new_password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block mb-2 text-black1">Password Confirmation</label>
                <input type="password" class="w-full border rounded-md border-gray5 h-[2.9rem] px-3" id="" name="password_confirmation" placeholder="Password Confirmation">
                @error('password_confirmation')
                <div class="text-danger">{{ $message }}</div>
                @enderror
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