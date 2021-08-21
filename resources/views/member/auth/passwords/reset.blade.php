<!doctype html>
<html lang="ja">
<head>
    <title>パスワードリセット</title>
    <meta charset="UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="password-reset-token" content="{{ $token }}">
    <meta name="errors" content="{{ json_encode($errors->all()) }}">

    <title>会員パスワードリセットページ</title>
</head>
<body>
<div id="app">
    @include('component.progressBar')
</div>
</body>
@php
    $jsPath = public_path('assets/member/js/password_reset.js');
    $jsUrl = loose_secure_asset('assets/member/js/password_reset.js');
@endphp
@include('component.loadBigJS',compact('jsPath', 'jsUrl'))
</html>
