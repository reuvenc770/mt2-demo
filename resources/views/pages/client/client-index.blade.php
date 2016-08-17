@extends( 'layout.default' )

@section( 'title' , 'Client' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller', 'ng-controller="ClientController as client"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('client.add'))
        <md-button ng-click="client.viewAdd()" aria-label="Add Client">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Client</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="client.loadClients()">
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
