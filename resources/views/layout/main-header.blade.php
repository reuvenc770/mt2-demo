<md-toolbar>
    <div class="md-toolbar-tools">
    @if(Sentinel::check())
        <md-button class="md-icon-button" aria-label="Open Navigation" ng-click="app.toggleMenu( 'mainNav' )" ng-hide="app.largePageWidth()">
            <md-icon md-svg-icon="img/icons/ic_menu_white_36px.svg"></md-icon>
        </md-button>

        <md-button class="md-icon-button" aria-label="Open Navigation" ng-click="app.lockSideNav=!app.lockSideNav" ng-show="app.largePageWidth()">
            <md-icon md-svg-icon="img/icons/ic_menu_white_36px.svg"></md-icon>
        </md-button>
    @endif

        <h2><span>MT2</span></h2>

        <span>&nbsp;&nbsp;&nbsp;</span>

        <span flex></span>

        @if(Sentinel::check())
            <md-button ng-href="{{route("myprofile")}}" target="_self">{{Sentinel::getUser()->first_name}} {{Sentinel::getUser()->last_name}}</md-button>
            <md-button ng-href="{{route("logout")}}" target="_self">Logout</md-button>

        @else
            <md-button ng-href="{{route("login")}}" target="_self">Login</md-button>
        @endif
    </div>
</md-toolbar>

<md-menu-bar>
    <div class="md-toolbar-tools mt2-sub-toolbar">
    {!! Breadcrumbs::renderIfExists() !!}

    <span flex></span>

    @yield( 'page-menu' )
    </div>

</md-menu-bar>
