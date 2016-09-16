@extends( 'layout.default' )

@section( 'title' , 'Data Cleanse' )

@section( 'angular-controller' , 'ng-controller="DataCleanseController as cleanse"')

@section( 'page-menu' )
    @if ( Sentinel::hasAccess( 'datacleanse.add' ) )
        <md-button ng-click="cleanse.viewAdd()" aria-label="Add Data Cleanse">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Data Cleanse</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="cleanse.load()">
    <md-content layout="row" layout-align="center" class="md-mt2-zeta-theme md-hue-1">
        <md-card flex-gt-md="70" flex="100">
            @include( 'pages.datacleanse.datacleanse-table' )
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
