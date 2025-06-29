<p>Per Km</p>

<div id="per-km-repeater">
    @if (isset($calculation_method) && !empty($calculation_method->data['defualt_fee']))
        <div class="row">
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <input type="text" name="defualt_fee" class="" value="{{ $calculation_method->data['defualt_fee'] }}">
                    <legend>Default Fee <span class="text-danger">*</span></legend>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <input type="text" name="min_km" class="" value="{{ $calculation_method->data['min_km'] ?? '' }}">
                    <legend>Min Km <span class="text-danger">*</span></legend>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <input type="text" name="per_km_fee" class="" value="{{ $calculation_method->data['per_km_fee'] ?? '' }}" >
                    <legend>Per Km Fee <span class="text-danger">*</span></legend>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <select class=" select2" id="service_type" name="type" style="width: 100%;">
                        <option value="" {{ empty($calculation_method->data['type']) ? 'selected' : '' }} disabled>Type</option>
                        <option value="Multiplier" {{ $calculation_method->data['type'] === 'Multiplier' ? 'selected' : '' }}>Multiplier</option>
                        <option value="Addition" {{ $calculation_method->data['type'] === 'Addition' ? 'selected' : '' }}>Addition</option>
                    </select>
                    <span class="text-danger" id="service_type_error"></span>
                </fieldset>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <input type="text" name="defualt_fee" class="" >
                    <legend>Default Fee <span class="text-danger">*</span></legend>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <input type="text" name="min_km" class="" >
                    <legend>Min Km <span class="text-danger">*</span></legend>
                </fieldset>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <fieldset class="floating-label-input">
                    <input type="text" name="per_km_fee" class="" >
                    <legend>Per Km Fee <span class="text-danger">*</span></legend>
                </fieldset>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <select class=" select2" id="service_type" name="type" style="width: 100%;">
                        <option value="" selected="selected" disabled>Type</option>
                        <option value="Multiplier">Multiplier</option>
                        <option value="Addition">Addition</option>
                    </select>
                    <span class="text-danger" id="service_type_error"></span>
                </div>
            </div>
        </div>
    @endif
</div>
