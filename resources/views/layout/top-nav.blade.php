@inject( 'menu' , 'App\Services\NavigationService' )

<nav id="adminNav" class="navmenu navmenu-default navmenu-fixed-left offcanvas" role="navigation" ng-controller="HelperController as helper">
    <md-list>
    @foreach ( $menu->getMenu() as $current )
        <md-list-item ng-click="helper.redirect( '{{ '/' . $current[ 'uri' ] }}' )">
            <p><strong>{{ $current[ 'name' ] }}</strong></p>
        </md-list-item>

        @if(isset($current['children']))
            @foreach ( $current['children'] as $currentChild )
            <md-list-item ng-click="helper.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )">
                <p class="childMenu">{{ $currentChild[ 'name' ] }}</p>
            </md-list-item>
            @endforeach
        @endif
    @endforeach
        @if(Sentinel::check())
            <md-list-item ng-click="helper.redirect( '{{'/' . route('logout')}}' )">
                <p>Log Out</p>
            </md-list-item>
        @else
            <md-list-item ng-click="helper.redirect( '{{'/' . route('login')}}' )">
            </md-list-item>
        @endif
    </md-list>
</nav>
