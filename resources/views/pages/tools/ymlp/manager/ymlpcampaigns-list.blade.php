
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
                <md-card-content>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="ymlp.paginationCount" currentpage="ymlp.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="ymlp.currentPage" maxpage="ymlp.pageCount"></pagination>
                        </md-input-container>
                    </div>

                    <ymlpcampaign-table records="ymlp.campaigns"></ymlpcampaign-table>

                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="auto">
                            <pagination-count recordcount="ymlp.paginationCount" currentpage="ymlp.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="ymlp.currentPage" maxpage="ymlp.pageCount"></pagination>
                        </md-input-container>
                    </div>
                </md-card-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/ymlpcampaign.js"></script>
@stop
