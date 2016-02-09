@extends( 'layout.default' )

@section( 'title' , 'Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Clients</h1></div>
</div>

<div ng-controller="ClientController as client" ng-init="client.loadClients()">
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="client.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Client</button>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div id="mtTableContainer" class="table-responsive">
                <generic-table headers="client.headers" records="client.clients" editurl="client.editUrl"></generic-table>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
