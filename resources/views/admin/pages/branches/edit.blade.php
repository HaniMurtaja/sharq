@extends('admin.layouts.app')
<style>
   

   
</style>
@section('content')

@include('admin.includes.content-header', ['header' => 'Edit Branch', 'title' => 'Branches'])




<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">


            <div class="col-md-12">

                <div class="card card-default">




                    <div class="tab-pane table-responsive p-0" style="height: 450px;" id="timeline">
                        <div class="card-header">
                            <h3 class="card-title">Edit branches</h3>
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
                            <form id="shift-form" action="{{route('update-branch', $branch->id)}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="Branch Name" value="{{ isset($branch) ? $branch->name : '' }}">
                                            @error('branch_name')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                               
                                
                                <div class="row">
    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <select class="form-control select2" id="driver_id" name="driver_id" style="width: 100%;">
                                                <option value="" selected="selected" disabled>Drivers</option>
                                                @foreach ($drivers as $driver )
                                                    <option  {{ isset($branch) && $branch->driver_id == $driver->id ? 'selected' : '' }} value="{{$driver->id}}">{{$driver->full_name}}</option>
                                                @endforeach
                        
                                            </select>
                                            @error('driver_id')
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