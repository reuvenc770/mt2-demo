@extends( 'layout.default' )

@section( 'title' , 'Add Data Cleanse' )

@section( 'content' )

<div ng-controller="DataCleanseController as cleanse">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <div layout="column" layout-align="end end">
               <md-button class="md-raised md-accent" ng-disabled="cleanse.creatingCleanse" ng-click="cleanse.saveCleanse( $event , cleanseForm )" layout="row">
                  <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-hide="cleanse.creatingCleanse">save</md-icon>
                  <md-progress-circular ng-show="cleanse.creatingCleanse" md-mode="indeterminate" md-diameter="16"></md-progress-circular><span flex>Save</span>
               </md-button>
            </div>

            @include( 'pages.datacleanse.datacleanse-form' )

            <div layout="column" layout-align="end end">
               <md-button class="md-raised md-accent" ng-disabled="cleanse.creatingCleanse" ng-click="cleanse.saveCleanse( $event , cleanseForm )" layout="row">
                  <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-hide="cleanse.creatingCleanse">save</md-icon>
                  <md-progress-circular ng-show="cleanse.creatingCleanse" md-mode="indeterminate" md-diameter="16"></md-progress-circular><span flex>Save</span>
               </md-button>
            </div>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
