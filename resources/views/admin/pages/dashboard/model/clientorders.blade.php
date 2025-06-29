<div class="modal fade dashboardModal" id="reportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="rechargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <div class="head text-start w-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>
                            Orders
                        </h4>
                        <button class="closeBtn" aria-label="Close" data-bs-dismiss="modal">
                            <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                    fill="black"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p id="totalOrders"></p>
                            <p id="counts_delivered_orders"></p>
                            <p id="counts_cancel_orders"></p>
                        </div>

                        <form class="m-0" id="exportForm"
                           >
                            <input type="hidden" name="fromtime" id="export_fromtime">
                            <input type="hidden" name="totime" id="export_totime">
                            <input type="hidden" name="client_id" id="export_client_id">
                            <input type="hidden" name="city_id" id="export_city_id">
                            <button
                                class="red-color d-flex align-items-center border-0 outline-none bg-white fw-semiBold  d-flex gap-1 red-color">
                                <svg width="16px" height="16px" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.49998 14.1666V9.16663L5.83331 10.8333" stroke="#a30133"
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M7.5 9.16663L9.16667 10.8333" stroke="#a30133" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path
                                        d="M18.3334 8.33329V12.5C18.3334 16.6666 16.6667 18.3333 12.5 18.3333H7.50002C3.33335 18.3333 1.66669 16.6666 1.66669 12.5V7.49996C1.66669 3.33329 3.33335 1.66663 7.50002 1.66663H11.6667"
                                        stroke="#a30133" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                    <path
                                        d="M18.3334 8.33329H15C12.5 8.33329 11.6667 7.49996 11.6667 4.99996V1.66663L18.3334 8.33329Z"
                                        stroke="#a30133" stroke-width="1.2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                </svg>
                                Export
                            </button>
                        </form>

                    </div>
                </div>


            </div>

            <div class="modal-body px-0">
                <div class="scrollable-table">
                    <table class="table" id="reportTable">
                        <thead>
                            <th onclick='sortTable(0)'>Brand Name <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(1)'>Total <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(2)'>Pending Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(3)'>In Progress Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(4)'>Cancel Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(5)'>Delivered Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(6)'>Avg Operator Waiting (H:i:s) <span
                                    style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTable(7)'>Avg Delivered (H:i:s) <span style="opacity: 0.5;">↑↓</span></th>
                        </thead>
                        <tbody>



                        </tbody>
                    </table>

                </div>


            </div>
        </div>

    </div>

</div>




<div class="modal fade" id="reportModal2222" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-xl modal-dialog-scrollable">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Orders Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Table to display report data -->

                <table class="table table-bordered table-sm " id="reportTable2222">
                    <thead>
                        <tr>

                            <th>User Name</th>
                            <th>Total</th>
                            <th>Pending Orders</th>
                            <th>In Progress Orders</th>
                            <th>Cancel Orders</th>
                            <th>Delivered Orders</th>
                            <th>Avg Operator Waiting (H:i:s)</th>
                            <th>Avg Delivered (H:i:s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows will be injected here -->
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <span id="totalOrders" class="me-auto"></span>
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {




        document.querySelector('#exportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('export_fromtime').value = document.getElementById('from_date')
                .value;
            document.getElementById('export_totime').value = document.getElementById('to_date').value;
            document.getElementById('export_client_id').value = document.getElementById('clientFilter')
                .value;
            document.getElementById('export_city_id').value = document.getElementById('city_id').value;
           
            const form = e.target;
            const formData = new URLSearchParams(new FormData(form)).toString();


            fetch("{{ url('admin/orders-per-clients-export') }}?" + formData, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.download_url) {
                        window.location.href = data.download_url;
                    } else {
                        alert("Download URL not found.");
                    }
                })
                .catch(error => {
                    console.error('Export error:', error);
                    alert("Something went wrong while exporting.");
                });
        });

    });


    let sortOrder = {};

    function sortTable(columnIndex) {
        console.log(565);

        const table = document.querySelector("#reportModal #reportTable");
        if (!table) return;

        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const headers = table.querySelectorAll("th");


        headers.forEach(th => th.style.background = "");

        sortOrder[columnIndex] = !sortOrder[columnIndex];

        rows.sort((rowA, rowB) => {
            let valA = rowA.cells[columnIndex].textContent.trim().toLowerCase();
            let valB = rowB.cells[columnIndex].textContent.trim().toLowerCase();

            valA = isNaN(valA) ? valA : parseFloat(valA);
            valB = isNaN(valB) ? valB : parseFloat(valB);

            return sortOrder[columnIndex] ? valA > valB ? 1 : -1 : valA < valB ? 1 : -1;
        });
        tbody.innerHTML = "";
        rows.forEach(row => tbody.appendChild(row));

        headers.forEach((th, index) => {
            th.style.background = "";
            th.style.color = "";
            th.style.borderRadius = "";
            th.innerHTML = th.innerHTML.replace(/<span.*<\/span>/, "") +
                ` <span style="opacity: 0.5;">↑↓</span>`;
        });

        headers[columnIndex].style.color = "#f46624";

        headers[columnIndex].innerHTML = headers[columnIndex].innerHTML.replace(/<span.*<\/span>/, "") +
            ` <span style="opacity: 1; font-weight: bold;">${sortOrder[columnIndex] ? "↑" : "↓"}</span>`;
    }
</script>
