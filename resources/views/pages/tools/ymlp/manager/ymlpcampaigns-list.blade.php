
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
