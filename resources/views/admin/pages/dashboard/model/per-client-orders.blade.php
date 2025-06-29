<div class="modal fade dashboardModal" id="clientreportModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="rechargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="head">
                    <h4>
                        Orders
                    </h4>
                    <p id="totalOrdersPerClient"></p>
                    <p id="counts_delivered_ordersPerClient"></p>
                    <p id="counts_cancel_ordersPerClient"></p>
                </div>
                <button class="closeBtn" aria-label="Close" data-bs-dismiss="modal">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>
                </button>

            </div>

            <div class="modal-body">
                <div class="scrollable-table">
                    <table class="table" id="clientreportTable">
                        <thead>
                            <th onclick='sortTablePerClient(0)'>Client <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(1)'>Total <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(2)'>Operators No <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(3)'>Dispatchers No <span style="opacity: 0.5;">↑↓</span></th>

                            
                            <th onclick='sortTablePerClient(4)'>Pending Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(5)'>In Progress Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(6)'>Cancel Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(7)'>Delivered Orders <span style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(8)'>Avg Operator Waiting (H:i:s) <span
                                    style="opacity: 0.5;">↑↓</span></th>
                            <th onclick='sortTablePerClient(9)'>Avg Delivered (H:i:s) <span style="opacity: 0.5;">↑↓</span></th>
                  
                        </thead>
                        <tbody>



                        </tbody>
                    </table>

                </div>


            </div>
        </div>

    </div>

</div>







<script>
    let sortOrderPerClient = {};

    function sortTablePerClient(columnIndex) {

        const table = document.querySelector("#clientreportModal #clientreportTable");
        if (!table) return;

        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const headers = table.querySelectorAll("th");


        headers.forEach(th => th.style.background = "");

        sortOrderPerCity[columnIndex] = !sortOrderPerCity[columnIndex];

        rows.sort((rowA, rowB) => {
            let valA = rowA.cells[columnIndex].textContent.trim().toLowerCase();
            let valB = rowB.cells[columnIndex].textContent.trim().toLowerCase();

            valA = isNaN(valA) ? valA : parseFloat(valA);
            valB = isNaN(valB) ? valB : parseFloat(valB);

            return sortOrderPerCity[columnIndex] ? valA > valB ? 1 : -1 : valA < valB ? 1 : -1;
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
            ` <span style="opacity: 1; font-weight: bold;">${sortOrderPerCity[columnIndex] ? "↑" : "↓"}</span>`;
    }
</script>
