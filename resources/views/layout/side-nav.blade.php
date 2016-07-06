@inject( 'menu' , 'App\Services\NavigationService' )

<md-sidenav md-component-id="mainNav" class="md-sidenav-left md-accent">
    <md-list layout-align="center start">
    @foreach ( $menu->getMenu() as $current )
        <md-list-item ng-click="app.redirect( '{{ '/' . $current[ 'uri' ] }}' )" layout-align="start center" aria-label="{{ $current[ 'name' ] }}">
            <h4>{{ $current[ 'name' ] }}</h4>
            <span flex></span>
            <md-icon md-svg-icon="{{ $menu->getMenuIcon( $current[ 'uri' ] ) }}"></md-icon>
        </md-list-item>

        @if(isset($current['children']))
            @foreach ( $current['children'] as $currentChild )
            <md-list-item ng-click="app.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )" aria-label="{{ $current[ 'name' ] }}">
                <h5 class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></h5>
                <span flex></span>
            </md-list-item>
            @endforeach
        @endif
    @endforeach

    @if(Sentinel::check())
        <md-list-item ng-click="app.redirect( '{{'/' . route('logout')}}' )" aria-label="Log Out">
            <h4>Log Out</h4>
            <span flex></span>
        </md-list-item>
    @else
        <md-list-item ng-click="app.redirect( '{{'/' . route('login')}}' )" aria-label="Log Out">
            <span flex></span>
            <h4>Log In</h4>
        </md-list-item>
    @endif
    </md-list>
</md-sidenav>
