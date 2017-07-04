<!DOCTYPE html>
<html lang="en">
<?php
date_default_timezone_set('America/Los_Angeles');
?>
    <head>
        {!! meta_init() !!}
        <meta name="keywords" content="@get('keywords')">
        <meta name="description" content="@get('description')">
        <meta name="author" content="@get('author')">
        <input type="hidden" id="currentDate" data-date="<?php echo date('mdy',time()); ?>">
        <title>@get('title')</title>

        @styles()
        @scripts()
        
    </head>

    <body>
        @partial('header')

        @content()

        @partial('footer')
    </body>

</html>
