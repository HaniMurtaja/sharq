@extends('admin.layouts.app')
<!-- <link rel="stylesheet" href="{{ asset('new/src/css/globalLayout.css') }}" /> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/foodics.css') }}" />
<style>
    .dt-input {
        background: white !important;
    }
</style>
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2 ">
        <!-- Tabs and Button -->
        <div class="flex flex-column justify-between md:flex-row mb-192">
            <p id="tabDescription" class="text-black fs-192 fw-bold">Foodics</p>

        </div>
        <div>

            <div class="p-4 bg-white br-96 d-flex flex-column gap-lg-3">
                <!-- Table -->
                <div class="w-full overflow-x-auto position-relative">

                    <table id="foodics-table" class="w-full">
                        <thead class="">
                            <tr>
                                <th class="px-4 py-3 font-medium">Id</th>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Integration company</th>
                                <th class="px-4 py-3 font-medium">Action</th>
                            </tr>


                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="//cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {


        clientsTable();

        function clientsTable() {
            if ($.fn.DataTable.isDataTable('#foodics-table')) {
                $('#foodics-table').DataTable().destroy();
            }



            $('#foodics-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, 'DESC']
                ],
                "lengthMenu": [10, 15, 20, 50, 100, 500, 1000, 2000],
                ajax: {
                    url: '{{ route('getFoodicsClientsData') }}',

                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: true
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                        orderable: true
                    },
                    {
                        data: 'integration_company',
                        name: 'integration.name',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],

            });
        }



        $(document).on('click', '.callApiBtn', function() {
            let clientId = $(this).data('id');

            $.ajax({
                url: '{{ route('callClientFoodicsAPI') }}',
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: clientId
                },
                success: function(response) {
                    console.log(response);
                    alert('API call successful!');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    console.error('An error occurred while calling the API.');
                }
            });
        });




        $(document).on("mouseenter", ".text-slide-wrapper", function() {
            console.log("hovered")
            const $textSlide = $(this).find(".text-slide");
            const textContent = $textSlide.text();
            console.log(textContent)
            const textLength = textContent.length;
            const $wrapper = $(this);

            const textWidth = $textSlide[0].scrollWidth;
            const wrapperWidth = $wrapper.outerWidth();

            const moveDistance = textWidth - wrapperWidth;
            const calcValue = `-${moveDistance}px`;

            const keyframes = `
                @keyframes textSlide {
                    0% {
                        transform: translateX(0); /* Start position */
                    }
                    50% {
                        transform: translateX(${calcValue}); /* Move to the last letter */
                    }
                    100% {
                        transform: translateX(0); /* Return to start */
                    }
                }`;
            // Check if the keyframes are already appended to avoid duplication
            if ($("style#textSlideKeyframe").length === 0) {
                $("head").append(`<style id="textSlideKeyframe">${keyframes}</style>`);
            } else {
                // If style already exists, update the keyframes
                $("style#textSlideKeyframe").html(keyframes);
            }



        });

    });
</script>
