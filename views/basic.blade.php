<!DOCTYPE html>
<html>
    <head>
        <meta charset="{{ bloginfo( 'charset' ) }}" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>{{ wp_title('|', true, 'right'); }}</title>
        {{ wp_head() }}
    </head>
    <body class="@yield('body_class')">
        <div class="container">
            @yield('content')
        </div>

        {{ wp_footer() }}
    </body>
</html>
