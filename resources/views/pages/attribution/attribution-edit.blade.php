@extends( 'layout.default' )

@section( 'title' , 'Edit Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="AttributionController as attr"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.attribution.model.update'))
        <md-button ng-click="attr.updateModel( $event , attrModelForm )" aria-label="Add Attribution Model">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Update Model</span>
        </md-button>
    @endif

    @if (Sentinel::hasAccess('api.attribution.model.copyLevels'))
        <md-button ng-click="attr.copyModelPreview( $event )" aria-label="Add Attribution Model">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Copy Model</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="attr.prepopModel()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            @include( 'pages.attribution.attribution-form' )
        </div>
    </div>

    @include( 'pages.attribution.attribution-level-copy-sidenav' )
</div>
@stop


@section( 'pageIncludes' )
<script src="js/attribution.js"></script>
@stop
