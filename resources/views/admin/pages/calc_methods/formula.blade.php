<p>Formula</p>

<p>Service fees (per KM)</p>
<div id="repeater3">
    @if (isset($calculation_method) && !empty($calculation_method->data['between1']))
        @for ($i = 0; $i < count($calculation_method->data['between1']); $i++)
            <div class="row mb-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <select class=" select2" style="width: 100%;" name="between1[]">
                            <option value="" {{ isset($calculation_method->data['between1'][$i]) && $calculation_method->data['between1'][$i] == '' ? 'selected' : '' }}>Between</option>
                            <option value=">" {{ isset($calculation_method->data['between1'][$i]) && $calculation_method->data['between1'][$i] == '>' ? 'selected' : '' }}>></option>
                            <option value="<" {{ isset($calculation_method->data['between1'][$i]) && $calculation_method->data['between1'][$i] == '<' ? 'selected' : '' }}><</option>
                            <option value="=" {{ isset($calculation_method->data['between1'][$i]) && $calculation_method->data['between1'][$i] == '=' ? 'selected' : '' }}>=</option>
                            <option value=">=" {{ isset($calculation_method->data['between1'][$i]) && $calculation_method->data['between1'][$i] == '>=' ? 'selected' : '' }}>>=</option>
                            <option value="<=" {{ isset($calculation_method->data['between1'][$i]) && $calculation_method->data['between1'][$i] == '<=' ? 'selected' : '' }}><=</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9 d-flex ">
                    <fieldset class="floating-label-input me-2">
                        <input type="text" class="" name="from1[]" value="{{ isset($calculation_method->data['from1'][$i]) ? $calculation_method->data['from1'][$i] : '' }}" >
                        <legend>From <span class="text-danger">*</span></legend>
                        <span class="text-danger" id="additional_from_error"></span>
                    </fieldset>
                    <fieldset class="floating-label-input me-2">
                        <input type="text" class="" name="to1[]" value="{{ isset($calculation_method->data['to1'][$i]) ? $calculation_method->data['to1'][$i] : '' }}" >
                        <legend>To <span class="text-danger">*</span></legend>
                        <span class="text-danger" id="additional_to_error"></span>
                    </fieldset>
                    <fieldset class="floating-label-input me-2">
                        <input type="text" class="" name="amount1[]" value="{{ isset($calculation_method->data['amount1'][$i]) ? $calculation_method->data['amount1'][$i] : '' }}" >
                        <legend>Amount <span class="text-danger">*</span></legend>
                        <span class="text-danger" id="additional_percentage_error"></span>
                    </fieldset>
                    <button type="button" class="btn btn-add-formula mx-1 rounded-5" style="color: green;">
                    <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z" fill="#F46624"></path>
                                </svg>
                    </button>
                    <button type="button" class="btn btn-delete-formula rounded-5" style="color: red;">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z" fill="#949494"></path>
                                    <path d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z" fill="#949494"></path>
                                    <path d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z" fill="#949494"></path>
                                    <path d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z" fill="#949494"></path>
                                </svg>
                    </button>
                </div>
            </div>
        @endfor
    @else
        <!-- Default empty row if no existing data -->
        <div class="row mb-2">
            <div class="col-md-3">
                <div class="form-group">
                    <select class=" select2" style="width: 100%;" name="between1[]">
                        <option value="" selected="selected">Between</option>
                        <option value=">">></option>
                        <option value="<"><</option>
                        <option value="=">=</option>
                        <option value=">=">>=</option>
                        <option value="<="><=</option>
                    </select>
                </div>
            </div>
            <div class="col-md-9 d-flex ">
                <fieldset class="floating-label-input me-2">
                    <input type="text" class="" name="from1[]">
                    <legend>From <span class="text-danger">*</span></legend>
                    <span class="text-danger" id="additional_from_error"></span>
                </fieldset>
                <fieldset class="floating-label-input me-2">
                    <input type="text" class="" name="to1[]">
                    <legend>To <span class="text-danger">*</span></legend>
                    <span class="text-danger" id="additional_to_error"></span>
                </fieldset>
                <fieldset class="floating-label-input me-2">
                    <input type="text" class="" name="amount1[]" >
                    <legend>Amount <span class="text-danger">*</span></legend>
                    <span class="text-danger" id="additional_percentage_error"></span>
                </fieldset>
                <button type="button" class="btn btn-add-formula mx-1 rounded-5" style="color: green;">
                <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z" fill="#F46624"></path>
                                </svg>
                </button>
                <button type="button" class="btn btn-delete-formula rounded-5" style="color: red;">
                <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z" fill="#949494"></path>
                                    <path d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z" fill="#949494"></path>
                                    <path d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z" fill="#949494"></path>
                                    <path d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z" fill="#949494"></path>
                                </svg>
                </button>
            </div>
        </div>
    @endif
