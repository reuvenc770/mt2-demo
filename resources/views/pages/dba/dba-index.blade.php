@extends( 'layout.default' )

@section( 'title' , 'DBA List' )

@section ( 'angular-controller' , 'ng-controller="DBAController as dba"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('dba.add'))
        <md-button ng-click="dba.viewAdd()" aria-label="Add DBA Account">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add DBA Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="dba.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                @include( 'pages.dba.dba-table' )
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop