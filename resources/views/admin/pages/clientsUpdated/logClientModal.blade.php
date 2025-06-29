    <!-- Operator Detail Modal -->
    <div class="modal fade " id="logClientDetails" aria-labelledby="logClientDetails" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2 border-0">
                    <h5 class="modal-title fw-bold">
                        Client Log
                    </h5>
                    <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                        <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                                fill="black"></path>
                        </svg>

                    </button>
                </div>
                <div class="modal-body px-0">
                    <!-- Table of data -->

                    <table id="client-log-table" class="w-full">
                        <thead class="">
                            <tr>
                                <th class="px-4 py-3 font-medium">Username</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">Created At</th>
                                <th class="px-4 py-3 font-medium">Action</th>
                            </tr>


                        </thead>
                        <tbody id="logTableBody">
                            
                        </tbody>
                    </table>





                </div>


            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

           
              

                $(document).on('hidden.bs.modal', '#logClientDetails', function() {
                    const $table = $('#client-log-table');
                    if ($.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().destroy();
                    }
                });
         




        });
    </script>
