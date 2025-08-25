<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dynamic Form</title>
    @filamentStyles
    @livewireStyles
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-50">
        @yield('content')
    </div>

    @livewireScripts
    @filamentScripts
</body>
</html>
