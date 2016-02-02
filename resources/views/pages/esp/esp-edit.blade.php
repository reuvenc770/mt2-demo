@extends( 'layout.default' )

@section( 'title' , 'Edit ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1>Edit ESP</h1></div>
</div>

<div ng-controller="espController as esp" ng-init="esp.loadAccount()">
    <input type="hidden" ng-model="esp.currentAccount.id" />
    ESP Name: <strong>{{ $espName }}</strong> <br /><br />
    Account Name: <input type="text" ng-model="esp.currentAccount.accountName" /> <br /><br />
    Key 1: <input type="text" ng-model="esp.currentAccount.key1" /> <br /><br />
    Key 2: <input type="text" ng-model="esp.currentAccount.key2" /> <br /><br />
    <button type="button" class="btn btn-info btn-lg" ng-click="esp.editAccount()">Save</button>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/esp.js"></script>
@stop
