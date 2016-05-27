<!doctype html>
<html>
    <head>
        @include( 'layout.html-head' )
    </head>
    <body ng-app="mt2App">
        <div ng-controller="AppController as app" md-theme="mt2-zeta" layout="column" layout-fill ng-cloak>
            <section layout="row" flex>
                @include( 'layout.sidenav-left' )

                <md-content flex>
                    <md-toolbar>
                        <div class="md-toolbar-tools">
                            <span flex></span>

                            <md-menu>
                                <md-button ng-click="app.openDropdownMenu( $mdOpenMenu , $event )">
                                    <md-icon md-svg-icon="/img/icons/ic_account_circle_white_36px.svg"></md-icon>
                                </md-button>

                                <md-menu-content width="4">
                                    @if(Sentinel::check())
                                    <md-menu-item flex layout-align="center center">
                                        {{Sentinel::getUser()->first_name}}
                                    </md-menu-item>

                                    <md-menu-item>
                                        <md-button ng-click="app.redirect( '{{ '/' . route("myprofile") }}' )">My Profile</md-button>
                                    </md-menu-item>

                                    <md-menu-item>
                                        <md-button ng-click="app.redirect( '{{ '/' . route("logout")}}' )">Logout</md-button>
                                    </md-menu-item>
                                    @else
                                    <md-menu-item>
                                        <md-button ng-click="app.redirect( '{{ '/' . route("login")}}' )">Logout</md-button>
                                    </md-menu-item>
                                    @endif
                                </md-menu-content>
                            </md-menu>
                        </div>
                    </md-toolbar>

                    <div id="flashContainer">
                        @include('flash::message')
                    </div>

                    {!! Breadcrumbs::renderIfExists() !!}

                    @yield( 'content' )
                </md-content>
            </section>
        </div>

        @include( 'layout.modal' )
        @include( 'layout.body-footer' )
        @yield( 'pageIncludes' )
    </body>
</html>
