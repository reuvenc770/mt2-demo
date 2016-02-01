
@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1>Add ESP</h1></div>
</div>

<div ng-controller="espController as esp">
    ESP Name: <input type="text" ng-model="esp.newAccount.espName" value="" /> <br /><br />
    Account Name: <input type="text" ng-model="esp.newAccount.accountName" value="" /> <br /><br />
    Key 1: <input type="text" ng-model="esp.newAccount.key1" value="" /> <br /><br />
    Key 2: <input type="text" ng-model="esp.newAccount.key2" value="" /> <br /><br />
    <button type="button" class="btn btn-info btn-lg" ng-click="esp.saveNewAccount()">Save</button>
</div>
@stop
