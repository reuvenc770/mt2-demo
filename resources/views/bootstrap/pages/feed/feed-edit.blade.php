@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit Feed' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<div ng-init="feed.loadFeed()">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">Edit Feed</div>
        </div>
        <fieldset>
            <input type="hidden" ng-model="feed.current.id" />

            @include( 'bootstrap.pages.feed.feed-form' )

        </fieldset>
        <div class="panel-footer">
            <div class="row">
                <div class="form-group col-sm-6">
                    <input class="btn btn-primary btn-block" ng-click="feed.updateFeed()" ng-disabled="feed.formSubmitted" type="submit" value="Update Feed">
                </div>
                <div class="form-group col-sm-6">
                    <input class="btn btn-success btn-block" ng-click="feed.resetPassword()" ng-disabled="feed.formSubmitted" type="submit" value="Reset Password">
                </div>
            </div>
        </div>
    </div>
</div>

@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feed/FeedController.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js',
        'resources/assets/js/bootstrap/feed/FeedUrlModalDirective.js'],'js','pageLevel') ?>
