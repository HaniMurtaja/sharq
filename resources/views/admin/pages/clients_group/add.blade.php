
<section class="globalForm groupCreateForm">

    <div class="globalHeader">
        <h1 id="title-group">Create new group</h1>
    </div>


    <form id="group-form" class="customForm sectionGlobalForm">
        @csrf
        <p class="sectionTitle">Inforamtion</p>
        <span class="visibility-hidden"></span>
        <input name="client_group_id" hidden>
        <fieldset class="floating-label-input">
            <input type="text" name="group_name" id="group_name" required />
            <legend>Group Name<span class="text-danger">*</span></legend>
        </fieldset>
        <span class="visibility-hidden"></span>
        <p class="sectionTitle">Details</p>
        <span class="visibility-hidden"></span>

        <div class="custom-fieldset">
            <label for="template-name" class="custom-legend">
                Calculation Method <span class="text-danger d-none">*</span>
            </label>
            <select
                class="form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3 select2"
                id="calculation_method" name="calculation_method" style="width: 100%;"
                onchange="fetchCalculationMethod(this.value)">
                <option value="" selected="selected" disabled>Calculation method
                </option>

                @foreach (App\Enum\DeleveryFeed::cases() as $feedType)
                    <option value="{{ $feedType->value }}">{{ $feedType->getLabel() }}
                    </option>
                @endforeach

            </select>
        </div>
        <fieldset class="floating-label-input">
            <input type="text" value="" id="default_delivery_fee" name="default_delivery_fee" required />
            <legend>Default delivery fee<span class="text-danger">*</span></legend>
        </fieldset>
        <fieldset class="floating-label-input">
            <input type="text" id="collection_amount" name="collection_amount"   required />
            <legend>Collection amount<span class="text-danger">*</span></legend>
        </fieldset>
        <div class="custom-fieldset">
            <label for="template-name" class="custom-legend">
                Service Type <span class="text-danger d-none">*</span>
            </label>
            <select class=" form-control shadow-none custom-select2-search w-full border rounded-md border-gray5 h-[2.9rem] px-3 " style="width: 100%"  id="service_type" name="service_type">
                <option></option>
                <option value="Delivery">Delivery</option>

            </select>
        </div>

        <div id="calcMethod"></div>
   
        <div class="itemListDivider"></div>

        <!-- Buttons -->
        <div class="templatesActionBtns w-100 d-flex justify-content-between align-items-center" dir="ltr">
            <div >
                <button style="display: none" type="button" id="delete-client-group-btn" class="templateDeleteBtn">
                    Delete
                </button>
            </div>
            <div>
               
                <button type="button" id="btn-save-group" class="templateSaveBtn">
                    Save changes
                </button>
            </div>
        </div>


    </form>
</section>
