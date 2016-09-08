@extends( 'layout.default' )

@section( 'title' , 'Update Data Export' )

@section( 'content' )

<div ng-controller="DataExportController as dataExport">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : dataExport.updatingDataExport }" ng-click="dataExport.saveDataExport( $event )">
                <span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : dataExport.updatingDataExport }"></span>
                Save
            </button>

            <div class="clearfix"></div>

            @include( 'pages.dataexport.dataexport-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : dataExport.updatingDataExport }" ng-click="dataExport.saveDataExport( $event )">
                <span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : dataExport.updatingDataExport }"></span>
                Save
            </button>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/dataexport.js"></script>
@stop