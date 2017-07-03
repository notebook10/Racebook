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
        <input type="hidden" id="userId" value="{{ htmlspecialchars(Auth::id()) }}">
        @styles()
        @scripts()

    </head>

    <body>
        @partial('header')

        @content()

        @partial('footer')
    </body>

</html>
