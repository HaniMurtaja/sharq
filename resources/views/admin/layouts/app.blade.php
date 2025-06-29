<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'en' ? 'ltr' : 'rtl' }}">

<head>

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpg" href="{{ asset('new/src/assets/images/favicon (2).jpg') }}">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="preload" href="//alshrouqdelivery.b-cdn.net/public/bootstrap/css/bootstrap.min.css" as="style">
    <link rel="stylesheet" href="//alshrouqdelivery.b-cdn.net/public/bootstrap/css/bootstrap.min.css" media="print" onload="this.media='all'">

    @include('admin.includes.style')

    @yield('css')

    <title>@yield('title')</title>
    @livewireStyles
</head>

<style>
    body{
        font-family: "inter" , sans-serif !important;
        background-color: #fff !important;
    }
    .mainContent {
        width: 92%;
    left: 6.5rem;
    position: relative;

    }

    @media (max-width: 992px) {
        .mainContent {
            width: 100%;
            left: 0;
        }
        .layoutContainer{
            flex-direction: column;
        }
    }







</style>

<body>
    <main class="container-fluid">

        <div class="relative flex transition-all h-full layoutContainer">
            @include('admin.includes.sidebar')
            <div class="w-full h-full mainContent">

                @yield('content')
            </div>
            <!-- end of header -->


            {{-- @include('admin.includes.firebase') --}}



        @include('admin.includes.script')






        @livewireScripts

        @livewire('wire-elements-modal')



        <script src="//alshrouqdelivery.b-cdn.net/public/bootstrap/js/bootstrap.bundle.min.js"></script>



    @stack('scripts')
</body>

</html>
