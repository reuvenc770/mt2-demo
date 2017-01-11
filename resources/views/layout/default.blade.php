@inject( 'menu' , 'App\Services\NavigationService' )
<?php Assets::add(['base', 'mt2AppBase']) ?>
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
    <div>
    @include( 'layout.internal-navigation' )
    {!! Breadcrumbs::renderIfExists() !!}
        @if(Sentinel::check() && View::hasSection('cacheTag') )
        @if(Sentinel::hasAccess("tools.cache"))
        <div style="position:relative">
            <a ng-href="/tools/cacheclear?cacheTag=@yield('cacheTag', 'Entity')"  style="position: absolute; right: 0px; top:-52px; padding:6px 10px;" target="_self" class="btn btn-danger btn-sm">Clear Cache for @yield('cacheTag', 'Entity')</a>
        </div>
        @endif
        @endif
    @yield( 'content' )
    </div>
</div>

@if (Session::has('flash_notification.message'))
    <div id="flashContainer"
         ng-init="app.showToastMessage( '{{ Session::get('flash_notification.message') }}' , '{{ Session::get('flash_notification.level') }}' )"></div>
@endif

@include( 'layout.modal' )

{!! Assets::js('pageLevel') !!}
</body>
</html>
