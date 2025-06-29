<p>Flat Rate</p>

<div class="row mt-2">

    <div class="col-md-6">
        <fieldset class="floating-label-input">
            <input  name="collec_amount" class=""
                value="{{ isset($calculation_method) && count($calculation_method->data) > 0 ? $calculation_method->data['collec_amount'] : '' }}">
            <legend>Collection Amount <span class="text-danger">*</span></legend>
        </fieldset>
    </div>

</div>
