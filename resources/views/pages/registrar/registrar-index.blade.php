@extends( 'layout.default' )

@section( 'title' , 'MT2 Registrar List' )

@section ( 'angular-controller' , 'ng-controller="RegistrarController as registrar"' )

@section ( 'page-menu' )
    @if (Sentinel::hasAccess('registrar.add'))
        <md-button ng-click="registrar.viewAdd()" aria-label="Add Registrar">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Registrar</span>
        </md-button>
    @endif
@stop


@section( 'content' )
    <div ng-init="registrar.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                @include( 'pages.registrar.registrar-table' )
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop
