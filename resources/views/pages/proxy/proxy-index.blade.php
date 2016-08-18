@extends( 'layout.default' )

@section( 'title' , 'MT2 Proxy List' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Proxies</h1></div>
    </div>

    <div ng-controller="ProxyController as proxy" ng-init="proxy.loadAccounts()">
        @if (Sentinel::hasAccess('proxy.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="proxy.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Proxy</button>
        </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                                <pagination-count recordcount="proxy.paginationCount" currentpage="proxy.currentPage"></pagination-count>
                            </div>

                            <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                                <pagination currentpage="proxy.currentPage" maxpage="proxy.pageCount"></pagination>
                            </div>
                        </div>
                <div id="mtTableContainer" class="table-responsive">
                    <proxy-table  toggle="proxy.toggle(recordId, direction)" records="proxy.accounts"></proxy-table>
                </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                                        <pagination-count recordcount="proxy.paginationCount" currentpage="proxy.currentPage"></pagination-count>
                                    </div>

                                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                                        <pagination currentpage="proxy.currentPage" maxpage="proxy.pageCount"></pagination>
                                    </div>
                                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop
