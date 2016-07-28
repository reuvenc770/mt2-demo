@extends( 'layout.default' )

@section( 'title' , 'MT2 DBA List' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">DBA Accounts</h1></div>
    </div>

    <div ng-controller="DBAController as dba" ng-init="dba.loadAccounts()">
        @if (Sentinel::hasAccess('dba.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="dba.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add DBA Account</button>
        </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <dba-table headers="dba.headers" records="dba.accounts" editurl="dba.editUrl"></dba-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
