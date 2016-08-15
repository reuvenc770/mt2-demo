@extends( 'layout.default' )

@section( 'title' , 'MT2 Proxy List' )


@section( 'content' )

    <div ng-controller="ProxyController as proxy" ng-init="proxy.loadAccounts()">
        @if (Sentinel::hasAccess('proxy.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="proxy.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Proxy</button>
        </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <generic-table headers="proxy.headers" records="proxy.accounts" editurl="proxy.editUrl"></generic-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/proxy.js"></script>
@stop
