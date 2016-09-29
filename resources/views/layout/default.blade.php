@inject( 'menu' , 'App\Services\NavigationService' )

<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App" ng-controller="AppController as app" layout="row" ng-cloak>

        <script type="text/javascript">
            var _config = {
                userId : {{Sentinel::check() ? Sentinel::getUser()->id: 0}},
                userName : "{{Sentinel::check() ? Sentinel::getUser()->username: 0}}"
            };
        </script>

        <div @yield( 'angular-controller' ) layout="column" layout-fill flex>
            @include( 'layout.main-header' )

         <md-content flex class="md-hue-1">

                @yield( 'content' )
            </md-content>
        </div>

        @if (Session::has('flash_notification.message'))
            <div id="flashContainer" ng-init="app.showToastMessage( '{{ Session::get('flash_notification.message') }}' , '{{ Session::get('flash_notification.level') }}' )"></div>
        @endif

        @include( 'layout.modal' )

        @include( 'layout.body-footer' )

        @yield( 'pageIncludes' )
    </body>
</html>
