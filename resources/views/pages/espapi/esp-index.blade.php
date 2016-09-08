
@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('espapi.add'))
        <md-button ng-click="esp.viewAdd()" aria-label="Add ESP Account">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add ESP Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="esp.loadAccounts()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            @include( 'pages.espapi.esp-table' )
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/espapi.js"></script>
@stop
