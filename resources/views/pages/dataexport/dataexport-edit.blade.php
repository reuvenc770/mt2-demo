@extends( 'layout.default' )

@section( 'title' , 'Update Data Export' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Update Data Export</h1></div>
</div>

<div ng-controller="DataExportController as dataExport" ng-init="dataExport.setupPage()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : dataExport.updatingDataExport }" ng-click="dataExport.updateDataExport( $event )">
                <span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : dataExport.updatingDataExport }"></span>
                Save
            </button>

            <div class="clearfix"></div>

            @include( 'pages.dataexport.dataexport-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : dataExport.updatingDataExport }" ng-click="dataExport.updateDataExport( $event )">
                <span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : dataExport.updatingDataExport }"></span>
                Save
            </button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/dataexport.js"></script>
@stop