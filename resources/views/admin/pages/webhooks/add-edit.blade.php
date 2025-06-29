<div id="webhook_drawer" data-drawer="Webhook"
        class="fixed top-0 right-0 z-50 min-h-full p-4 transition-transform transform translate-x-full bg-white shadow-lg custom-drawer md:w-1/2">
        <div class="flex flex-col h-screen overflow-scroll">
            <div class="flex items-center justify-between mb-6">
                <h5 class="text-xl font-bold text-blue-gray-700" id="webhook-title">
                    New WebHook
                </h5>
                <button id="close-drawer" class="text-gray-500 close-drawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-2 p-8 overflow-scroll">
                <form method="post" enctype="multipart/form-data" id="webhook-form">
                    @csrf


                    <div class="row">
                       
                        <div class="col-md-12">
                            <div class="form-group">
        
                                <select class="form-control select2" style="width: 100%;" name="integration_company_id">
                                    <option value="" selected="selected" disabled>Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{$company->id}}"> {{$company->name}} </option>
                                    @endforeach                  
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <p>WebHook information</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" id="name_webhook" placeholder="Name" name="name_webhook">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
        
                                <select class="form-control select2" style="width: 100%;" name="type">
                                    <option value="" selected="selected" disabled>Type</option>
                                    <option value="order_created">Order created</option>
                                    <option value="order_updated">Order updated</option>  
                                    <option value="order_cancelled">Order cancelled</option>                   
                                </select>
                            </div>
                        </div>
                    </div>
        
        
        
        
        
        
        
        
        
        
        
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" id="url" placeholder="URL" name="url">
                                <input name="webhook_id" id="webhook_id" hidden >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                  
                                <select class="form-control select2" style="width: 100%;" name="format">
                                    <option value="" selected="selected" disabled>Format</option>
                                    <option value="form-data">Form data</option>
                                    <option value="JSON">JSON</option>  
                                                     
                                </select>
                            </div>
                        </div>
                    </div>
        
               
                    
                   
        
                    <div class="row">
                        <div class="flex items-center justify-center pt-16">
                            <button type="button" class="p-3 !px-20 !text-xl text-white rounded-md bg-blue1"
                                id="save-webhook-btn">Save</button>
                        </div>
                     
        
        
                    </div>
                </form>
            </div>
        </div>
    </div>