<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#0b1020">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base" content="{{ url('/api') }}">
    <meta name="description" content="A minimal feedback SPA built with Laravel, Vue, Vuex and Vue Router.">

    <title>{{ config('app.name', 'Feedback') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
