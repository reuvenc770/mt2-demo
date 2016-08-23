@extends( 'layout.default' )

@section( 'title' , 'MT2 Proxy List' )

@section ( 'angular-controller' , 'ng-controller="ProxyController as proxy"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('proxy.add'))
        <md-button ng-click="proxy.viewAdd()" aria-label="Add Proxy">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Proxy</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="proxy.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-card-content>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="proxy.paginationCount"
                                              currentpage="proxy.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="proxy.currentPage" maxpage="proxy.pageCount"></pagination>
                        </md-input-container>
                    </div>
                    <div id="mtTableContainer" class="table-responsive">
                        <proxy-table toggle="proxy.toggle(recordId, direction)"
                                     records="proxy.accounts"></proxy-table>
                    </div>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="proxy.paginationCount"
                                              currentpage="proxy.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="proxy.currentPage"
                                        maxpage="proxy.pageCount"></pagination>
                        </md-input-container>
                    </div>
                </md-card-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop
