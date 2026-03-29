@php
    $viteManifest = public_path('build/manifest.json');
@endphp

@if (file_exists($viteManifest))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endif
