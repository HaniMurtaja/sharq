{{-- <style>
    .loader {
        display: none; /* Initially hidden */
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        z-index: 99999999;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.8); /* Optional light background overlay */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .overlay {
        display: none; /* Initially hidden */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Dimmed background effect */
        z-index: 9999999;
    }

    .show {
        display: block !important; /* To show loader and overlay */
    }
</style> --}}
<style>
    .scrollable-table{
        max-height:400px !important;
    }

    .select_km{
        float: right;
        margin: 11px 12px 0px 0px;
        margin-bottom: 0px;
        font-size: 17px;
        margin-bottom: .75rem;
        --tw-bg-opacity: 1;
        background-color: rgb(255 255 255 / var(--tw-bg-opacity));
        border-width: 1px;
        border-radius: 9999px !important;
        height: 38px !important;
        text-align: center;
        width: 100px;
    }
</style>
<div class="overlay" id="overlay"></div>
<div id="loader" class="loader"></div>

<div class="modal fade assignDriverModal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " style="max-width: 891.2px ">

        <div class="modal-content assignDriver">
            <div class="modal-header">
                <div class="head">
                    <h4>
                        Assign order - <span id="order_id">#</span>
                    </h4>
                    <p id="branch_name">
                    </p>
                </div>
                <button class="closeBtn" aria-label="Close" data-bs-dismiss="modal" ><svg width="2.4rem"
                        height="2.4rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg></button>

            </div>

{{--            <!-- Map Place-->--}}
{{--            <div class="map ">--}}
{{--                <div  id="assignDriverMap"  style="width:100%; height:100%;"></div>--}}
{{--            </div>--}}
            <div class="scrollable-table">
                <table class="table" id="driver_assign_table">
                  <thead>
                  <tr>
                      <th>Driver</th>
                      <th>Completed Jobs</th>
                      <th>Distance To PU</th>
                      <th>Tasks</th>
                      <th>Destination</th>
                      <th>Action</th>
                  </tr>
                  </thead>
                    <tbody id="assign-driver-table">

                    </tbody>
                </table>

            </div>



        </div>
    </div>
</div>

