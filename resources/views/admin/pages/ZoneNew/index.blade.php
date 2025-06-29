@extends('admin.layouts.app')


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />

<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />


@section('title')
    Show Zone
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">

        <!-- Table -->
        <div class="">
            <!-- Navigation Tabs -->
            <div class="flex flex-col items-center justify-center mb-192 pb-192 border-b md:flex-row md:justify-between">
                <div class="flex flex-col">
                    <h3 class="text-black fs-192 fw-bold m-0 ">Zone</h3>
                </div>

                <a href="{{ route('ZoneNew.create') }}" class="bg-red-a3 px-4 py-2 text-white br-96 fw-bold  fs-112">+
                    New
                    Zone</a>

            </div>

            <div class="row d-flex gap-3 px-192 mb-176">
                <div class="col-lg-2 col-12 flex  br-64 gap-128 align-items-center  p-128 bg-white ">
                    <div class="flex items-center  border-ce p-2 rounded-5">
                        <img src="{{ asset('new/src/assets/icons/orderCount2.svg') }}" alt="" width="19.2px"
                            height="19.2px" />
                    </div>
                    <div class="flex flex-col">
                        <p class="fs-128 gray-1a fw-bold m-0">
                            {{ $items->total() }}
                        </p>
                        <h4 class="fs-96 fw-bold gray-94 m-0">
                            Zone Count
                        </h4>

                    </div>


                </div>


 @include('admin.pages.clientsUpdated.navbar')




            </div>

            <!-- Filter -->
            <div class="pxy-256 bg-white br-96 mb-192">

                <p class="fs-128 gap-2 gray-94 fw-semibold mb-3 pb-3 d-flex align-items-center border-bottom">
                    <img src="{{ asset('new/src/assets/icons/filter.svg') }}" class="brightness-50" width="16"
                        alt="" />
                    Filters
                </p>

                {{-- @include('admin.pages.clientsUpdated.search') --}}
            </div>



            <!-- Table -->
            <table class="table table-boreder table-hover table-responsive d-none">
                <thead>
                    <tr>
                        <th>Order Count</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th> {{ $items->total() }} </th>

                    </tr>
                </tbody>
            </table>

            <div class="pxy-256 bg-white br-96 mb-192 ">
                <div class="table-responsive w-full overflow-x-auto  border br-64 ">
                    <table id="order-list" class="text-center fw-semibold w-100">
                        <thead class="">
                            <tr>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">ID</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Name</th>
                                {{-- <th class="fs-112 gray-b4 pxy-1288 font-semibold">client_orders_count</th>
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">city</th> --}}
                                <th class="fs-112 gray-b4 pxy-1288 font-semibold">Actions</th>

                                </th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center; width:100%">
                            @forelse ($items as $item)
                                <tr class="fs-112 text-center black-58">
                                    <td class="pxy-1288">{{ @$item->id }}</td>
                                    <td class="pxy-1288">{{ @$item->name  }}</td>
                                    {{-- <td class="pxy-1288">{{ @$item->client_orders_count }}</td>
                                    <td class="pxy-1288">{{ @$item->client?->city?->name }}</td> --}}
                                    <td class="pxy-1288 d-flex gap-2">

                                        <a href="{{ route('ZoneNew.edit', ['id' => $item->id]) }}"
                                            class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 order-history-btn">
                                            <svg width="19.2px" height="19.2px" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M2.59631 2.59656C3.70138 1.49148 5.35723 1.06666 7.49974 1.06666H9.16641C9.49778 1.06666 9.76641 1.33528 9.76641 1.66666C9.76641 1.99803 9.49778 2.26666 9.16641 2.26666H7.49974C5.47558 2.26666 4.21476 2.67516 3.44484 3.44509C2.67491 4.21501 2.26641 5.47583 2.26641 7.49999V12.5C2.26641 14.5241 2.67491 15.785 3.44484 16.5549C4.21476 17.3248 5.47558 17.7333 7.49974 17.7333H12.4997C14.5239 17.7333 15.7847 17.3248 16.5546 16.5549C17.3246 15.785 17.7331 14.5241 17.7331 12.5V10.8333C17.7331 10.502 18.0017 10.2333 18.3331 10.2333C18.6644 10.2333 18.9331 10.502 18.9331 10.8333V12.5C18.9331 14.6425 18.5082 16.2983 17.4032 17.4034C16.2981 18.5085 14.6423 18.9333 12.4997 18.9333H7.49974C5.35723 18.9333 3.70138 18.5085 2.59631 17.4034C1.49123 16.2983 1.06641 14.6425 1.06641 12.5V7.49999C1.06641 5.35748 1.49123 3.70163 2.59631 2.59656Z"
                                                    fill="#949494"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M15.1661 0.869525C16.084 0.77943 16.9969 1.18191 17.9073 2.09239C18.8178 3.00287 19.2203 3.91576 19.1302 4.8336C19.0432 5.71998 18.5083 6.45658 17.9073 7.05759L11.3337 13.6312C11.1484 13.8106 10.9054 13.9679 10.6702 14.0861C10.4364 14.2036 10.1619 14.3067 9.90126 14.344L7.38987 14.7027C6.77804 14.7869 6.19452 14.6198 5.78571 14.2126C5.37605 13.8046 5.20761 13.221 5.29759 12.6057L5.29777 12.6045L5.6555 10.1004L5.65565 10.0993C5.69268 9.83517 5.7953 9.55871 5.91365 9.32325C6.03224 9.0873 6.19153 8.84301 6.37548 8.65906L12.9421 2.09239C13.5432 1.49138 14.2798 0.956531 15.1661 0.869525ZM15.2834 2.06379C14.8114 2.11011 14.323 2.4086 13.7907 2.94092L7.224 9.50759C7.15795 9.57364 7.06724 9.70018 6.98583 9.86215C6.90434 10.0243 6.85708 10.1724 6.84398 10.2662L6.84371 10.2682L6.48538 12.7765L6.48504 12.7788C6.44209 13.0714 6.52786 13.2581 6.63252 13.3624C6.73813 13.4675 6.92917 13.5545 7.22487 13.5141L7.22628 13.5139L9.73155 13.156C9.8209 13.1433 9.96726 13.0964 10.1313 13.0139C10.2919 12.9332 10.4226 12.8419 10.4962 12.7716L17.0588 6.20906C17.5911 5.67673 17.8896 5.18833 17.9359 4.71637C17.9792 4.27589 17.815 3.69711 17.0588 2.94092C16.3026 2.18473 15.7238 2.02055 15.2834 2.06379Z"
                                                    fill="#949494"></path>
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M12.2628 2.88059C12.5819 2.79115 12.913 2.97729 13.0025 3.29637C13.5041 5.08572 14.9047 6.48763 16.705 6.99771C17.0238 7.08805 17.209 7.41973 17.1187 7.73855C17.0284 8.05737 16.6967 8.2426 16.3778 8.15227C14.1781 7.52902 12.4621 5.81425 11.847 3.62028C11.7576 3.30121 11.9437 2.97004 12.2628 2.88059Z"
                                                    fill="#949494"></path>
                                            </svg>
                                        </a>

                                        {{-- <form method="POST"
                                            action="{{ route('ZoneNew.destroy', ['id' => $item->id]) }}"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this client?');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                                                <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="Delete" />
                                            </button>
                                        </form> --}}


                                    </td>
                                </tr>

                            @empty
                            @endforelse

                        </tbody>
                    </table>

                </div>
                <div class="d-flex justify-content-between pagination mt-192">
                    {!! $items->appends(request()->all())->links() !!}
                </div>
            </div>
        </div>

        <!-- Pagination -->

    </div>
@endsection

<script>
    $(document).ready(function() {




    });
</script>
