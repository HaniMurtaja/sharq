@extends('admin.layouts.app')

@section('css')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            /* Reduced width */
            height: 20px;
            /* Reduced height */
        }

        /* Hide default HTML checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            /* Reduced height */
            width: 14px;
            /* Reduced width */
            left: 3px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #f46624;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #f46624;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(20px);
            /* Adjusted for smaller width */
            -ms-transform: translateX(20px);
            transform: translateX(20px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 20px;
            /* Adjusted for smaller height */
        }

        .slider.round:before {
            border-radius: 50%;
        }


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
@endsection
@section('content')
    @include('admin.includes.content-header', ['header' => 'Edit User', 'title' => 'Users'])

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="tab-pane table-responsive p-0" style="height: 450px;" id="timeline">
                            <div class="card-header">
                                <h3 class="card-title">Edit User</h3>
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
                                <form id="user-form" action="{{ route('update-user', $user->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf

                                    <p>Information</p>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <p style="display: inline">Locked</p>
                                                <label class="switch" style="float: right;">
                                                    <input type="checkbox" class="status-toggle" name="locked"
                                                        {{ $user->user?->locked ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                                @error('locked')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="file" id="file-upload" class="file-upload"
                                                    name="profile_photo" accept="image/*">

                                                @if ($user->getFirstMediaUrl('profile'))
                                                    <label for="file-upload" class="upload-label"
                                                        style="background-image: url('{{ $user->getFirstMediaUrl('profile') }}')"
                                                        id="upload-label">
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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>First Name</p>
                                                <input type="text" class="form-control" id="firstName" name="first_name"
                                                    placeholder="First Name"
                                                    value="{{ old('first_name', $user->first_name) }}">
                                                @error('first_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Last Name</p>
                                                <input type="text" class="form-control" id="lastName" name="last_name"
                                                    placeholder="Last Name"
                                                    value="{{ old('last_name', $user->last_name) }}">
                                                @error('last_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Email</p>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="Email" value="{{ old('email', $user->email) }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Operators Group</p>

                                                <select class="form-control groups select2" multiple="multiple"
                                                    style="width: 100%;" name="groups[]">
                                                    
                                                    @foreach ($groups as $group)
                                                        <option value="{{ $group->id }}"
                                                            {{ in_array($group->id, $group_ids ?? []) ? 'selected' : '' }}>
                                                            {{ $group->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('group_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <br>
                                    <p>Marketplace Access</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="switch">
                                                    <input type="checkbox" class="status-toggle" name="marketplace_access"
                                                        {{ $user->user?->marketplace_access ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                                @error('marketplace_access')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Password</p>
                                                <input type="password" class="form-control" id="password"
                                                    name="password">
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <p>Advanced Settings</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>MAC Address</p>
                                                <input type="text" class="form-control" id="mac_address"
                                                    name="mac_address"
                                                    value="{{ old('mac_address', $user->user?->mac_address) }}">
                                                @error('mac_address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Sim Card</p>
                                                <input type="text" class="form-control" id="sim_card"
                                                    name="sim_card" value="{{ old('sim_card', $user->user?->sim_card) }}">
                                                @error('sim_card')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>S/N</p>
                                                <input type="text" class="form-control" id="sn" name="sn"
                                                    value="{{ old('sn', $user->user?->sn) }}">
                                                @error('sn')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p>Request per Second</p>
                                                <input type="text" class="form-control" id="request_per_second"
                                                    name="request_per_second"
                                                    value="{{ old('request_per_second', $user->user?->request_per_second) }}">
                                                @error('request_per_second')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="submit"
                                                class="btn btn-block bg-gradient-primary btn-sm">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>

    <script src="//cdn.datatables.net/2.1.0/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" defer></script>


    <script>
        $(document).ready(function() {
            $('.groups').select2({
                placeholder: "Operators Group",
                allowClear: true
            });

            $('input[type="checkbox"]').each(function() {
                $(this).val(this.checked ? 1 : 0);
            });

            $('input[type="checkbox"]').change(function() {
                $(this).val(this.checked ? 1 : 0);
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
@endpush
