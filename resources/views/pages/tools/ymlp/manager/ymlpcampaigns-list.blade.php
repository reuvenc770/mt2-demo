
@extends( 'layout.default' )

@section( 'title' , 'YMLP Campaigns' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">YMLP Campaigns</h1></div>
    </div>

    <div ng-controller="ymlpCampaignController as ymlp" ng-init="ymlp.loadCampaigns()">
        @if (Sentinel::hasAccess('ymlpcampaign.add'))
            <div class="row">
                <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="ymlp.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add YMLP Campaign</button>
            </div>
        @endif

        <div class="row">
            <div class="col-xs-12">

                <md-content class="md-mt2-zeta-theme">
                @include( 'pages.tools.ymlp.manager.ymlpcampaigns-table' )
                </md-content>

            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
