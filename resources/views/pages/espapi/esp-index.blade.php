
@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">ESP Accounts</h1></div>
</div>

<div ng-controller="espController as esp" ng-init="esp.loadAccounts()">
    @if (Sentinel::hasAccess('espapi.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="esp.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add ESP Account</button>
    </div>
    @endif

    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="esp.paginationCount" currentpage="esp.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="esp.currentPage" maxpage="esp.pageCount"></pagination>
                </div>
            </div>

            <espapi-table records="esp.accounts"></espapi-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="esp.paginationCount" currentpage="esp.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="esp.currentPage" maxpage="esp.pageCount"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/espapi.js"></script>
@stop
