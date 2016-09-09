@extends( 'layout.default' )

@section( 'title' , 'Update Client Group' )

@section( 'content' )
<div ng-controller="ClientGroupController as clientGroup" ng-init="clientGroup.loadClientList()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="50" flex="100">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : clientGroup.updatingClientGroup }" ng-click="clientGroup.updateClientGroup( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : clientGroup.updatingClientGroup }"></span> Save</button>

            <div class="clearfix"></div>

            @include( 'pages.clientgroup.clientgroup-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : clientGroup.updatingClientGroup }" ng-click="clientGroup.updateClientGroup( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : clientGroup.updatingClientGroup }"></span> Save</button>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
