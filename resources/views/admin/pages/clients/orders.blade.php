<style>
    .modal-dialog-custom {
        width: 700px !important;
        margin: 1.75rem auto !important;
    }

    .modal .select2-container {
        z-index: 1050;
    }

    .pac-container {
        z-index: 1051 !important;
        /* Bootstrap modal z-index is typically 1050 */
    }

    #exampleModal {
        -ms-overflow-style: none;

        scrollbar-width: none;

    }

    #exampleModal::-webkit-scrollbar {
        width: 0 !important;
        height: 0 !important;
    }


    #exampleModal .modal-body {
        overflow-y: auto;

        -webkit-overflow-scrolling: touch;
    }

    #exampleModal .modal-body::-webkit-scrollbar {
        width: 0 !important;

    }

    #exampleModal .modal-body {
        scrollbar-width: none;

    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Orders &nbsp; &nbsp; </h3>


            </div>
            <!-- /.card-header -->
            <div class="card-body" style="height: 400px;">
                <table id="orders-table" class="table table-head-fixed text-nowrap">
                    <thead>
                        <td>ID</td>
                        <td>Order time</td>
                        <td>Branch</td>
                        <td>Customer name</td>
                        <td>Customer area</td>
                        <td>Status</td>


                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>








