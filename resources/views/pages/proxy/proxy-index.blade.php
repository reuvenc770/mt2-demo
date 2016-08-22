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
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                                <pagination-count recordcount="proxy.paginationCount"
                                                  currentpage="proxy.currentPage"></pagination-count>
                            </div>

                            <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                                <pagination currentpage="proxy.currentPage" maxpage="proxy.pageCount"></pagination>
                            </div>
                        </div>
                        <div id="mtTableContainer" class="table-responsive">
                            <proxy-table toggle="proxy.toggle(recordId, direction)"
                                         records="proxy.accounts"></proxy-table>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                                        <pagination-count recordcount="proxy.paginationCount"
                                                          currentpage="proxy.currentPage"></pagination-count>
                                    </div>

                                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                                        <pagination currentpage="proxy.currentPage"
                                                    maxpage="proxy.pageCount"></pagination>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    @stop

                    @section( 'pageIncludes' )
                        <script src="js/proxy.js"></script>
@stop
