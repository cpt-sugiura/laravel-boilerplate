<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="errors" content="{{ json_encode($errors->all()) }}">
    <meta name="next-url" content="{{ $redirectTo }}">

    <title>管理者ログインページ</title>

    <link rel="stylesheet" href="{{ '/assets/admin/css/login.css' }}?{{ hash_file('sha1', public_path('assets/admin/css/login.css')) }}">
</head>
<body>
<div id="app">
    @include('component.progressBar')
</div>
@php
    $jsPath = public_path('assets/admin/js/login.js');
    $jsUrl = '/assets/admin/js/login.js';
@endphp
@include('component.loadBigJS',compact('jsPath', 'jsUrl'))
</body>
</html>