</div>

<p>Min. Service fee (per KM)</p>
<div id="repeater2">
    @if (isset($calculation_method) && !empty($calculation_method->data['between2']))
        @for ($i = 0; $i < count($calculation_method->data['between2']); $i++)
            <div class="row mb-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <select class=" select2" style="width: 100%;" name="between2[]">
                            <option value="" {{ isset($calculation_method->data['between2'][$i]) && $calculation_method->data['between2'][$i] == '' ? 'selected' : '' }}>Between</option>
                            <option value=">" {{ isset($calculation_method->data['between2'][$i]) && $calculation_method->data['between2'][$i] == '>' ? 'selected' : '' }}>></option>
                            <option value="<" {{ isset($calculation_method->data['between2'][$i]) && $calculation_method->data['between2'][$i] == '<' ? 'selected' : '' }}><</option>
                            <option value="=" {{ isset($calculation_method->data['between2'][$i]) && $calculation_method->data['between2'][$i] == '=' ? 'selected' : '' }}>=</option>
                            <option value=">=" {{ isset($calculation_method->data['between2'][$i]) && $calculation_method->data['between2'][$i] == '>=' ? 'selected' : '' }}>>=</option>
                            <option value="<=" {{ isset($calculation_method->data['between2'][$i]) && $calculation_method->data['between2'][$i] == '<=' ? 'selected' : '' }}><=</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9 d-flex ">
                    <fieldset class="floating-label-input me-2">
                        <input type="text" class="" name="from2[]" value="{{ isset($calculation_method->data['from2'][$i]) ? $calculation_method->data['from2'][$i] : '' }}" >
                        <legend>From <span class="text-danger">*</span></legend>
                        <span class="text-danger" id="additional_from_error"></span>
                    </fieldset>
                    <fieldset class="floating-label-input me-2">
                        <input type="text" class="" name="to2[]" value="{{ isset($calculation_method->data['to2'][$i]) ? $calculation_method->data['to2'][$i] : '' }}" >
                        <legend>To <span class="text-danger">*</span></legend>
                        <span class="text-danger" id="additional_to_error"></span>
                    </fieldset>
                    <fieldset class="floating-label-input me-2">
                        <input type="text" class="" name="amount2[]" value="{{ isset($calculation_method->data['amount2'][$i]) ? $calculation_method->data['amount2'][$i] : '' }}" >
                        <legend>Amount <span class="text-danger">*</span></legend>
                        <span class="text-danger" id="additional_percentage_error"></span>
                    </fieldset>
                    <button type="button" class="btn btn-add-formula mx-1 rounded-5" style="color: green;">
                    <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z" fill="#F46624"></path>
                                </svg>
                    </button>
                    <button type="button" class="btn btn-delete-formula rounded-5" style="color: red;">
                    <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z" fill="#949494"></path>
                                    <path d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z" fill="#949494"></path>
                                    <path d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z" fill="#949494"></path>
                                    <path d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z" fill="#949494"></path>
                                </svg>
                    </button>
                </div>
            </div>
        @endfor
    @else
        <!-- Default empty row if no existing data -->
        <div class="row mb-2">
            <div class="col-md-3">
                <div class="form-group">
                    <select class=" select2" style="width: 100%;" name="between2[]">
                        <option value="" selected="selected">Between</option>
                        <option value=">">></option>
                        <option value="<"><</option>
                        <option value="=">=</option>
                        <option value=">=">>=</option>
                        <option value="<="><=</option>
                    </select>
                </div>
            </div>
            <div class="col-md-9 d-flex ">
                <fieldset class="floating-label-input me-2">
                    <input type="text" class="" name="from2[]" >
                    <legend>From <span class="text-danger">*</span></legend>

                    <span class="text-danger" id="additional_from_error"></span>
                </fieldset>
                <fieldset class="floating-label-input me-2">
                    <input type="text" class="" name="to2[]" >
                    <legend>To <span class="text-danger">*</span></legend>
                    <span class="text-danger" id="additional_to_error"></span>
                </fieldset>
                <fieldset class="floating-label-input me-2">
                    <input type="text" class="" name="amount2[]" >
                    <legend>Amount <span class="text-danger">*</span></legend>
                    <span class="text-danger" id="additional_percentage_error"></span>
                </fieldset>
                <button type="button" class="btn btn-add-formula mx-1 rounded-5" style="color: green;">
                <svg width="17.6px" height="17.6px" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 0C4.83579 0 0 4.83579 0 10.75C0 16.6642 4.83579 21.5 10.75 21.5C16.6642 21.5 21.5 16.6642 21.5 10.75C21.5 4.83579 16.6642 0 10.75 0ZM1.5 10.75C1.5 5.66421 5.66421 1.5 10.75 1.5C15.8358 1.5 20 5.66421 20 10.75C20 15.8358 15.8358 20 10.75 20C5.66421 20 1.5 15.8358 1.5 10.75ZM10.75 6C11.1642 6 11.5 6.33579 11.5 6.75V10H14.75C15.1642 10 15.5 10.3358 15.5 10.75C15.5 11.1642 15.1642 11.5 14.75 11.5H11.5V14.75C11.5 15.1642 11.1642 15.5 10.75 15.5C10.3358 15.5 10 15.1642 10 14.75V11.5H6.75C6.33579 11.5 6 11.1642 6 10.75C6 10.3358 6.33579 10 6.75 10H10V6.75C10 6.33579 10.3358 6 10.75 6Z" fill="#F46624"></path>
                                </svg>
                </button>
                <button type="button" class="btn btn-delete-formula rounded-5" style="color: red;">
                <svg width="19.2px" height="19.2px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.76523 4.81675L7.98035 3.53579L7.99216 3.46487C8.06102 3.04899 8.16203 2.43887 8.56873 1.97769C9.04279 1.44012 9.76609 1.25 10.69 1.25H13.31C14.2451 1.25 14.9677 1.4554 15.439 1.99845C15.8462 2.46776 15.9447 3.08006 16.0105 3.48891L16.0199 3.54711L16.2395 4.84486C16.2408 4.85258 16.242 4.8603 16.243 4.868C17.8559 4.95217 19.4673 5.07442 21.074 5.23364C21.4861 5.27448 21.7872 5.64175 21.7463 6.05394C21.7055 6.46614 21.3382 6.76717 20.926 6.72632C17.6199 6.39869 14.2946 6.22998 10.98 6.22998C9.02529 6.22998 7.07045 6.3287 5.11537 6.52618L5.11317 6.5264L3.07317 6.7264C2.66093 6.76682 2.29399 6.4654 2.25357 6.05316C2.21316 5.64092 2.51458 5.27397 2.92681 5.23356L4.96572 5.03367C5.89884 4.93943 6.83202 4.86712 7.76523 4.81675ZM9.29681 4.75377L9.45958 3.78456C9.54966 3.24976 9.60165 3.07427 9.69376 2.96981C9.7422 2.91488 9.9239 2.75 10.69 2.75H13.31C14.0649 2.75 14.2523 2.9196 14.306 2.98155C14.4032 3.09352 14.4561 3.27767 14.5398 3.79069L14.7105 4.79954C13.4667 4.75334 12.2227 4.72998 10.98 4.72998C10.4189 4.72998 9.85786 4.73791 9.29681 4.75377Z" fill="#949494"></path>
                                    <path d="M18.8983 8.39148C19.3117 8.41816 19.6251 8.77488 19.5984 9.18823L18.9482 19.2623L18.9468 19.2813C18.9205 19.6576 18.8915 20.0713 18.814 20.4563C18.7336 20.8554 18.5919 21.2767 18.3048 21.6505C17.7036 22.4332 16.6806 22.7499 15.21 22.7499H8.78999C7.31943 22.7499 6.29636 22.4332 5.69519 21.6505C5.40809 21.2767 5.2664 20.8554 5.186 20.4563C5.10847 20.0713 5.0795 19.6576 5.05315 19.2813L5.05154 19.2582L4.40155 9.18823C4.37487 8.77488 4.68833 8.41816 5.10168 8.39148C5.51503 8.3648 5.87175 8.67826 5.89843 9.09161L6.54816 19.1575L6.5483 19.1595C6.57652 19.5623 6.60041 19.8817 6.65648 20.1601C6.71108 20.4313 6.78689 20.6094 6.88479 20.7368C7.05362 20.9566 7.47055 21.2499 8.78999 21.2499H15.21C16.5294 21.2499 16.9464 20.9566 17.1152 20.7368C17.2131 20.6094 17.2889 20.4313 17.3435 20.1601C17.3996 19.8817 17.4235 19.5623 17.4517 19.1595L17.4518 19.1575L18.1015 9.09161C18.1282 8.67826 18.4849 8.3648 18.8983 8.39148Z" fill="#949494"></path>
                                    <path d="M9.57999 16.5C9.57999 16.0858 9.91577 15.75 10.33 15.75H13.66C14.0742 15.75 14.41 16.0858 14.41 16.5C14.41 16.9142 14.0742 17.25 13.66 17.25H10.33C9.91577 17.25 9.57999 16.9142 9.57999 16.5Z" fill="#949494"></path>
                                    <path d="M9.5 11.75C9.08579 11.75 8.75 12.0858 8.75 12.5C8.75 12.9142 9.08579 13.25 9.5 13.25H14.5C14.9142 13.25 15.25 12.9142 15.25 12.5C15.25 12.0858 14.9142 11.75 14.5 11.75H9.5Z" fill="#949494"></path>
                                </svg>
                </button>
            </div>
        </div>
    @endif
</div>
