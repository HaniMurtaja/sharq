@extends('admin.layouts.app')
<style>
   

   
</style>
@section('content')

@include('admin.includes.content-header', ['header' => 'Edit Country', 'title' => 'Locations'])




<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">


            <div class="col-md-12">

                <div class="card card-default">




                    <div class="tab-pane table-responsive p-0" style="height: 450px;" id="timeline">
                        <div class="card-header">
                            <h3 class="card-title">Edit country</h3>
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
                            <form id="city-form" action="{{route('update-country', $country->id)}}" method="POST">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="country_name" name="country_name" placeholder="Country Name" value="{{ isset($country) ? $country->name : '' }}">
                                            @error('country_name')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                               
                                
                               
                                <div class="row">
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-block bg-gradient-primary btn-sm" id="save-shift-btn">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>







                </div>

                <!-- /.col -->

                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
</section>


@endsection