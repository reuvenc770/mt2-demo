@extends( 'layout.default' )

@section( 'title' , 'Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Clients</h1></div>
</div>

<div ng-controller="ClientController as client" ng-init="client.loadClients()">
    @if (Sentinel::hasAccess('client.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="client.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Client</button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12">
            <client-table records="client.clients"></client-table>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
