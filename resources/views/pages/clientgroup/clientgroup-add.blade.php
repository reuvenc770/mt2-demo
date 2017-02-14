@extends( 'layout.default' )

@section( 'title' , 'Add Feed Group' )

@section( 'content' )

<div ng-controller="ClientGroupController as clientGroup" ng-init="clientGroup.loadClientList()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="50" flex="100">
            <div layout="column" layout-align="end end">
               <md-button class="md-raised md-accent" ng-disabled="clientGroup.creatingClientGroup" ng-click="clientGroup.saveClientGroup( $event , feedGroupForm )" layout="row">
                  <md-icon md-font-set="material-icons" class="mt2-icon-black">save</md-icon> <span flex>Save</span>
               </md-button>
            </div>

            @include( 'pages.clientgroup.clientgroup-form' )

            <div layout="column" layout-align="end end">
               <md-button class="md-raised md-accent" ng-disabled="clientGroup.creatingClientGroup" ng-click="clientGroup.saveClientGroup( $event , feedGroupForm )" layout="row">
                  <md-icon md-font-set="material-icons" class="mt2-icon-black">save</md-icon> <span flex>Save</span>
               </md-button>
            </div>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
