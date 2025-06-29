<div id="driversContainer">

    <!-- Tabs navigation -->
    <ul class="nav nav-tabs row gx-1 p-1" id="driversTabs" role="tablist">
        <li class="nav-item col-4 " role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#available" type="button"
                role="tab" aria-controls="available" aria-selected="true">
                available (<span id="available_count">200</span>)
            </button>
        </li>
        <li class="nav-item col-4" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#busy" type="button"
                role="tab" aria-controls="busy" aria-selected="false">
                busy (<span id="busy_count">200</span>)
            </button>
        </li>
        <li class="nav-item col-4" role="presentation">
            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#offline" type="button"
                role="tab" aria-controls="offline" aria-selected="false">
                offline (<span id="offline_count">651</span>)
            </button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content mt-3" style="display:block !important;">
        <div class="tab-pane tab-pane-drivers fade show active" id="available" role="tabpanel"
            style="height: 500px; overflow-y: auto;" aria-labelledby="available">

            

        </div>

        <div class="tab-pane tab-pane-drivers fade" id="away" role="tabpanel"
            style="height: 500px; overflow-y: auto;" aria-labelledby="away">

        </div>
        <div class="tab-pane tab-pane-drivers fade" id="busy" style="height: 500px; overflow-y: auto;"
            role="tabpanel" aria-labelledby="busy">

        </div>
        <div class="tab-pane tab-pane-drivers fade" id="offline" style="height: 500px; overflow-y: auto;"
            role="tabpanel" aria-labelledby="offline">


        </div>

    </div>

</div>

