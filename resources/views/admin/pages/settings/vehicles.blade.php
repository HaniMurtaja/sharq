<div class="w-full p-6 bg-white border rounded border-gray1 settings_content" data-system="vehicles" id="vehicles">
    <h2 class="mb-6 text-base font-medium">Vehicle Types</h2>
    <form action="{{ route('save-vehicle-types') }}" method="post">
        @csrf
        <div id="repeater">



            @if (empty($settings->vehicle_types))
                <div class="row mb-2">
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" class="form-control" name="vehicle_types[]" placeholder="Type">
                            @error('vehicle_types.*')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <span class="text-danger" id="from_error"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-add mx-1" style="color: green;">
                            <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                        </button>
                        <button type="button" class="btn btn-delete" style="color: red;">
                            <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                        </button>
                    </div>
                </div>
            @else
                @foreach ($settings->vehicle_types as $type)
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input type="text" class="form-control" name="vehicle_types[]"
                                    value="{{ $type }}" placeholder="Type">
                                @error('vehicle_types.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <span class="text-danger" id="from_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-add mx-1" style="color: green;">
                                <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                            </button>
                            <button type="button" class="btn btn-delete" style="color: red;">
                                <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif


        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col justify-center gap-3 mt-6 md:flex-row">
           
            <button type="submit" class="px-16 py-3 mr-4 font-bold text-white rounded-md border-gray1 bg-blue1">
                Save
            </button>
        </div>
    </form>
</div>

<script>
    function updateDeleteButtons(repeaterId) {
        var repeater = document.getElementById(repeaterId);
        var rows = repeater.querySelectorAll('.row');
        rows.forEach(function(row, index) {
            var deleteButton = row.querySelector('.btn-delete');
            if (rows.length === 1) {
                deleteButton.disabled = true;
            } else {
                deleteButton.disabled = false;
            }
        });
    }

    function addRow(repeaterId) {
        var repeater = document.getElementById(repeaterId);
        var newRow = document.createElement('div');
        newRow.classList.add('row', 'mb-2');

        newRow.innerHTML = `
                 <div class="col-md-8 ">
                    <div class="form-group">
                        <input type="text" class="form-control" name="vehicle_types[]" placeholder="Type">
                        <span class="text-danger" id="from_error"></span>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <button type="button" class="btn btn-add mx-1" style="color: green;">
                        <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                    </button>
                    <button type="button" class="btn btn-delete" style="color: red;">
                        <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                    </button>
                </div>
            `;

        repeater.appendChild(newRow);

        // Add event listeners for new buttons
        newRow.querySelector('.btn-add').addEventListener('click', function() {
            addRow(repeaterId);
            updateDeleteButtons(repeaterId);
        });
        newRow.querySelector('.btn-delete').addEventListener('click', function() {
            newRow.remove();
            updateDeleteButtons(repeaterId);
        });

        updateDeleteButtons(repeaterId);
    }

    document.querySelectorAll('.btn-add').forEach(function(button) {
        button.addEventListener('click', function() {
            var repeaterId = this.closest('[id^="repeater"]').id;
            addRow(repeaterId);
            updateDeleteButtons(repeaterId);
        });
    });
    document.querySelectorAll('.btn-delete').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = button.closest('.row');
            var repeaterId = row.closest('[id^="repeater"]').id;
            row.remove();
            updateDeleteButtons(repeaterId);
        });
    });
</script>
