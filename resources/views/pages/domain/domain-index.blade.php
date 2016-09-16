
@extends( 'layout.default' )

@section( 'title' , 'Domain List' )

@section( 'angular-controller' , 'ng-controller="domainController as domain"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <md-button ng-click="domain.viewAdd()" aria-label="Add Domain">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Domain</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="domain.loadAccounts()">
        <md-content layout="row" layout-align="center" class="md-mt2-zeta-theme md-hue-1">
            <md-card flex-gt-md="70" flex="100">
                @include( 'pages.domain.domain-table' )
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
