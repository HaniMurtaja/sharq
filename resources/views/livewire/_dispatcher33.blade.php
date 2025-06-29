<style>
    .orders-color {
        color: #343a40
    }
</style>

<div class="">



    <div class="container-fluid">
        <div class="row">

            <div class="col-md-4">
                <div class="card">
                    <div class="p-2 card-header">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link"></a></li>
                            <li class="nav-item"><a class="nav-link"></a></li>

                            <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Orders</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"> 
                                    On Demand
                                    <p style="color: red; display:inline">({{ $orders_count }})</p>
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="p-0 card-body">
                                 <table class="table table-responsive table-sm mous-click-new">
                                    <thead>
                                        <tr>

                                            <th></th>
                                            <th></th>



                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $order->shop?->full_name }}</td>
                                                <td>{{ $order->branch->name }}</td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                       
                                    </tfoot>
                                  
                                </table>
                                {{ $orders->links() }}
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h5 class="mb-0">
                                                <button class=" orders-color btn btn-link collapsed" data-toggle="collapse"
                                                    data-target="#allOrders" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                    All <p style="color: red; display:inline">({{$orders_count}})</p>
                                                </button>
                                            </h5>
                                        </div>
    
                                        <div id="allOrders" class="collapse" aria-labelledby="headingOne"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingTwo">
                                            <h5 class="mb-0">
                                                <button class="btn orders-color btn-link collapsed" data-toggle="collapse"
                                                    data-target="#pendingOrders" aria-expanded="false"
                                                    aria-controls="collapseTwo">
                                                   Pending <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="pendingOrders" class="collapse" aria-labelledby="headingTwo"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                                98
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#acceptedOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Accepted <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="acceptedOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#driverAtPickupOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Driver At Pickup <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="driverAtPickupOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#pickedOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Picked <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="pickedOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#driverAtDropoffOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Driver At Dropoff <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="driverAtDropoffOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#completedOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Completed <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="completedOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#canceledOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Cancelled <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="canceledOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
    
    
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapsed orders-color" data-toggle="collapse" style="color:#343a40"
                                                    data-target="#failedOrders" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                   Failed <p style="color: red; display:inline"></p>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="failedOrders" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                               test
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                

                               
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

            <div class="col-md-8">
                <div class="card card-default">
                    <div class="tab-content">
                        <div wire:ignore id="map" style="width:750px;height:600px;"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>














    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script type="text/javascript">
        window.addEventListener('updatedMapLocation', function(e) {

            @this.set('lat', e.detail.lat, true);
            @this.set('lng', e.detail.lng, true);


            let coord = new google.maps.LatLng(e.detail.lat, e.detail.lng);


            formMap.setCenter(coord);
        });
        // Function to initialize the form map



        let map;

        // Function to initialize the map
        async function initMap() {
            console.log('Initializing map...');
            try {
                const {
                    Map
                } = await google.maps.importLibrary("maps");
                map = new Map(document.getElementById("map"), {
                    zoom: 4,
                    center: {
                        lat: @js($lat),
                        lng: @js($lng)
                    },
                    mapId: "DEMO_MAP_ID",
                });
                console.log('Map initialized successfully');
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        }



        document.addEventListener('livewire:init', () => {
            Livewire.on('openModal', (event) => {
                console.log('Modal opened');
                setTimeout(() => {
                    console.log('Attempting to initialize maps');
                    const formMapElement = document.getElementById("formMap");
                    const formMap2Element = document.getElementById("formMap2");
                    console.log('formMap:', formMapElement);
                    console.log('formMap2:', formMap2Element);

                    if (formMapElement) {
                        initFormMap();
                    } else {
                        console.error('formMap element not found');
                    }

                    if (formMap2Element) {
                        initFormMap2();
                    } else {
                        console.error('formMap2 element not found');
                    }


                    document.querySelectorAll('.nextBtn').forEach(button => {
                        button.addEventListener('click', () => {

                            console.log('clicked')

                            setTimeout(() => {

                                const button2 = document.getElementById(
                                    'nextBtn2');
                                button.addEventListener('click', () => {
                                    setTimeout(() => {
                                        if (
                                            formMap2Element
                                        ) {
                                            initFormMap2();
                                        }

                                    }, 1000);
                                });

                            }, 1000);
                        });
                    });


                }, 1000);
            });
        });
    </script>
