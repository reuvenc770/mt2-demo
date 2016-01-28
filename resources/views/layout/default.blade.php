<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body>
        @include( 'layout.header' )

        @include( 'layout.top-nav' )

        <div class="container-fluid fullHeight">
            @yield( 'content' )
        </div>

        @include( 'layout.body-footer' )
    </body>
</html>
