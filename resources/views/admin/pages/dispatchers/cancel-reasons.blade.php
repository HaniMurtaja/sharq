<div class="modal fade " id="cancelRequestModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="rechargeModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title fw-bold text-danger" id="exampleModalLabel">Cancellation Request</h5>
                <button type="button" class="btnClose" data-bs-dismiss="modal" aria-label="Close">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M1.5 12C1.5 6.22386 6.22386 1.5 12 1.5C17.7761 1.5 22.5 6.22386 22.5 12C22.5 17.7761 17.7761 22.5 12 22.5C6.22386 22.5 1.5 17.7761 1.5 12ZM12 2.5C6.77614 2.5 2.5 6.77614 2.5 12C2.5 17.2239 6.77614 21.5 12 21.5C17.2239 21.5 21.5 17.2239 21.5 12C21.5 6.77614 17.2239 2.5 12 2.5ZM12 12.7071L9.52351 15.1835L8.81641 14.4764L11.2928 12L8.81641 9.52351L9.52351 8.81641L12 11.2929L14.4764 8.81641L15.1835 9.52351L12.7071 12L15.1835 14.4764L14.4764 15.1835L12 12.7071Z"
                            fill="black"></path>
                    </svg>

                </button>
            </div>

            <form id="cancelRequestForm">
                <div class="modal-body d-flex flex-column gap-3">
                    <p class="sectionTitle">Please select reason of cancellation</p>
                    <input hidden name="order_id_cancel_reason" id="order_id_cancel_reason">
                    <div class="modalSelectBox cancel-reason w-100  position-relative">
                        <label for="template-name" class="customSelectLegend ">Reason</label>
                        <select  name="reason_id" id="cancel-reason">
                            <option></option>
                            @foreach ($cancel_reasons as $cancel_reason)
                                <option value="{{$cancel_reason->id}}">{{$cancel_reason->name}}</option>
                            @endforeach

                        </select>
                        <span class="error_country invalid-feedback pl-2">pp</span>
                    </div>





                    <!-- Buttons -->
                    <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                        <div>
                            <button type="button" class="templateCancelBtn" aria-label="Close" data-bs-dismiss="modal">
                                No, Keep
                            </button>
                            <button type="button" id="order-cancel-reason-btn" class="templateSaveBtn bg-danger">
                                Yes, Cancel
                            </button>
                        </div>
                    </div>
                </div>


            </form>


        </div>
    </div>
</div>