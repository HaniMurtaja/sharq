@extends('admin.layouts.app')

<style>
    .file-upload {
        display: none;
    }

    .upload-label {
        display: inline-block;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        background-color: #f0f0f0;
        border: 2px solid #ccc;
        background-size: cover;
        background-position: center;
    }

    .upload-label svg {
        width: 50%;
        height: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        fill: #aaa;
    }

    .file-upload:focus+.upload-label,
    .file-upload:active+.upload-label {
        outline: 2px solid #007bff;
    }
</style>

@section('content')
    @include('admin.includes.content-header', ['header' => 'Edit Group', 'title' => 'Groups'])

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="settings">
                            <div class="card-header">
                                <h3 class="card-title"> Edit Group</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="group-form" action="{{ route('edit-group', $group) }}" method="POST">
                                    @csrf
                                    <p>Detail</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Group Name</p>
                                                <input type="text" class="form-control" name="group_name"
                                                    placeholder="Group Name" value="{{ $group->name ?? '' }}">
                                                @error('group_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Minimum free per order</p>
                                                <input type="text" class="form-control" name="min_feed_order"
                                                    placeholder="Minimum free per order"
                                                    value="{{ $group->min_feed_order ?? '' }}">
                                                @error('min_feed_order')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <p>Feeds per order</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" style="width: 100%;"
                                                    name="type_feed_order">
                                                    <option value="" selected="selected" disabled>Type</option>
                                                    @foreach (App\Enum\FeedType::values() as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ $group->type_feed_order->value == $key ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                </select>
                                                @error('type_feed_order')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <p>Condition</p>
                                    <p>Add % To Consider The Percentage Of Delivery Fee</p>
                                    <div id="repeater">
                                        @foreach ($group->conditions()->where('feed_type', 'main')->get() as $condition)
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control select2" style="width: 100%;"
                                                            name="type[]">
                                                            <option value="Between"
                                                                {{ $condition->data['type'] === 'Between' ? 'selected' : '' }}>
                                                                Between</option>
                                                            <option value=">"
                                                                {{ $condition->data['type'] === '>' ? 'selected' : '' }}>>
                                                            </option>
                                                            <option value="<"
                                                                {{ $condition->data['type'] === '<' ? 'selected' : '' }}><</option>
                                                        </select>
                                                        <input hidden name="condition_id[]" value="{{ $condition->id }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-9 d-flex align-items-center">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="from[]"
                                                            placeholder="From" value="{{ $condition->data['from'] ?? '' }}">
                                                        <span class="text-danger" id="from_error"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="to[]"
                                                            placeholder="To" value="{{ $condition->data['to'] ?? '' }}">
                                                        <span class="text-danger" id="to_error"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="percentage[]"
                                                            placeholder="Percentage"
                                                            value="{{ $condition->data['percentage'] ?? '' }}">
                                                        <span class="text-danger" id="percentage_error"></span>
                                                    </div>
                                                    <button type="button" class="btn btn-add mx-1" style="color: green;">
                                                        <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-delete" style="color: red;">
                                                        <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p>Additional feeds per order</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" style="width: 100%;"
                                                    name="additional_type_feed">
                                                    <option value="" selected="selected" disabled>Type</option>
                                                    @foreach (App\Enum\FeedType::values() as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ $group->additional_feed_order->value == $key ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <p>Condition</p>
                                    <p>Add % To Consider The Percentage Of Delivery Fee</p>
                                    <div id="repeater2">
                                        @foreach ($group->conditions()->where('feed_type', 'additional')->get() as $condition)
                                            <div class="row mb-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select class="form-control select2" style="width: 100%;"
                                                            name="additional_type[]">
                                                            <option value="Between"
                                                                {{ $condition->data['type'] === 'Between' ? 'selected' : '' }}>
                                                                Between</option>
                                                            <option value=">"
                                                                {{ $condition->data['type'] === '>' ? 'selected' : '' }}>>
                                                            </option>
                                                            <option value="<"
                                                                {{ $condition->data['type'] === '<' ? 'selected' : '' }}>
                                                                < </option>
                                                        </select>
                                                        <input hidden name="condition_id_add[]"
                                                            value="{{ $condition->id }}">

                                                    </div>
                                                </div>

                                                <div class="col-md-9 d-flex align-items-center">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            name="additional_from[]" placeholder="From"
                                                            value="{{ $condition->data['from'] ?? '' }}">
                                                        <span class="text-danger" id="additional_from_error"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="additional_to[]"
                                                            placeholder="To" value="{{ $condition->data['to'] ?? '' }}">
                                                        <span class="text-danger" id="additional_to_error"></span>
                                                    </div>

                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            name="additional_percentage[]" placeholder="Percentage"
                                                            value="{{ $condition->data['percentage'] ?? '' }}">
                                                        <span class="text-danger" id="additional_percentage_error"></span>
                                                    </div>
                                                    <button type="button" class="btn btn-add mx-1"
                                                        style="color: green;">
                                                        <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-delete" style="color: red;">
                                                        <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block bg-gradient-primary btn-sm"
                                                id="save-group-btn">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to update delete button status
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

            // Function to add a new row
            function addRow(repeaterId) {
                var repeater = document.getElementById(repeaterId);
                var newRow = document.createElement('div');
                newRow.classList.add('row', 'mb-2');

                newRow.innerHTML = `
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select2" style="width: 100%;" name="type">
                            <option value="" selected="selected" disabled>Between</option>
                            <option>></option>
                            <option><</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9 d-flex align-items-center">
                    <div class="form-group">
                        <input type="text" class="form-control" name="from[]" placeholder="From">
                        <span class="text-danger" id="from_error"></span>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="to[]" placeholder="To">
                        <span class="text-danger" id="to_error"></span>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="percentage[]" placeholder="Percentage">
                        <span class="text-danger" id="percentage_error"></span>
                    </div>
                    <button type="button" class="btn btn-add mx-1" style="color: green;">
                        <i class="fas fa-plus" style="font-size: 0.8em;"></i>
                    </button>
                    <button type="button" class="btn btn-delete" style="color: red;">
                        <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                    </button>
                </div>
            `;

                // Append the new row and update delete button status
                repeater.appendChild(newRow);
                updateDeleteButtons(repeaterId);
            }

            // Function to delete a row
            function deleteRow(button) {
                var row = button.closest('.row');
                row.remove();
                updateDeleteButtons(row.parentNode.id);
            }

            // Event listener for adding rows
            $('#repeater').on('click', '.btn-add', function() {
                addRow('repeater');
            });

            $('#repeater2').on('click', '.btn-add', function() {
                addRow('repeater2');
            });

            // Event listener for deleting rows
            $('#repeater').on('click', '.btn-delete', function() {
                deleteRow(this);
            });

            $('#repeater2').on('click', '.btn-delete', function() {
                deleteRow(this);
            });

            // Initial update of delete buttons
            updateDeleteButtons('repeater');
            updateDeleteButtons('repeater2');


        });
    </script>
@endsection
