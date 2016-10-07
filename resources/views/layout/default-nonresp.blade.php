
@inject( 'menu' , 'App\Services\NavigationService' )

<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App" ng-controller="AppController as app" ng-init="app.currentPath = '{{ Request::path() }}'" layout="row" flex="none" ng-cloak>
        {!! $menu->getMenuHtml() !!}
        <script type="text/javascript">
            var _config = {
                userId : {{Sentinel::check() ? Sentinel::getUser()->id: 0}}
            };
        </script>

        <div @yield( 'angular-controller' ) layout="column" layout-fill flex="none">
            @include( 'layout.main-header' )

            <md-content flex="none" class="md-hue-1">

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
