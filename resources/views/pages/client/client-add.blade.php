@extends( 'layout.default' )

@section( 'title' , 'Add Client' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )

<div ng-controller="ClientController as client">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

		<div flex-gt-sm="50" flex="100">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : client.creatingClient }" ng-click="client.saveClient( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : client.creatingClient }"></span> Save</button>

            <div class="clearfix"></div>

            @include( 'pages.client.client-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : client.creatingClient }" ng-click="client.saveClient( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : client.creatingClient }"></span> Save</button>
   	</div>
   </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
