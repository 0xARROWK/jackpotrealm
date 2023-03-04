<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Jackpot Realm</title>
        <link href="{{ mix('/app/css/app.css') }}" rel="stylesheet">
        <link rel="preload" href="{{ mix('/app/css/async.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{{ mix('/app/css/async.css') }}"></noscript>

        <script>
            window._translations = {!! session('settings.translations') !!};
        </script>

    </head>

    <body class="antialiased bg-th-body no-scrollbar">

        <div id="app" class="vh-100">
            <app></app>
        </div>

        <script src="{{ mix('/app/js/app.js') }}" defer=""></script>

    </body>

</html>
