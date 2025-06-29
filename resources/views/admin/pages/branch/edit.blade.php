@extends('admin.layouts.app')


<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.3/css/bootstrap.min.css"
    integrity="sha512-oc9+XSs1H243/FRN9Rw62Fn8EtxjEYWHXRvjS43YtueEewbS6ObfXcJNyohjHqVKFPoXXUxwc+q1K7Dee6vv9g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.2/dist/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js" defer></script>
<link rel="stylesheet" href="{{ asset('new/src/css/globalForms.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/cropperCustomStyle.css') }}" />
<link rel="stylesheet" href="{{ asset('new/src/css/layout.css') }}" />


@section('title')
    Edit Branch
@endsection
@section('content')
    <div class="flex flex-col p-192 br-128 bg-gray-f9 ms-3 mt-2">
        <section class="">

            <div class="flex flex-column pb-192 border-bottom justify-between md:flex-row gap-192 mb-4">
                <p id="tabDescription" class="text-black fs-192 fw-bold">
                    <a href="{{ route('clientupdated') }}">Branch</a>
                    <span class="fs-118 gray-94">»</span>
                    <span class="">Edit Branch</span>
                </p>
            </div>


            <form action ="{{ route('branchnew.update', ['id' => $item->id]) }}" method="POST"
                class="p-4 bg-white br-96 d-flex flex-column gap-4" id="client-form" enctype="multipart/form-data">
                @csrf
                <p class="sectionTitle">Information</p>



                <div class="row gap-md-0 gap-3">

                    <div class="col-md-6 col-12">
                        <fieldset class="floating-label-input">
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $item->name) }}" />
                            <legend>Name</legend>

                        </fieldset>
                        @error('name')
                            <div class="text-danger invalid-feedback left-24px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>



                <div class="templatesActionBtns w-100 d-flex justify-content-end align-items-center" dir="ltr">
                    <div>
                        <button type="submit" id="save-client-btn" class="templateSaveBtn bg-red-a3">
                            Save
                        </button>
                    </div>
                </div>
            </form>

        </section>
    </div>




@endsection

