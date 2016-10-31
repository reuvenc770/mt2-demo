@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Update Feed Group' )

@section( 'angular-controller' , 'ng-controller="FeedGroupController as feedGroup"' )

@section( 'content' )
<div class="panel panel-primary" ng-init="feedGroup.loadFeedList();feedGroup.setId( {{$id}} );feedGroup.setName( '{{$name}}' );feedGroup.setFeeds( {{$feeds}} )">
    @include( 'bootstrap.pages.feedgroup.feedgroup-form' )

    <div class="panel-footer">
        <button type="button" class="btn btn-primary btn-lg btn-block" ng-click="feedGroup.updateFeedGroup()" ng-disabled="feedGroup.updatingFeedGroup">
           Update Feed Group
        </button>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feedgroup/FeedGroupController.js',
        'resources/assets/js/bootstrap/feedgroup/FeedGroupApiService.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js'],'js','pageLevel') ?>
