<header headroom tolerance="5" class="navbar navbar-fixed-top navbar-default" role="navigation">
    <div class="container-fluid">

        <ul class="nav navbar-nav navbar-left">
            <li>

                <div class="mt2-nav-toggle" type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu" data-canvas="body" ng-click="app.toggleNavSize( $event )" layout="row" layout-align="center center">
                    <md-icon md-font-set="material-icons">menu</md-icon>
                </div>
            </li>
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

