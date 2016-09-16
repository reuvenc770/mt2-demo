@extends( 'layout.default' )

@section( 'title' , 'Client Group' )

@section( 'angular-controller' , 'ng-controller="ClientGroupController as clientGroup"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('clientgroup.add'))
        <md-button ng-click="clientGroup.viewAdd()" aria-label="Add Client Group">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Client Group</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="clientGroup.loadClientGroups()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <md-card flex-gt-md="70" flex="100">
            @include( 'pages.clientgroup.clientgroup-table' )
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
