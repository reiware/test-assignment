<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Documents Storage')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">@yield('heading', 'File Storage')</h1>

                <div>
                    <a href="{{ route('files.create') }}" class="btn btn-outline-primary btn-sm">
                        Upload
                    </a>

                    <a href="{{ route('files.index') }}" class="btn btn-outline-secondary btn-sm">
                        Files
                    </a>
                </div>
            </div>

            <div id="alert" class="alert d-none"></div>

            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
