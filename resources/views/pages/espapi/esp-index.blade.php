
@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('espapi.add'))
        <md-button ng-click="esp.viewAdd()" aria-label="Add ESP Account">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add ESP Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="esp.loadAccounts()">
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
