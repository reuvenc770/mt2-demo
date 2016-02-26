@extends( 'layout.default' )

@section( 'title' , 'Add Client Group' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Add Client Group</h1></div>
</div>

<div ng-controller="ClientGroupController as clientGroup" ng-init="clientGroup.loadClientList()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : clientGroup.creatingClientGroup }" ng-click="clientGroup.saveClientGroup( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : clientGroup.creatingClientGroup }"></span> Save</button>

            <div class="clearfix"></div>

            @include( 'pages.clientgroup.clientgroup-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : clientGroup.creatingClientGroup }" ng-click="clientGroup.saveClientGroup( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : clientGroup.creatingClientGroup }"></span> Save</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
