<header headroom tolerance="5" class="navbar navbar-fixed-top navbar-default mt2-theme-navbar" ng-class="{ 'fixed-sidenav-offset' : app.isFixedNav() === true }" role="navigation">
    <div class="container-fluid">
        @if(Sentinel::check())
        <div id="navToggleButton" class="mt2-nav-toggle" type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#mainSideNav" data-canvas="body" data-autohide="false" data-disable-scrolling="false" layout="row" layout-align="center center" ng-show="app.isFixedNav() === false">
            <md-icon md-font-set="material-icons">menu</md-icon>
        </div>
        @endif

        <ul class="nav navbar-nav navbar-left page-action-menu">
           @yield('page-menu')
        </ul>
        <ul class="nav navbar-nav navbar-right">
            @if(Sentinel::check())
            <li><a ng-href="{{route("myprofile")}}" target="_self">{{Sentinel::getUser()->first_name}} {{Sentinel::getUser()->last_name}}</a></li>
            <li><a ng-href="{{route("logout")}}" target="_self">Logout</a></li>
            @else
                <li><a ng-href="{{route("login")}}" target="_self">Login</a></li>
            @endif
        </ul>
    </div>
</header>

