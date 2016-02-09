@extends( 'layout.default' )

@section( 'title' , 'MT2 User List' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">User Accounts</h1></div>
    </div>

    <div ng-controller="userController as user" ng-init="user.loadAccounts()">
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="user.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add User Account</button>
        </div>

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
