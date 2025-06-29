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
    @include('admin.includes.content-header', ['header' => 'Locations', 'title' => 'Locations'])




    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#countries_menu" id="countries"
                                        data-toggle="tab">Countries</a>
                                </li>

                                <li class="nav-item"><a class="nav-link" href="#cities_menu" id="cities"
                                        data-toggle="tab">Cities</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#areas_menu" data-toggle="tab"
                                        id="areas">Areas</a></li>

                                </li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div id="table-list">
                            @include('admin.pages.countries.list')
                        </div>
                    </div>
                    <!-- /.card -->
                </div>


                <div class="col-md-8">

                    <div class="card card-default">
                        <div class="tab-content">



                            @include('admin.pages.countries.add')

                            @include('admin.pages.cities.add')

                            @include('admin.pages.areas.add')




                        </div>



                    </div>

                    <!-- /.col -->

                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        </div><!-- /.container-fluid -->
    </section>
   @include('admin.pages.countries.scripts')
@endsection
