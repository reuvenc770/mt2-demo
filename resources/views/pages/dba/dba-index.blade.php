@extends( 'layout.default' )

@section( 'title' , 'MT2 DBA List' )

@section ( 'angular-controller' , 'ng-controller="DBAController as dba"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('dba.add'))
        <md-button ng-click="dba.viewAdd()" aria-label="Add DBA Account">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add DBA Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="dba.loadAccounts()">
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="dba.paginationCount"
                                          currentpage="proxy.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="dba.currentPage" maxpage="dba.pageCount"></pagination>
                    </div>
                </div>
                <div id="mtTableContainer" class="table-responsive">
                    <dba-table format-box="dba.formatBox(box)" toggle="dba.toggle(recordId, direction)" records="dba.accounts" ></dba-table>
                </div>
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="dba.paginationCount"
                                          currentpage="dba.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="dba.currentPage" maxpage="dba.pageCount"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop
