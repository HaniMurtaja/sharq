@extends('admin.layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


@section('content')


    <div class="flex flex-col p-6 overflow-hidden">

      
        <div id="drawer-overlay" data-drawer="Integration" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 drawer-overlay">
        </div>

        @include('admin.pages.integrations.add-edit')
        @include('admin.pages.webhooks.add-edit')
        
        <div class="flex flex-col-reverse items-center justify-between md:flex-row">
          

            <div class="flex mb-4 space-x-8 overflow-x-scroll border-b md:overflow-hidden client-tabs">
                <button class="px-4 py-2 font-semibold border-b-2 border-mainColor text-mainColor client-tab"
                data-tab="Integration"  id="integration">
                Integration Companies
                    
                </button>
             
                <button class="px-4 py-2 text-gray-600 client-tab" data-tab="Webhook" id="webhooks">
                    WebHook
                </button>
            </div>



            <div class="flex items-center justify-center mb-3 space-x-4 md:mb-0 client_btns">
                <button type="button"
                    class="flex items-center justify-center w-full h-12 gap-3 px-4 py-2 text-white rounded-md md:w-48 bg-blue1 border-blue1 open-drawer"
                    data-drawer="Integration" id="new-integration">
                    <img src="{{ asset('new/src/assets/icons/add-square.svg') }}" alt="" />
                    <span>New</span>
                </button>
               
                <button type="button"
                    class="items-center justify-center hidden w-full h-12 gap-3 px-4 py-2 text-white rounded-md md:w-48 bg-blue1 border-blue1 open-drawer"
                    data-drawer="Webhook" id="new-webhook">
                    <img src="{{ asset('new/src/assets/icons/add-square.svg') }}" alt="" />
                    <span>New</span>
                </button>
            </div>

           
        </div>


       


       

        <div class="p-4 bg-white border rounded-lg border-gray1">
            @include('admin.pages.integrations.list')
         
            @include('admin.pages.webhooks.list')
        </div>

     





    </div>
    @include('admin.pages.integrations.scripts')
@endsection
