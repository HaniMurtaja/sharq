<div wire:poll.3s>





    <div class="collapsible">
        <button type="button"
            class="flex items-center justify-between w-full px-3 py-3 mt-3 text-sm border rounded-md toggleButton border-gray1 text-black1 bg-gray8">
            <div class="flex items-center gap-4">
                <span class="w-0.5 h-6 bg-gray7"></span>
                <span>Busy ({{ $busy_drivers }})</span>
            </div>
            <span>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.92 8.94995L13.4 15.47C12.63 16.24 11.37 16.24 10.6 15.47L4.07996 8.94995"
                        stroke="#A30133" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        </button>
        <div class="mt-2 overflow-hidden text-sm transition-all duration-500 ease-in-out collapseContent max-h-0 md:text-base"
            wire:ignore.self>
            <div class="flex flex-col gap-3 px-4 border rounded shadow-md bg-gray-50 border-gray1">
                <table class="table table-borderless">
                    <thead>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            <tr class="w-100 border-none" data-popup="{{ $driver['infoWindowContent'] }}"
                                onclick="openDriverPopup(this, {{ $driver['lat'] }},{{ $driver['lng'] }})">

                                <td
                                    class="d-flex align-items-center justify-content-between gap-3 bg-white rounded-2 my-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 32px; height: 32px;">


                                            <img src="{{ $driver['profile_image'] ? $driver['profile_image'] : 'https://fakeimg.pl/300/' }}"
                                                alt="" class="w-100 h-100 rounded-full">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <p class="m-0 " style="font-size: 12.8px">{{ $driver['full_name'] }}</p>
                                            <p class="m-0 " style="font-size: 9.6px">{{ $driver['phone'] }}</p>
                                        </div>

                                    </div>
                                    <div
                                        style="width:19px ; height: 19px; background: #f9f9f9; border-radius: 50%; display: flex; justify-content: center; align-items: center">
                                        <span>{{ $driver['tasks'] }}</span>
                                    </div>
                                </td>


                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>

                    </tfoot>

                </table>
                {{ $drivers->links() }}
            </div>
        </div>
    </div>
</div>
