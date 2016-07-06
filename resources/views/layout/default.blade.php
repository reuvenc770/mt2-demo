<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App" ng-controller="AppController as app" layout="row">
        @include( 'layout.side-nav' )

        <div layout="column" layout-fill flex>
            @include( 'layout.main-header' )

            <md-content layout-padding flex>
                <div id="flashContainer">
                    @include('flash::message')
                </div>

                @yield( 'content' )
            </md-content>
        </div>

        @include( 'layout.modal' )

        @include( 'layout.body-footer' )

        @yield( 'pageIncludes' )
    </body>
</html>
