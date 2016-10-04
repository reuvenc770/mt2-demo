@extends( 'layout.default' )

@section( 'title' , 'Update Feed Group' )

@section( 'content' )
<div ng-controller="ClientGroupController as clientGroup" ng-init="clientGroup.loadClientList()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="50" flex="100">
            <div layout="column" layout-align="end end">
               <md-button class="md-raised md-accent" ng-disabled="clientGroup.updatingClientGroup" ng-click="clientGroup.updateClientGroup( $event )" layout="row">
                  <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-hide="clientGroup.updatingClientGroup">save</md-icon>
                  <md-progress-circular ng-show="clientGroup.updatingClientGroup" md-mode="indeterminate" md-diameter="16"></md-progress-circular><span flex>Save</span>
               </md-button>
            </div>

            @include( 'pages.clientgroup.clientgroup-form' )

            <div layout="column" layout-align="end end">
               <md-button class="md-raised md-accent" ng-disabled="clientGroup.updatingClientGroup" ng-click="clientGroup.updateClientGroup( $event )" layout="row">
                  <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-hide="clientGroup.updatingClientGroup">save</md-icon>
                  <md-progress-circular ng-show="clientGroup.updatingClientGroup" md-mode="indeterminate" md-diameter="16"></md-progress-circular><span flex>Save</span>
               </md-button>
            </div>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
