@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Add Feed' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<div>
    <button type="button" class="btn btn-primary btn-md pull-right padding" ng-class="{ 'disabled' : feed.creatingFeed }" ng-click="feed.saveFeed()" layout="row">
        <md-icon md-font-set="material-icons" ng-hide="feed.creatingFeed">save</md-icon>
        <span class="glyphicon glyphicon-repeat" ng-show="feed.creatingFeed" ng-class="{ 'rotateMe' : feed.creatingFeed }"></span>
        Save
    </button>

    <div class="clearfix"></div>

    @include( 'bootstrap.pages.feed.feed-form' )

    <button type="button" class="btn btn-primary btn-md pull-right padding" ng-class="{ 'disabled' : feed.creatingFeed }" ng-click="feed.saveFeed()" layout="row">
        <md-icon md-font-set="material-icons" ng-hide="feed.creatingFeed">save</md-icon>
        <span class="glyphicon glyphicon-repeat" ng-show="feed.creatingFeed" ng-class="{ 'rotateMe' : feed.creatingFeed }"></span>
        Save
    </button>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feed/FeedController.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js',
        'resources/assets/js/bootstrap/feed/FeedUrlModalDirective.js'],'js','pageLevel') ?>
