@inject( 'menu' , 'App\Services\NavigationService' )
<?php Assets::add(['base', 'mt2BootstrapBase']) ?>
        <!DOCTYPE html>
<html lang="en">
<head>
    @include( 'layout.html-head' )
    {!! Assets::js() !!}
</head>
<body ng-app="mt2App" ng-controller="AppController as app" ng-init="app.currentPath = '{{ Request::path() }}'" ng-cloak>

{!! $menu->getMenuHtmlBootStrap() !!}
<script type="text/javascript">
    var _config = {
        userId: {{Sentinel::check() ? Sentinel::getUser()->id: 0}},
        userName: "{{Sentinel::check() ? Sentinel::getUser()->username: 0}}",
        emailAddress: "{{Sentinel::check() ? Sentinel::getUser()->email: 0}}"
    };
</script>
<div @yield( 'angular-controller' ) id="containerSizer" class="@yield('container', 'container') pinned-container"  ng-cloak>
    @include( 'bootstrap.layout.internal-navigation' )
    {!! Breadcrumbs::renderIfExists() !!}
    @yield( 'content' )
</div>

@if (Session::has('flash_notification.message'))
    <div id="flashContainer"
         ng-init="app.showToastMessage( '{{ Session::get('flash_notification.message') }}' , '{{ Session::get('flash_notification.level') }}' )"></div>
@endif

@include( 'layout.modal' )

{!! Assets::js('pageLevel') !!}
</body>
</html>
