
@extends( 'layout.default' )

@section( 'title' , 'Edit Client' )

@section( 'navClientClasses' , 'active' )


@section( 'content' )

<div ng-controller="ClientController as client" ng-init="client.loadClient()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-sm="50" flex="100">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disable' : client.updatingClient }" ng-click="client.updateClient( $event )"><span class="glyphicon glyphicon-repeat" ng-class="{ 'rotateMe' : client.updatingClient }"></span> Update</button>
            <button type="button" class="btn btn-info btn-md pull-right" ng-class="{ 'disable' : client.generatingLinks }" ng-click="client.generateLinks()"><span class="glyphicon glyphicon-link" ng-class="{ 'rotateMe' : client.generatingLinks }"></span> Generate Links</button>
            <button type="button" class="btn btn-danger btn-md pull-right"  ng-click="client.resetPassword()"><span class="glyphicon glyphicon-cog" ng-class="{ 'rotateMe' : client.generatingLinks }"></span> Reset FTP Password</button>

            <div class="clearfix"></div>

            @include( 'pages.client.client-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disable' : client.updatingClient }" ng-click="client.updateClient( $event )"><span class="glyphicon glyphicon-repeat" ng-class="{ 'rotateMe' : client.updatingClient }"></span> Update</button>
            <button type="button" class="btn btn-info btn-md pull-right" ng-class="{ 'disable' : client.generatingLinks }" ng-click="client.generateLinks()"><span class="glyphicon glyphicon-link" ng-class="{ 'rotateMe' : client.generatingLinks }"></span> Generate Links</button>
        </div>
    </md-content>

    <client-url-modal records="client.urlList"></client-url-modal>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
