<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Servicio de Salud') }} @yield('title')</title>

    <link href="{{ asset('favicon-'. env('APP_ENV') .'.ico') }}"
        rel="icon" type="image/x-icon">

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->
    <script src="{{asset('js/custom.js')}}"></script>
    @yield('custom_js_head')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="{{ asset('css/nunito.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l"
        crossorigin="anonymous">
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/intranet.css') }}" rel="stylesheet">

    <style media="screen">
        .bg-nav-gobierno {
            @switch(env('APP_ENV'))
                @case('local') background-color: rgb(73, 17, 82); @break
                @case('testing') background-color: rgb(2, 82, 0); @break
                @case('production')
                    @if(env('APP_DEBUG') == true)
                        background-color: rgb(255, 0, 0);
                    @endif
                    @break;
            @endswitch
        }
    </style>
    @yield('custom_css')

    <!-- Place your kit's code here -->
    <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" data-mutate-approach="sync"></script>

    @livewireStyles
</head>
<body>
    <div id="app">
    <!-- TODO ¿QUÉ PASA SI UN INTERNO QUIERE SER PARTE DE STAFF? -->
        @guest
            @include('layouts.partials.nav')
        @else
            @if(Auth::user()->external )
                @include('layouts.partials.nav_external')
            @else
                @include('layouts.partials.nav')
            @endif
        @endGuest
        <main class="container pt-3">
            <div class="d-none d-print-block">
                <strong>{{ env('APP_SS') }}</strong><br>
                Ministerio de Salud
            </div>
            @include('layouts.partials.errors')
            @include('layouts.partials.flash_message')
            @yield('content')
        </main>

        <footer class="footer">
            <div class="col-8 col-md-6 d-inline-block text-white"
                style="background-color: rgb(0,108,183);">{{ config('app.ss', 'Servicio de Salud') }}</div>
            <div class="col-4 col-md-6 float-right text-white"
                style="background-color: rgb(239,65,68);"> © {{ date('Y') }}</div>
        </footer>
    </div>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"
        integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg=="
        crossorigin="anonymous"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"
        integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF"
        crossorigin="anonymous"></script>

    <script src="https://cdn.amcharts.com/lib/version/4.9.34/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/version/4.9.34/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/version/4.9.34/themes/material.js"></script>
    <script src="https://cdn.amcharts.com/lib/version/4.9.34/themes/animated.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css"
          integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg=="
          crossorigin="anonymous"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
            integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
            crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>

    @yield('custom_js')
    @livewireScripts
</body>
</html>
