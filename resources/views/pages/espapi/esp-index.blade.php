@extends( 'layout.default' )

@section( 'title' , 'ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
<div class="row">
    <ol class="breadcrumb">
      <li><a href="#">Home</a></li>
      <li><a href="#">ESP</a></li>
      <li class="active">Accounts</li>
    </ol>
    <div class="page-header col-xs-12"><h1 class="text-center">ESP Accounts</h1></div>
</div>

<div ng-controller="espController as esp" ng-init="esp.loadAccounts()">
    @if (Sentinel::hasAccess('espapi.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="esp.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add ESP Account</button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12">
            <div id="mtTableContainer" class="table-responsive">
                <generic-table headers="esp.headers" records="esp.accounts" editurl="esp.editUrl"></generic-table>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/espapi.js"></script>
@stop
