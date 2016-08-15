@extends( 'layout.default' )

@section( 'title' , 'Add Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )

<div ng-controller="ClientController as client">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : client.creatingClient }" ng-click="client.saveClient( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : client.creatingClient }"></span> Save</button>

            <div class="clearfix"></div>

            @include( 'pages.client.client-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : client.creatingClient }" ng-click="client.saveClient( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : client.creatingClient }"></span> Save</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
