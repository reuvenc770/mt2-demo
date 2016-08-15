@extends( 'layout.default' )

@section( 'title' , 'Add Data Cleanse' )

@section( 'content' )

<div ng-controller="DataCleanseController as cleanse">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <form name="cleanseForm" novalidate>
                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : cleanse.creatingCleanse }" ng-click="cleanse.saveCleanse( $event , cleanseForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : cleanse.creatingCleanse }"></span> Save</button>

                <div class="clearfix"></div>

                @include( 'pages.datacleanse.datacleanse-form' )

                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : cleanse.creatingCleanse }" ng-click="cleanse.saveCleanse( $event , cleanseForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : cleanse.creatingCleanse }"></span> Save</button>
            </form>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
