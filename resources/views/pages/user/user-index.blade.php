@extends( 'layout.default' )

@section( 'title' , 'MT2 User List' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller', 'ng-controller="userController as user"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('user.add'))
        <md-button ng-click="user.viewAdd()" aria-label="Add User Account">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add User Account</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="user.loadAccounts()">
        <div class="row">
            <div class="col-xs-12">
                <div id="mtTableContainer" class="table-responsive">
                    <generic-table headers="user.headers" records="user.accounts" editurl="user.editUrl"></generic-table>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/user.js"></script>
@stop
