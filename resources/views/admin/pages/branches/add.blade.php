<section class="globalForm branchCreateForm">

    <div class="globalHeader">
        <h1 id="branches-title">Create new branch group</h1>
    </div>
    <form class="customForm sectionGlobalForm" id="branch-form">
        @csrf
        <input hidden name="branch_id" id="branch_id">
        <fieldset class="floating-label-input">
            <input type="text" value="" requiredid="branch_name" id="branch_name" name="branch_name">

            <legend>Branch Name<span class="text-danger d-none">*</span></legend>
        </fieldset>

        <div class="dataContent position-relative">
            <div class="modalSelectBox clientsNewUser w-100 d-flex flex-row-reverse position-relative">

                <select class="operator"  id="driver_id" name="driver_id"
                    style="width: 100%;">
                    <option></option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                    @endforeach
                </select>
            </div>

            
        </div>

       
        <div class="templatesActionBtns w-100 d-flex justify-content-between align-items-center" dir="ltr">
            <div >
                <button style="display: none" type="button" id="delete-branch-btn" class="templateDeleteBtn">
                    Delete
                </button>
            </div>
            <div>
               
                <button type="button" id="save-branch-btn" class="templateSaveBtn">
                    Save
                </button>
            </div>
        </div>

    </form>

</section>
