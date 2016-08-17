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
