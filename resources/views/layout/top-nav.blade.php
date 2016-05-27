@inject( 'menu' , 'App\Services\NavigationService' )

<nav id="adminNav" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation" ng-controller="HelperController as helper">
    <md-list>
    @foreach ( $menu->getMenu() as $current )
        <md-list-item ng-click="helper.redirect( '{{ '/' . $current[ 'uri' ] }}' )" layout-align="start center">
            <h4>{{ $current[ 'name' ] }}</h4>
            <span flex></span>
            <md-icon md-svg-icon="{{ $menu->getMenuIcon( $current[ 'uri' ] ) }}"></md-icon>
        </md-list-item>

        @if(isset($current['children']))
            @foreach ( $current['children'] as $currentChild )
            <md-list-item ng-click="helper.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )">
                <h5 class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></h5>
                <span flex></span>
            </md-list-item>
            @endforeach
        @endif
    @endforeach
        @if(Sentinel::check())
            <md-list-item ng-click="helper.redirect( '{{'/' . route('logout')}}' )">
                <h4>Log Out</h4>
                <span flex></span>
            </md-list-item>
        @else
            <md-list-item ng-click="helper.redirect( '{{'/' . route('login')}}' )">
                <span flex></span>
                <h4>Log In</h4>
            </md-list-item>
        @endif
    </md-list>
</nav>
