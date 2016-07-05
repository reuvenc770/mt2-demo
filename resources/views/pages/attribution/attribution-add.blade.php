@extends( 'layout.default' )

@section( 'title' , 'Add Attribution Model' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Add Attribution Model</h1></div>
</div>

<div ng-controller="AttributionController as attr" ng-init="attr.loadClients()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : attr.creatingModel }" ng-click="attr.saveModel( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : attr.creatingModel }"></span> Save</button>

            <div class="clearfix"></div>

            @include( 'pages.attribution.attribution-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : attr.creatingModel }" ng-click="attr.saveModel( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : attr.creatingModel }"></span> Save</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/recordAttribution.js"></script>
<script src="js/angular-drag-and-drop-lists.min.js"></script>
@stop
