@extends( 'layout.default' )

@section( 'title' , 'Add Data Cleanse' )

@section( 'content' )
<div ng-controller="DataCleanseController as cleanse">
    <form name="cleanseForm" novalidate>
        <button type="button" class="btn btn-primary btn-md pull-right padding" ng-class="{ 'disabled' : cleanse.creatingCleanse }" ng-click="cleanse.saveCleanse( $event , cleanseForm )" layout="row">
            <md-icon md-font-set="material-icons" ng-hide="cleanse.creatingCleanse">save</md-icon>
            <span class="glyphicon glyphicon-repeat" ng-show="cleanse.creatingCleanse" ng-class="{ 'rotateMe' : cleanse.creatingCleanse }"></span>
            Save
        </button>

        <div class="clearfix"></div>

        @include( 'bootstrap.pages.datacleanse.datacleanse-form' )

        <button type="button" class="btn btn-primary btn-md pull-right" ng-class="{ 'disabled' : cleanse.creatingCleanse }" ng-click="cleanse.saveCleanse( $event , cleanseForm )">
            <md-icon md-font-set="material-icons" ng-hide="cleanse.creatingCleanse">save</md-icon>
            <span class="glyphicon glyphicon-repeat" ng-show="cleanse.creatingCleanse" ng-class="{ 'rotateMe' : cleanse.creatingCleanse }"></span>
            Save
        </button>
    </form>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/datacleanse/DataCleanseController.js',
                'resources/assets/js/bootstrap/datacleanse/DataCleanseApiService.js'],'js','pageLevel') ?>