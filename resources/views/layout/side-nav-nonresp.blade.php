<div flex="none">
    <md-list layout-align="center start" class="mt2-sidenav-dark md-hue-3">
    @foreach ( $menuItems as $current )
        <md-list-item class="mt2-nav-main-item" ng-click="app.redirect( '{{ '/' . $current[ 'uri' ] }}' )" layout-align="start center" aria-label="{{ $current[ 'name' ] }}">
            <md-icon class="mt2-nav-icon" md-svg-icon="{{ $current[ 'icon' ] }}"></md-icon>
            <span class="mt2-nav-main-item">{{ $current[ 'name' ] }}</span>
            <span flex></span>
        </md-list-item>

        @if(isset($current['children']))
            @foreach ( $current['children'] as $currentChild )
            <md-list-item class="mt2-nav-sub-item" ng-click="app.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )" aria-label="{{ $current[ 'name' ] }}">
                <span flex="20"></span>
                <span class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></span>
                <span flex></span>
            </md-list-item>
            @endforeach
        @endif
    @endforeach

        <md-list-item ng-click="app.redirect( '{{ route('logout')}}' )" aria-label="Log Out">
            <h4 class="mt2-nav-main-item">Log Out</h4>
            <span flex></span>
        </md-list-item>
    </md-list>
</div>
