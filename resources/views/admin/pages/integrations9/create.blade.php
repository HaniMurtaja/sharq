<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


<style>
    .year {
        max-height: 100px;
        /* Adjust the height as needed */
        overflow-y: auto;
    }
    .form-control,
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }
</style>
<div class="active tab-pane table-responsive p-0" style="height: 450px;" id="activity">

    <div class="card-header">
        <h3 class="card-title" id="title-vehicle"> New Integration</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" id="integration-form">
            @csrf
            
            <p>Integration information</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" placeholder="Name" name="name">
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
                        <input name="integration_id" id="integration_id" hidden >
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

                <div class="col-md-3">
                    <button type="button" class="btn btn-block bg-gradient-primary btn-sm" id="save-integration-btn">Save</button>

                </div>


            </div>
        </form>


    </div>



</div>

