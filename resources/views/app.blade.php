<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title inertia>Link</title>
        <!-- Fonts -->
        <link rel="stylesheet" href="https://static2.sharepointonline.com/files/fabric/office-ui-fabric-core/11.0.0/css/fabric.min.css" />
        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <!-- Scripts -->
        @routes

        @env ('local')
        <script src="{{ mix('lib/link.js') }}" defer></script>
        @endenv

        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://unpkg.com/chart.js@3"></script>
        <script src="https://unpkg.com/chartjs-chart-geo@3"></script>
        <script src="{{ mix('js/app.js') }}" defer></script>

    </head>
    <body class="ms-Fabric antialiased" dir="ltr">
        @inertia

        @env ('local')
            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
        @endenv
    </body>
</html>
