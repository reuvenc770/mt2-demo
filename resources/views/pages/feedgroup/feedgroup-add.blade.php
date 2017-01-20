`@extends( 'layout.default' )

@section( 'title' , 'Add Feed Group' )

@section( 'angular-controller' , 'ng-controller="FeedGroupController as feedGroup"' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-init="feedGroup.loadFeedList()">
    @include( 'pages.feedgroup.feedgroup-form' )

    <div class="panel-footer">
        <button type="button" class="btn mt2-theme-btn-primary btn-block" ng-click="feedGroup.saveFeedGroup()" ng-disabled="feedGroup.creatingFeedGroup">
            Add Feed Group
        </button>
    </div>
</div>

@stop

<?php Assets::add(
        ['resources/assets/js/feedgroup/FeedGroupController.js',
        'resources/assets/js/feedgroup/FeedGroupApiService.js',
        'resources/assets/js/feed/FeedApiService.js'],'js','pageLevel') ?>
