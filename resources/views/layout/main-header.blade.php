<md-toolbar>
    <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="Open Navigation" ng-click="app.toggleMenu( 'mainNav' )">
            <md-icon md-svg-icon="img/icons/ic_menu_white_36px.svg"></md-icon>
        </md-button>

        <h2><span>MT2</span></h2>

        <span>&nbsp;&nbsp;&nbsp;</span>

        <span flex></span>

        @if(Sentinel::check())
            <md-menu>
                <md-button ng-click="app.openDropdownMenu( $mdOpenMenu , $event )">{{Sentinel::getUser()->first_name}} {{Sentinel::getUser()->last_name}}</md-button>
                <md-menu-content width="4">
                    <md-menu-item>
                        <md-button ng-href="{{route("myprofile")}}" target="_self">My Profile</md-button>
                    </md-menu-item>

                    <md-menu-item>
                        <md-button ng-href="{{route("logout")}}" target="_self">Logout</md-button>
                    </md-menu-item>
                </md-menu-content>
            </md-menu>
        @else
            <md-button ng-href="{{route("login")}}" target="_self">Login</md-button>
        @endif
    </div>
</md-toolbar>

<md-toolbar class="md-accent">
    <div class="md-toolbar-tools">
    {!! Breadcrumbs::renderIfExists() !!}

    <span flex></span>

    @yield( 'page-menu' )
    </div>

</md-toolbar>
