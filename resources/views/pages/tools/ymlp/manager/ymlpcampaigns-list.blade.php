
@extends( 'layout.default' )

@section( 'title' , 'YMLP Campaigns' )

@section( 'angular-controller' , 'ng-controller="ymlpCampaignController as ymlp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('ymlpcampaign.add'))
        <md-button ng-click="ymlp.viewAdd()" aria-label="Add YMLP Campaign">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add YMLP Campaign</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="ymlp.loadCampaigns()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                @include( 'pages.tools.ymlp.manager.ymlpcampaigns-table' )
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
