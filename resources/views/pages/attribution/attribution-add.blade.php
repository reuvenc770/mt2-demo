@extends( 'layout.default' )

@section( 'title' , 'Add Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.attribution.model.store'))
        <md-button ng-click="attr.saveModel( $event , attrModelForm )" aria-label="Add Attribution Model">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Save Model</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="attr.loadClients()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            @include( 'pages.attribution.attribution-form' )
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
@stop
