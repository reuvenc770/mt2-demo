@extends( 'layout.default' )

@section( 'title' , 'Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Clients</h1></div>
</div>

<div ng-controller="ClientController as client" ng-init="client.loadClients()">
    @if (Sentinel::hasAccess('client.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="client.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Client</button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="client.paginationCount" currentpage="client.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="client.currentPage" maxpage="client.pageCount" disableceiling="client.reachedMaxPage" disablefloor="client.reachedFirstPage"></pagination>
                </div>
            </div>

            <client-table records="client.clients" loadingflag="client.currentlyLoading"></client-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="client.paginationCount" currentpage="client.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="client.currentPage" maxpage="client.pageCount" disableceiling="client.reachedMaxPage" disablefloor="client.reachedFirstPage"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
