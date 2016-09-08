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
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-card-content>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="dba.paginationCount"
                                              currentpage="proxy.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="dba.currentPage" maxpage="dba.pageCount"></pagination>
                        </md-input-container>
                    </div>
                    <div id="mtTableContainer" class="table-responsive">
                        <dba-table format-box="dba.formatBox(box)" toggle="dba.toggle(recordId, direction)" records="dba.accounts" ></dba-table>
                    </div>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="dba.paginationCount"
                                              currentpage="dba.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="dba.currentPage" maxpage="dba.pageCount"></pagination>
                        </md-input-container>
                    </div>
                </md-card-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/dba.js"></script>
@stop