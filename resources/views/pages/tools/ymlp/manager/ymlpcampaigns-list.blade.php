
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
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="ymlp.paginationCount" currentpage="ymlp.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="ymlp.currentPage" maxpage="ymlp.pageCount"></pagination>
                    </div>
                </div>

                <ymlpcampaign-table records="ymlp.campaigns"></ymlpcampaign-table>

                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="ymlp.paginationCount" currentpage="ymlp.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="ymlp.currentPage" maxpage="ymlp.pageCount"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
