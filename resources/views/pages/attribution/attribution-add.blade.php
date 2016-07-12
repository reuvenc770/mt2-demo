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
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            @include( 'pages.attribution.attribution-form' )
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
@stop
