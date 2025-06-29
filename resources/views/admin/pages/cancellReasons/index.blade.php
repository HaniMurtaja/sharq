@extends('admin.layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


@section('content')


    <div class="flex flex-col p-6 overflow-hidden">

      



        
        @include('admin.pages.cancellReasons.add')
      

       


        <!-- Tabs and Button -->
        <div class="flex flex-col-reverse items-center justify-between md:flex-row">
            <div class="flex mb-4 space-x-8 overflow-x-scroll border-b md:overflow-hidden client-tabs">
                <button class="px-4 py-2 font-semibold border-b-2 border-mainColor text-mainColor client-tab"
                    data-tab="Reasons" id="reasons">
                    Reasons
                </button>
               
                
            </div>

            <div class="flex items-center justify-center mb-3 space-x-4 md:mb-0 client_btns">
                <button type="button"
                    class="flex items-center justify-center w-full h-12 gap-3 px-4 py-2 text-white rounded-md md:w-48 bg-blue1 border-blue1 open-drawer"
                    data-drawer="Reasons" id="new_reason">
                    <img src="{{ asset('new/src/assets/icons/add-square.svg') }}" alt="" />
                    <span>New</span>
                </button>
               
               
            </div>
        </div>



        <div class="p-4 bg-white border rounded-lg border-gray1">
            @include('admin.pages.cancellReasons.list')
        
       
        </div>





    </div>
    @include('admin.pages.cancellReasons.scripts')
@endsection
