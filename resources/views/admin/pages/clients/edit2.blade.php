@extends('admin.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
        background-color: #e9ecef;
    }


    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;

    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .475rem .75rem;
        line-height: 1.5;
        vertical-align: middle;
    }
</style>
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
    @include('admin.includes.content-header', ['header' => 'Edit Client', 'title' => 'Clients'])

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="settings">
                            <div class="card-header">
                                <h3 class="card-title"> Edit Client</h3>
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
                                <form id="client-form" enctype="multipart/form-data"
                                    action="{{ route('update-client', $client->id) }}" method="post">
                                    @method('PUT')
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="file" id="file-upload" class="file-upload"
                                                    name="profile_photo" accept="image/*">
                                                    @if ($client->getFirstMediaUrl('profile'))
                                                    <label for="file-upload" class="upload-label" style="background-image: url('{{$client->getFirstMediaUrl('profile')}}')" id="upload-label">
                                                    </label>

                                                    @else
                                                    <label for="file-upload" class="upload-label" id="upload-label">

                                                        <svg viewBox="0 0 24 24" id="user-icon">
                                                            <path
                                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                        </svg>
                                                    </label>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row">
                                        <div class="col-md-6">
                                            <p for="id_card_image" class="form-label">Personal Photo</p>
                                            @if ($client->getFirstMediaUrl('profile'))
                                                <img id="profileImagePreview"
                                                    src="{{ $client->getFirstMediaUrl('profile') }}"
                                                    alt="Current Profile Image"
                                                    style="max-width: 300px; max-height:300px; display: block" />
                                            @else
                                                <img id="profileImagePreview" src="#" alt="Current Profile Image"
                                                    style="max-width: 300px; max-height: 300px; display: none;" />
                                            @endif
                                            <br>
                                            <input name="profile_photo" class="file-upload form-control form-control-sm"
                                                id="id_card_image" accept="image/*" type="file"
                                                onchange="previewImage(event)" />
                                            @error('profile_photo')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input hidden name="account_number" id="account_number"
                                                       value="{{ $client->account_number }}">
                                                <input type="text" class="form-control" id="account_number" name="account_number"
                                                       placeholder="account number" value="{{ old('account_number', $client->account_number) }}">

                                                @error('account_number')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="firstName" name="name"
                                                    placeholder="Name" value="{{ old('name', $client->first_name) }}">

                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="lastName" name="phone"
                                                    placeholder="Phone Number" value="{{ old('phone', $client->phone) }}">
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" style="width: 100%;" name="countery">
                                                    <option value="" selected="selected" disabled>Country</option>
                                                    <option
                                                        {{ old('countery', $client->client->countery) == 'Saudi Arabia' ? 'selected' : '' }}
                                                        value="Saudi Arabia">Saudi Arabia</option>
                                                </select>
                                                @error('countery')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" name="city_id" style="width: 100%;">
                                                    <option value="" selected="selected" disabled>City</option>
                                                    @foreach ($all_cities as $city)
                                                        <option
                                                            {{ old('city_id', $client->client->city_id) == $city->id ? 'selected' : '' }}
                                                            value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('city_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" style="width: 100%;"
                                                    name='currency'>
                                                    <option value="" selected="selected" disabled>Currency</option>
                                                    <option
                                                        {{ old('currency', $client->client->currency) == 'SAR' ? 'selected' : '' }}
                                                        value="SAR">SAR</option>
                                                </select>
                                                @error('currency')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="defaultPreparationTime"
                                                    name="default_prepartion_time"
                                                    value="{{ old('name', $client->client->default_prepartion_time) }}"
                                                    placeholder="Default preparation time">
                                                @error('default_prepartion_time')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="minPreparationTime"
                                                    name="min_prepartion_time"
                                                    value="{{ old('name', $client->client->min_prepartion_time) }}"
                                                    placeholder="Min. preparation time">
                                                @error('min_prepartion_time')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="partialPay"
                                                    name="partial_pay"
                                                    value="{{ old('partial_pay', $client->client->partial_pay) }}"
                                                    placeholder="Partial pay">
                                                @error('partial_pay')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="note" name="note"
                                                    placeholder="Note" value="{{ old('note', $client->client->note) }}">
                                                @error('note')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <p>Group</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" style="width: 100%;"
                                                    name="client_group_id">
                                                    <option value="" selected="selected" disabled>Client group
                                                    </option>
                                                    @foreach ($client_groups as $group)
                                                        <option
                                                            {{ old('client_group_id', $client->client->client_group_id) == $group->id ? 'selected' : '' }}
                                                            value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('client_group_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="form-control select2" style="width: 100%;"
                                                    name="driver_group_id">
                                                    <option value="" selected="selected" disabled>Driver group
                                                    </option>
                                                    @foreach ($driver_groups as $group)
                                                        <option value="{{ $group->id }}"
                                                            {{ old('driver_group_id', $client->client->driver_group_id) == $group->id ? 'selected' : '' }}>
                                                            {{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('driver_group_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit" id="save-client-btn"
                                                class="btn btn-block bg-gradient-primary btn-sm">Save</button>
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

            $('.select2').select2({
                allowClear: true
            });



        });

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('profileImagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }


        const fileUpload = document.getElementById('file-upload');
        const uploadLabel = document.getElementById('upload-label');
        const userIcon = document.getElementById('user-icon');

        fileUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadLabel.style.backgroundImage = `url(${e.target.result})`;
                    userIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
