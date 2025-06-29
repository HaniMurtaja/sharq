<style>
    .order_items {
        scrollbar-width: thin;
    }
</style>
@foreach (@\App\Enum\OrderStatus::GetStatus() as $key => $label_class)
    <div class="collapsible">
        <button type="button" style="background-color: #fff"
                class="flex items-center justify-between w-full px-2 mt-2 py-2 text-sm  rounded-md toggleButton collapseCustomBtn"
                data-status="{{ $key }}">
            <div class="flex items-center gap-2">

                <span class="w-0.5 h-6 {{ $label_class }}"></span>
                <span class="">{{ trans('dispatcher.' . $key) }} (<span class="{{ $key }}"></span>)</span>
            </div>
            <span class="d-flex gap-1 align-items-center">
                <span class="delayedCount">
                    {{--                    56 Delayed --}}
                </span>
                <svg width="11.2px" height="5.6px" viewBox="0 0 14 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M1.19037 0.436467C1.48327 0.143574 1.95814 0.143574 2.25103 0.436467L6.5977 4.78313C6.81814 5.00357 7.18327 5.00357 7.40371 4.78313L11.7504 0.436467C12.0433 0.143574 12.5181 0.143574 12.811 0.436467C13.1039 0.72936 13.1039 1.20423 12.811 1.49713L8.46437 5.84379C7.65814 6.65002 6.34327 6.65002 5.53704 5.84379L1.19037 1.49713C0.89748 1.20423 0.89748 0.72936 1.19037 0.436467Z"
                          fill="#949494"></path>
                </svg>
            </span>
        </button>

       


        <div style="background-color: #fff; margin: 0 !important;position: relative;top: -3px; border-radius: 0 0 6px 6px; max-height:507px "
             data-status="{{ $key }}"
             class=" overflow-hidden collapseContent-{{ $key }} text-sm transition-all duration-500 ease-in-out collapseContent max-h-0 md:text-base">
            <div class="flex flex-col order_items"
                 style="width: 96%;
            margin: auto !important;height:507px;overflow-y:scroll;">



            </div>
        </div>

    </div>
@endforeach
