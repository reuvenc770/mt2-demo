<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App">
        @include( 'layout.header' )

        @include( 'layout.top-nav' )

        <div id="pageContent" class="container-fluid">

            <div id="flashContainer">
                @include('flash::message')
            </div>
            {!! Breadcrumbs::renderIfExists() !!}
            @yield( 'content' )
        </div>

        @include( 'layout.modal' )

        @include( 'layout.body-footer' )

        @yield( 'pageIncludes' )
    </body>
</html>