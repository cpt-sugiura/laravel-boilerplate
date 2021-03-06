<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>管理者画面</title>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="minimum-scale=1, initial-scale=1, width=device-width"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Nunito:200,600">
    @php
        $cssPath = (new \App\Models\Eloquents\Admin)->assetStorage()->path('css/app.css');
        $cssUrl = '/admin/storage/css/app.css';
    @endphp
    <link rel="stylesheet"
          href="{{ $cssUrl }}?{{ hash_file('sha1', $cssPath) }}">
</head>
<body>
<div id="app">
    @include('component.progressBar')
</div>
@php
    $jsPath = (new \App\Models\Eloquents\Admin)->assetStorage()->path('js/app.js');
    $jsUrl = '/admin/storage/js/app.js';
@endphp
@include('component.loadBigJS',compact('jsPath', 'jsUrl'))
</body>
</html>
