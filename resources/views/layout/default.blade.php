<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App">
        @include( 'layout.header' )

        @include( 'layout.top-nav' )

        <div id="pageContent" class="container-fluid fullHeight">
            @yield( 'content' )
        </div>

        @include( 'layout.body-footer' )

        @yield( 'pageIncludes' )
    </body>
</html>
