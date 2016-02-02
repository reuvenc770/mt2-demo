
@extends( 'layout.default' )

@section( 'title' , 'Add ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1>Add ESP</h1></div>
</div>

<div ng-controller="espController as esp">
    ESP Name: <select ng-model="esp.currentAccount.espId">
                <option value="">Choose ESP</option>
                @foreach( $espList as $espId => $esp )
                <option value="{{ $espId }}">{{ $esp }}</option>
                @endforeach
            </select><br /><br />
    Account Name: <input type="text" ng-model="esp.currentAccount.accountName" value="" /> <br /><br />
    Key 1: <input type="text" ng-model="esp.currentAccount.key1" value="" /> <br /><br />
    Key 2: <input type="text" ng-model="esp.currentAccount.key2" value="" /> <br /><br />
    <button type="button" class="btn btn-info btn-lg" ng-click="esp.saveNewAccount()">Save</button>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/esp.js"></script>
@stop
