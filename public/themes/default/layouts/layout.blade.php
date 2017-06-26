<!DOCTYPE html>
<html lang="en">

    <head>
        {!! meta_init() !!}
        <meta name="keywords" content="@get('keywords')">
        <meta name="description" content="@get('description')">
        <meta name="author" content="@get('author')">
    
        <title>@get('title')</title>

        @styles()
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        @scripts();
    </head>

    <body>
        @partial('header')

        @content()

        @partial('footer')

        @scripts()
    </body>

</html>
