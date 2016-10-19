@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Add Feed Group' )

@section( 'content' )
<div ng-controller="ClientGroupController as clientGroup" ng-init="clientGroup.loadClientList()">
    <button type="button" class="btn btn-primary btn-md pull-right padding" ng-class="{ 'disabled' : clientGroup.creatingClientGroup }" ng-click="clientGroup.saveClientGroup()" layout="row">
        <md-icon md-font-set="material-icons" ng-hide="clientGroup.creatingClientGroup">save</md-icon>
        <span class="glyphicon glyphicon-repeat" ng-show="clientGroup.creatingClientGroup" ng-class="{ 'rotateMe' : clientGroup.creatingClientGroup }"></span>
        Save
    </button>

    <div class="clearfix"></div>
    <br/>

    <fieldset>
        @include( 'bootstrap.pages.clientgroup.clientgroup-form' )
    </fieldset>

    <button type="button" class="btn btn-primary btn-md pull-right padding" ng-class="{ 'disabled' : clientGroup.creatingClientGroup }" ng-click="clientGroup.saveClientGroup()" layout="row">
        <md-icon md-font-set="material-icons" ng-hide="clientGroup.creatingClientGroup">save</md-icon>
        <span class="glyphicon glyphicon-repeat" ng-show="clientGroup.creatingClientGroup" ng-class="{ 'rotateMe' : clientGroup.creatingClientGroup }"></span>
        Save
    </button>
</div>

@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/clientgroup/ClientGroupController.js',
        'resources/assets/js/bootstrap/clientgroup/ClientGroupApiService.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js'],'js','pageLevel') ?>