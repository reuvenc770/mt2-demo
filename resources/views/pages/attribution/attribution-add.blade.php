@extends( 'layout.default' )

@section( 'title' , 'Add Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.attribution.model.store'))
    <div ng-hide="app.isMobile()">
        <md-button ng-click="attr.saveModel( $event , attrModelForm )" aria-label="Add Attribution Model">
            <span>Save Model</span>
        </md-button>
    </div>

    <md-menu ng-show="app.isMobile()" md-position-mode="target-right target">
        <md-button aria-label="Open Menu" class="md-icon-button" ng-click="$mdOpenMenu($event)">
            <md-icon md-svg-src="img/icons/ic_more_horiz_black_24px.svg"></md-icon>
        </md-button>

        <md-menu-content width="3">
            <md-menu-item>
                <md-button ng-click="attr.saveModel( $event , attrModelForm )">
                    <span>Save Model</span>
                </md-button>
            </md-menu-item>
        </md-menu-content>
    </md-menu>
    @endif
@stop

@section( 'content' )
<div ng-init="attr.loadClients()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1" layout-padding>
        <div flex-gt-md="80" flex="100">
            @include( 'pages.attribution.attribution-form' )
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/attribution.js"></script>
@stop
