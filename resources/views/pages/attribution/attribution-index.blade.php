@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('attributionModel.add'))
        <md-button ng-href="{{ route( 'attributionModel.add' ) }}" target=
"_self" aria-label="Add Attribution Model">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Model</span>
        </md-button>
    @endif

    @if (Sentinel::hasAccess('api.attribution.run'))
        <md-button ng-click="attr.runAttribution( false )" aria-label="Run Attribution">
            <span>Run Attribution</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<md-content layout="column" class="md-mt2-zeta-theme" ng-init="attr.initIndexPage()">
    <md-tabs md-dynamic-height md-border-bottom>
        <md-tab label="Models">
            @include( 'pages.attribution.indexPartials.models-index' )
        </md-tab>

        <md-tab label="Report">
            <md-card class="md-mt2-zeta-theme" flex> 
            @include( 'pages.attribution.indexPartials.three-month-report' )
            </md-card>
        </md-tab>
    <md-tabs>

    @include( 'pages.attribution.attribution-level-copy-sidenav' )
</md-content>
@stop

@section( 'pageIncludes' )
<script src="js/attribution.js"></script>
@stop
