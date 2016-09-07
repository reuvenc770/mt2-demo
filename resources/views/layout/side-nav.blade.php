<md-sidenav md-component-id="mainNav" class="md-sidenav-left md-accent">
    <md-list layout-align="center start">
    @foreach ( $menuItems as $current )
        <md-list-item ng-click="app.redirect( '{{ '/' . $current[ 'uri' ] }}' )" layout-align="start center" aria-label="{{ $current[ 'name' ] }}">
            <h4 class="mt2-nav-main-item">{{ $current[ 'name' ] }}</h4>
            <span flex></span>
            <md-icon md-svg-icon="{{ $current[ 'icon' ] }}"></md-icon>
        </md-list-item>

        @if(isset($current['children']))
            @foreach ( $current['children'] as $currentChild )
            <md-list-item ng-click="app.redirect( '{{ '/' . $currentChild[ 'uri' ] }}' )" aria-label="{{ $current[ 'name' ] }}">
                <h5 class="childMenu mt2-nav-sub-item"><em>{{ $currentChild[ 'name' ] }}</em></h5>
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
</md-sidenav>
