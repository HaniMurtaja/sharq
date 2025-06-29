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

<link rel="stylesheet" href="{{ asset('new/src/css/indexClients.css') }}" />


@section('title')
    Show Clients
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">

        <!-- Navigation Tabs -->
        <div class="flex flex-col items-center justify-center mb-192 pb-192 border-b md:flex-row md:justify-between">
            <div class="flex flex-col">
                <h3 class="text-black fs-192 fw-bold m-0 ">Clients</h3>
            </div>

            <a href="{{ route('clientupdated.create') }}" class="bg-red-a3 px-4 py-2 text-white br-96 fw-bold  fs-112">+
                New
                Client</a>

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
                        Clients Count
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

            @include('admin.pages.clientsUpdated.search')
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
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">Phone</th>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">Email</th>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">Logo</th>
                            <th class="fs-112 gray-b4 pxy-1288 font-semibold">Account No</th>
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
                                <td class="pxy-1288">{{ @$item->FullName }}</td>
                                <td class="pxy-1288">{{ @$item->phone }}</td>
                                <td class="pxy-1288">{{ @$item->email }}</td>

                                <td class="pxy-1288">
                                    <div id="croppedImage">
                                        <img src="{{ asset($item->image ?? 'new/src/assets/images/No_Image_Available.jpeg') }}"
                                            alt="Current Profile" style="border-radius: 50%; max-width: 70px">
                                    </div>

                                </td>
                                <td class="pxy-1288">{{ @$item->client?->account_number }}</td>
                                {{-- <td class="pxy-1288">{{ @$item->client_orders_count }}</td>
                                    <td class="pxy-1288">{{ @$item->client?->city?->name }}</td> --}}
                                <td class="pxy-1288 d-flex gap-2 justify-content-center">

                                    <a href="{{ route('clientupdated.edit', ['id' => $item->id]) }}"
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

                                    <a href="{{ route('clientupdated.view', ['id' => $item->id]) }}"
                                        class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 order-view-btn">
                                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M11.9998 2.9707C8.15186 2.9707 4.64826 5.2393 2.25734 8.99767C1.71214 9.85208 1.46484 10.9467 1.46484 11.9957C1.46484 13.0445 1.71205 14.1389 2.25703 14.9932C4.64795 18.7519 8.1517 21.0207 11.9998 21.0207C15.848 21.0207 19.3517 18.7519 21.7427 14.9932C22.2876 14.1389 22.5348 13.0445 22.5348 11.9957C22.5348 10.9469 22.2876 9.8525 21.7427 8.99816C19.3517 5.2395 15.848 2.9707 11.9998 2.9707ZM3.52266 9.80325C5.71174 6.3619 8.78799 4.4707 11.9998 4.4707C15.2117 4.4707 18.288 6.3619 20.477 9.80325L20.4777 9.80423C20.8322 10.3597 21.0348 11.1549 21.0348 11.9957C21.0348 12.8365 20.8322 13.6317 20.4777 14.1872L20.477 14.1882C18.288 17.6295 15.2117 19.5207 11.9998 19.5207C8.78799 19.5207 5.71174 17.6295 3.52266 14.1882L3.52204 14.1872C3.16745 13.6317 2.96484 12.8365 2.96484 11.9957C2.96484 11.1549 3.16745 10.3597 3.52204 9.80423L3.52266 9.80325ZM9.17188 11.9999C9.17188 10.4341 10.4361 9.16992 12.0019 9.16992C13.5677 9.16992 14.8319 10.4341 14.8319 11.9999C14.8319 13.5657 13.5677 14.8299 12.0019 14.8299C10.4361 14.8299 9.17188 13.5657 9.17188 11.9999ZM12.0019 7.66992C9.60766 7.66992 7.67188 9.60571 7.67188 11.9999C7.67188 14.3941 9.60766 16.3299 12.0019 16.3299C14.3961 16.3299 16.3319 14.3941 16.3319 11.9999C16.3319 9.60571 14.3961 7.66992 12.0019 7.66992Z"
                                                fill="#aeaeae"></path>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('clientupdated.destroy', ['id' => $item->id]) }}"
                                        style="display:inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this client?');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="flex items-center justify-center w-8 h-8 text-white border rounded-lg border-gray10">
                                            <img src="{{ asset('new/src/assets/icons/delete.svg') }}" alt="Delete" />
                                        </button>
                                    </form>

                                    <a href="#" data-client_id="{{ $item->id }}"
                                        data-bs-target="#logClientDetails" data-bs-toggle="modal"
                                        class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 order-view-btn open-client-logs">
                                        <svg width="19px" height="19px" viewBox="0 0 512 512" version="1.1"
                                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>log</title>
                                            <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                                fill-rule="evenodd">
                                                <g id="log-white" fill="#000000"
                                                    transform="translate(85.572501, 42.666667)">
                                                    <path
                                                        d="M236.349632,7.10542736e-15 L1.68296533,7.10542736e-15 L1.68296533,234.666667 L44.349632,234.666667 L44.349632,192 L44.349632,169.6 L44.349632,42.6666667 L218.642965,42.6666667 L300.349632,124.373333 L300.349632,169.6 L300.349632,192 L300.349632,234.666667 L343.016299,234.666667 L343.016299,106.666667 L236.349632,7.10542736e-15 L236.349632,7.10542736e-15 Z M4.26325641e-14,405.333333 L4.26325641e-14,277.360521 L28.8096875,277.360521 L28.8096875,382.755208 L83.81,382.755208 L83.81,405.333333 L4.26325641e-14,405.333333 Z M153.17,275.102708 C173.279583,275.102708 188.692917,281.484792 199.41,294.248958 C209.705625,306.47125 214.853437,322.185625 214.853437,341.392083 C214.853437,362.404792 208.772396,379.112604 196.610312,391.515521 C186.134062,402.232604 171.653958,407.591146 153.17,407.591146 C133.060417,407.591146 117.647083,401.209062 106.93,388.444896 C96.634375,376.222604 91.4865625,360.267396 91.4865625,340.579271 C91.4865625,319.988021 97.5676042,303.490937 109.729687,291.088021 C120.266146,280.431146 134.74625,275.102708 153.17,275.102708 Z M153.079687,297.680833 C142.663646,297.680833 134.625833,302.015833 128.96625,310.685833 C123.848542,318.512917 121.289687,328.567708 121.289687,340.850208 C121.289687,355.059375 124.330208,366.0775 130.41125,373.904583 C136.131042,381.310208 143.717292,385.013021 153.17,385.013021 C163.525833,385.013021 171.59375,380.647917 177.37375,371.917708 C182.491458,364.211042 185.050312,354.035833 185.050312,341.392083 C185.050312,327.483958 182.009792,316.616354 175.92875,308.789271 C170.208958,301.383646 162.592604,297.680833 153.079687,297.680833 Z M343.91,333.715521 L343.91,399.011458 C336.564583,401.48 331.386667,403.105625 328.37625,403.888333 C319.043958,406.356875 309.019271,407.591146 298.302187,407.591146 C277.229271,407.591146 261.18375,402.292812 250.165625,391.696146 C237.943333,380.015729 231.832187,363.729375 231.832187,342.837083 C231.832187,318.813958 239.418437,300.69125 254.590937,288.468958 C265.609062,279.558125 280.480521,275.102708 299.205312,275.102708 C315.220729,275.102708 330.122292,278.022812 343.91,283.863021 L334.065937,306.350833 C327.563437,303.099583 321.87375,300.826719 316.996875,299.53224 C312.12,298.23776 306.761458,297.590521 300.92125,297.590521 C286.952917,297.590521 276.657292,302.13625 270.034375,311.227708 C264.435,318.934375 261.635312,329.079479 261.635312,341.663021 C261.635312,356.775312 265.849896,368.154687 274.279062,375.801146 C281.022396,381.942396 289.391354,385.013021 299.385937,385.013021 C305.226146,385.013021 310.765312,384.019583 316.003437,382.032708 L316.003437,356.293646 L293.967187,356.293646 L293.967187,333.715521 L343.91,333.715521 Z"
                                                        id="XLS">

                                                    </path>
                                                </g>
                                            </g>
                                        </svg>
                                    </a>


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
@endsection

@include('admin.pages.clientsUpdated.logClientModal')

<script>
    $(document).ready(function() {
        $('.open-client-logs').on('click', function() {
            let clientId = $(this).data('client_id');

            $.ajax({
                url: '{{ route('getClientLogData') }}',
                type: 'GET',
                data: {
                    client_id: clientId
                },
                success: function(response) {
                    let $table = $('#client-log-table');
                    let $tbody = $('#logTableBody');
                    $tbody.empty(); 

                    response.logs.forEach(log => {
                        let row = `<tr>
                    <td class="px-4 py-2">${log.user_name}</td>
                    <td class="px-4 py-2">${log.user_email}</td>
                    <td class="px-4 py-2">${log.date}</td>
                    <td class="px-4 py-2">${log.description}</td>
                </tr>`;
                        $tbody.append(row);
                    });

                   
                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().destroy();
                    }

                    $table.DataTable({
                        pageLength: 5,
                        searching: false,
                        lengthChange: false
                    });

                 
                    $('#logClientDetails').modal('show');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });


    });
</script>
