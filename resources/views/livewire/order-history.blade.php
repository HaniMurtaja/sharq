<div>

    <div class="modal fade " id="historyModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered my-modal-dialog  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>



                    <button type="button" class="closeBtn" aria-label="Close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>Brand: <span id="brand"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p>Branch: <span id="branch"></span></p>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p>Customer phone: <span id="customerPhone"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p>Customer name: <span id="customerName"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>Order Created Time: <span id="created_at"></span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p>Cancel Reason: <span id="cancel_reason"></span></p>
                        </div>
                    </div>

                    <table class="table table-responsive table-sm mous-click-new">
                        <thead>
                            <tr>
                                <td colspan="2">Date/Time</td>
                                <td></td>
                                <td colspan="3">Activity</td>
                                <td></td>
                                <td colspan="5">Description</td>
                            </tr>
                        </thead>
                        <tbody id="historyTable">
                            <!-- History rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
