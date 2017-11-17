<!DOCTYPE html>
<html lang="en">

    <head>
        {!! meta_init() !!}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="@get('keywords')">
        <meta name="description" content="@get('description')">
        <meta name="author" content="@get('author')">

        <title>@get('title')</title>
        {{--<input type="hidden" id="userId" value="{{ htmlspecialchars(Auth::id()) }}">--}}
        {{--<input type="hidden" id="userId" value="248">--}}
        <input type="hidden" id="userId" value="<?php
            if (!isset($_SESSION)) session_start();
            if(!isset($_SESSION["username"])){
                // NULL
            }else{
                echo $_SESSION["username"];
            }
        ?>">
        <input type="hidden" id="hdnURL" value="{{ URL::to('/') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @styles()
        @scripts()

    </head>

    <body>
        @partial('header')

        @content()

        @partial('footer')
    </body>

</html>
