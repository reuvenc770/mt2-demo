<md-sidenav md-component-id="mainNav" class="md-sidenav-left mt2-sidenav-dark md-hue-3" md-is-locked-open="app.lockSidenav && app.largePageWidth()" ng-cloak ng-init="app.setSidenavCookie()">
    <md-list layout-align="center start">
    @foreach ( $menuItems as $section )
        <md-list-item class="mt2-nav-main-item" ng-click="app.openSideNavMenu( '{{ $section[ 'name' ] }}' )" layout-align="start center" aria-label="{{ $section[ 'name' ] }}">
            @if ( $section[ 'icon' ] != '' )
            <md-icon class="mt2-nav-icon" md-font-set="material-icons" style="color: #FFF;">{{$section[ 'icon' ]}}</md-icon>
            @endif
            <span class="mt2-nav-main-item">{{ $section[ 'name' ] }}</span>
            <span flex></span>
        </md-list-item>

        <div ng-class="app.sidenavSectionClasses[ '{{$section[ 'name' ]}}' ]" ng-init="app.initSideNavMenu( '{{$section[ 'name' ]}}' )">
            @foreach ( $section[ 'children' ] as $currentChild )
            <a href="{{ '/' . $currentChild[ 'uri' ] }}" target="_self">
                <md-list-item class="mt2-nav-sub-item" aria-label="{{ $currentChild[ 'name' ] }}">
                    <span flex="20"></span>
                    <span class="childMenu"><em>{{ $currentChild[ 'name' ] }}</em></span>
                    <span flex></span>
                </md-list-item>
            </a>
            @endforeach
        </div>
    @endforeach
    </md-list>
</md-sidenav>
