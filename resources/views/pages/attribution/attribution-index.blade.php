@extends( 'layout.default' )

@section( 'title' , 'Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    <div ng-hide="app.isMobile()">
        @if (Sentinel::hasAccess('attributionModel.add'))
        <md-button ng-href="{{ route( 'attributionModel.add' ) }}" target="_self" aria-label="Add Attribution Model">
            <span>Add Model</span>
        </md-button>
        @endif

        @if (Sentinel::hasAccess('api.attribution.run'))
        <md-button ng-click="attr.runAttribution( false )" aria-label="Run Live Attribution">
            <span>Run Live Attribution</span>
        </md-button>
        @endif
    </div>

    <md-menu ng-show="app.isMobile()" md-position-mode="target-right target">
        <md-button aria-label="Open Menu" class="md-icon-button" ng-click="$mdOpenMenu( $event )">
            <md-icon md-svg-src="img/icons/ic_more_horiz_black_24px.svg"></md-icon>
        </md-button>
    
        <md-menu-content width="3">
            @if (Sentinel::hasAccess('attributionModel.add'))
            <md-menu-item>
                <md-button ng-href="{{ route( 'attributionModel.add' ) }}" target="_self" aria-label="Add Attribution Model">
                    <span>Add Model</span>
                </md-button>
            </md-menu-item>
            @endif

            @if (Sentinel::hasAccess('api.attribution.run'))
            <md-menu-item>
                <md-button ng-click="attr.runAttribution( false )" aria-label="Run Live Attribution">
                    <span>Run Live Attribution</span>
                </md-button>
            </md-menu-item>
            @endif
        </md-menu-content>
    </md-menu>
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
