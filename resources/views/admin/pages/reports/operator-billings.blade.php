<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">



<div class="active tab-pane table-responsive p-0" style="height: 700px;" >


    <div class="row">
        <div class="col-sm-4">

            <div class="card">
                <div class="card-body">
                    <i class="fa-solid fa-house"></i> &nbsp; &nbsp;
                    {{ $total_balance }} SAR Blaance
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card">
                <div class="card-body">
                    <i class="fa-solid fa-list"></i> &nbsp; &nbsp;
                    {{ $total_balance_after_tax }} SAR Balance after taxes

                </div>
            </div>
        </div>

        
    </div>

    <div class="row">


        <div class="col-md-12">

            <div class="card card-default">
                <div class="tab-content" id="tab-content">





                    <div class="card-body" style="height: 600px;">
                        <table id="operator-billings-table" class="table table-head-fixed text-nowrap">
                            <thead>
                                <td>Driver</td>
                                <td>Order count</td>
                                <td>Service fees</td>
                                <td>Operator fees</td>
                                <td>Balance</td>
                                <td>After tax</td>
                
                            </thead>
                            <tbody>
                
                
                            </tbody>
                        </table>
                    </div>








                </div>



            </div>

            <!-- /.col -->

            <!-- /.col -->
        </div>

    </div>




    
</div>
