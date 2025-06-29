<div wire:poll.3s>
    <div class="card-header p-2">
        <ul class="nav nav-pills">
       
            <li class="nav-item"><a class="nav-link" href="#availableDrivers" data-toggle="tab">
                    Available <p style="color: red; display:inline">({{ $available_orders }})</p></a></li>
            <li class="nav-item"><a class="nav-link" href="#awayDrivers" data-toggle="tab">Away <p style="color: red; display:inline">({{ $away_orders }})</p></a>
            </li>
    
            <li class="nav-item"><a class="nav-link" href="#busyDrivers" data-toggle="tab">Busy <p style="color: red; display:inline">({{ $busy_orers }})</p></a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#offlineDrivers" data-toggle="tab">Offline <p style="color: red; display:inline">({{ $offline_orders }})</p></a>
            </li>
        </ul>               
    </div><!-- /.card-header -->
    <div id="table-list">
        <div class="tab-content">
            @livewire('available-drivers')
           
        </div>

        
    </div><!-- /.card-body -->
   
</div>
