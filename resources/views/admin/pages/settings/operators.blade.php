<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">

<div class="tab-pane table-responsive p-0" style="height: 450px;" id="operators">
    <div class="card-header">
        <h4>Operators Settings</h4>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form action="{{ route('save-operators') }}" method="post">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <p style="display: inline;">Operators task sorting</p> &nbsp; &nbsp;
                        
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="task_sorting" class="status-toggle"
                                    value="{{ isset($settings->operators['task_sorting']) ? $settings->operators['task_sorting'] : 0 }}"
                                    name="task_sorting"
                                 
                                    {{ old('task_sorting', isset($settings->operators['task_sorting']) ? $settings->operators['task_sorting'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('task_sorting')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <p>Km before reminding operator to update status</p>
                        <input type="text" class="form-control" id="Km_reminding_operator_update" name="Km_reminding_operator_update"
                               value="{{ old('Km_reminding_operator_update', isset($settings->operators['Km_reminding_operator_update']) ? $settings->operators['Km_reminding_operator_update'] : '') }}">
                        @error('Km_reminding_operator_update')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
               
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <p>Drop-off buffer time (Min)</p>
                      
                    </div>
                </div>


                
                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="drop_off_buffer_time" class="status-toggle"
                                    value="{{ isset($settings->operators['drop_off_buffer_time']) ? $settings->operators['drop_off_buffer_time'] : 0 }}"
                                    name="drop_off_buffer_time"
                                 
                                    {{ old('drop_off_buffer_time', isset($settings->operators['drop_off_buffer_time']) ? $settings->operators['drop_off_buffer_time'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('drop_off_buffer_time')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>

               
                
            </div>


            <div class="row">
              


                
                

                <div class="col-md-6">
                    <div class="form-group">
                        <p>Drop-off handling time (Min)</p>
                        
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="drop_off_handling_time" class="status-toggle"
                                    value="{{ isset($settings->operators['drop_off_handling_time']) ? $settings->operators['drop_off_handling_time'] : 0 }}"
                                    name="drop_off_handling_time"
                                 
                                    {{ old('drop_off_handling_time', isset($settings->operators['drop_off_handling_time']) ? $settings->operators['drop_off_handling_time'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('drop_off_handling_time')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>
                
            </div>




            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <p>Pickup WhatsApp template</p>
                        <input type="text" class="form-control" id="pickup_whats_app" name="pickup_whats_app"
                               value="{{ old('pickup_whats_app', isset($settings->operators['pickup_whats_app']) ? $settings->operators['pickup_whats_app'] : '') }}">
                        @error('pickup_whats_app')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <p>Drop-off WhatsApp template</p>
                        <input type="text" class="form-control" id="drop_of_whats_app" name="drop_of_whats_app"
                               value="{{ old('drop_of_whats_app', isset($settings->operators['drop_of_whats_app']) ? $settings->operators['drop_of_whats_app'] : '') }}">
                        @error('drop_of_whats_app')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <p style="display: inline;">Hide dropoff area</p>
                    </div>
                </div> 


                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="hide_dropoff_area" class="status-toggle"
                                    value="{{ isset($settings->operators['hide_dropoff_area']) ? $settings->operators['hide_dropoff_area'] : 0 }}"
                                    name="hide_dropoff_area"
                                 
                                    {{ old('hide_dropoff_area', isset($settings->operators['hide_dropoff_area']) ? $settings->operators['hide_dropoff_area'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('hide_dropoff_area')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>

                
                
            </div>



            <div class="row">
               

                

                <div class="col-md-6">
                    <div class="form-group">
                        <p style="display: inline;">Enable mileage tracking</p> &nbsp; &nbsp;
                        
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="enable_milage_tracking" class="status-toggle"
                                    value="{{ isset($settings->operators['enable_milage_tracking']) ? $settings->operators['enable_milage_tracking'] : 0 }}"
                                    name="enable_milage_tracking"
                                 
                                    {{ old('enable_milage_tracking', isset($settings->operators['enable_milage_tracking']) ? $settings->operators['enable_milage_tracking'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('enable_milage_tracking')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>

                
                
            </div>



            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <p style="display: inline;">Enable inspection form filling</p> 
                        
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="enable_inspection_form_filling" class="status-toggle"
                                    value="{{ isset($settings->operators['enable_inspection_form_filling']) ? $settings->operators['enable_inspection_form_filling'] : 0 }}"
                                    name="enable_inspection_form_filling"
                                 
                                    {{ old('enable_inspection_form_filling', isset($settings->operators['enable_inspection_form_filling']) ? $settings->operators['enable_inspection_form_filling'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('enable_inspection_form_filling')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>



                
                
            </div>





            <div class="row">
              


                



                <div class="col-md-6">
                    <div class="form-group">
                        <p style="display: inline;">Enable accessories</p> 
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group text-right">
                           
                            <label class="switch">
                                <input type="checkbox" id="enable_accessories" class="status-toggle"
                                    value="{{ isset($settings->operators['enable_accessories']) ? $settings->operators['enable_accessories'] : 0 }}"
                                    name="enable_accessories"
                                 
                                    {{ old('enable_accessories', isset($settings->operators['enable_accessories']) ? $settings->operators['enable_accessories'] : 0) == 1 ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>


                            @error('enable_accessories')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                       
                    </div>
                </div>
                
            </div>





            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <p style="display: inline;">Select accessories</p>
                        <select class="form-control select2" style="width: 100%;" name="accessories">
                            <option value="Jake" {{ old('accessories', isset($settings->operators['accessories']) && $settings->operators['accessories'] === 'Jake' ? 'selected' : '') }}>Jake</option>
                            <option value="Uniform" {{ old('accessories', isset($settings->operators['accessories']) && $settings->operators['accessories'] === 'Uniform' ? 'selected' : '') }}>Uniform</option>
                            <option value="Cables" {{ old('accessories', isset($settings->operators['accessories']) && $settings->operators['accessories'] === 'Cables' ? 'selected' : '') }}>Cables</option>
                            <option value="Battery Booster" {{ old('accessories', isset($settings->operators['accessories']) && $settings->operators['accessories'] === 'Battery Booster' ? 'selected' : '') }}>Battery Booster</option>
                            <option value="Mobile" {{ old('accessories', isset($settings->operators['accessories']) && $settings->operators['accessories'] === 'Mobile' ? 'selected' : '') }}>Mobile</option>
                            <option value="Tablet" {{ old('accessories', isset($settings->operators['accessories']) && $settings->operators['accessories'] === 'Tablet' ? 'selected' : '') }}>Tablet</option>
                        </select>
                        @error('accessories')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <button type="submit" class="btn btn-block bg-gradient-primary btn-sm">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
    });
</script>
