@inject( 'menu' , 'App\Services\NavigationService' )

<md-sidenav class="md-sidenav-left" ng-class="{ slimMenu : !app.fullMenu }" md-component-id="main" md-is-locked-open="true" style="background-color:#C5CAE9;">
    <md-toolbar layout-align="center center">
        <h3>MT2</h3>
    </md-toolbar>

    <md-list layout-align="center start">
        <md-list-item ng-click="app.toggleMenuSize()" aria-label="Menu Toggle">
            <span flex></span>

            <md-icon md-svg-icon="/img/icons/ic_menu_black_36px.svg"></md-icon>

            <span flex></span>
        </md-list-item>

    @foreach ( $menu->getMenu() as $current )
        <md-divider ng-if="app.fullMenu"></md-divider>
        <md-list-item ng-click="app.redirect( '{{ '/' . $current[ 'uri' ] }}' )" layout-align="start center" aria-label="{{ $current[ 'name' ] }}" ng-if="app.fullMenu">
            <h4>{{ $current[ 'name' ] }}</h4>
            <span flex></span>
            <md-icon md-svg-icon="{{ $menu->getMenuIcon( $current[ 'uri' ] ) }}"></md-icon>
        </md-list-item>

        <md-list-item layout-align="start center" aria-label="{{ $current[ 'name' ] }}" ng-if="!app.fullMenu">
            <span flex></span>

            <md-menu>
                <md-button ng-click="app.openDropdownMenu( $mdOpenMenu , $event )" flex>
                    <md-icon md-svg-icon="{{ $menu->getMenuIcon( $current[ 'uri' ] ) }}"></md-icon>
                </md-button>

                <md-menu-content width="4">
                    <md-menu-item>
                        <md-button ng-click="app.redirect( '{{ '/' . $current[ 'uri' ] }}' )">Open</md-button>
                    </md-menu-item>

                    @if(isset($current['children']))

                    @foreach ( $current['children'] as $currentChild )
                    <md-menu-item>
                        <md-button ng-click="app.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )">{{ $currentChild[ 'name' ] }}</md-button>
                    </md-menu-item>
                    @endforeach

                    @endif
                </md-menu-content>
            </md-menu>

            <span flex></span>

            <md-tooltip md-direction="right">{{ $current[ 'name' ] }}</md-tooltip>
        </md-list-item>

        @if(isset($current['children']))
            @foreach ( $current['children'] as $currentChild )
            <md-list-item ng-click="app.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )" ng-if="app.fullMenu" aria-label="{{ $current[ 'name' ] }}">
                <h5 class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></h5>
                <span flex></span>
            </md-list-item>
            @endforeach
        @endif
    @endforeach

    @if(Sentinel::check())
        <md-divider ng-if="app.fullMenu"></md-divider>
        <md-list-item ng-click="app.redirect( '{{'/' . route('logout')}}' )" ng-if="app.fullMenu" aria-label="Log Out">
            <h4>Log Out</h4>
            <span flex></span>
        </md-list-item>
    @else
        <md-list-item ng-click="app.redirect( '{{'/' . route('login')}}' )" ng-if="app.fullMenu" aria-label="Log Out">
            <span flex></span>
            <h4>Log In</h4>
        </md-list-item>
    @endif
    </md-list>
</md-sidenav>
