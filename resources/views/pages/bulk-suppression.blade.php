@extends( 'layout.default' )

@section( 'title' , 'Bulk Suppression' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Bulk Suppression</h1></div>
</div>

<div ng-controller="BulkSuppressionController as supp">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Save</button>

            <md-input-container>
                <label>Suppression File</label>
                <input type="file" ng-model="supp.file" required>
            </md-input-container>

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Save</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<!-- <script src="js/show-info.js"></script> -->
@stop
