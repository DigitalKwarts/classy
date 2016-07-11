<!DOCTYPE html>
<html>
    <head>
        <meta charset="{{ bloginfo( 'charset' ) }}" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="pingback" href="{{ bloginfo('pingback_url') }}" />
        <link rel="alternate" type="application/rss+xml" title="{{ bloginfo('name') }} RSS Feed" href="{{ bloginfo('rss2_url') }}" />
        <link rel="shortcut icon" href="{{ CLASSY_THEME_DIR }}assets/favicon.ico" />
        <title>{{ wp_title('|', true, 'right'); }}</title>
        {{ wp_head() }}
    </head>
    <body {{ body_class() }}>

        {{ get_header() }}

        <div class="container">
            @yield('content')
        </div>

        {{ get_footer() }}

        {{ wp_footer() }}
    </body>
</html>
