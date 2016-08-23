@extends( 'layout.default' )

@section( 'title' , 'Add Data Cleanse' )

@section( 'content' )

<div ng-controller="DataCleanseController as cleanse">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <form name="cleanseForm" novalidate>
                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : cleanse.creatingCleanse }" ng-click="cleanse.saveCleanse( $event , cleanseForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : cleanse.creatingCleanse }"></span> Save</button>

                <div class="clearfix"></div>

                @include( 'pages.datacleanse.datacleanse-form' )

                <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : cleanse.creatingCleanse }" ng-click="cleanse.saveCleanse( $event , cleanseForm )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : cleanse.creatingCleanse }"></span> Save</button>
            </form>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
