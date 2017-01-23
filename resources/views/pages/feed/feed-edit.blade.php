@extends( 'layout.default' )

@section( 'title' , 'Edit Feed' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<div ng-init="feed.loadFeed()">
    <div class="panel mt2-theme-panel">
        <div class="panel-heading">
            <div class="panel-title">Edit Feed</div>
        </div>
        <fieldset>
            <input type="hidden" ng-model="feed.current.id" />

            @include( 'pages.feed.feed-form' )

        </fieldset>
        <div class="panel-footer">

            <div class="row">
                <div class="form-group col-sm-6">
                    <input class="btn btn-primary btn-block" ng-click="feed.runReattribution()" ng-disabled="feed.isReattributing" type="submit" value="Reattribute Records (Non-Unique)">
                </div>
                <div class="form-group col-sm-6">
                    <input class="btn btn-primary btn-block" ng-click="feed.createSuppression()" ng-disabled="feed.isSuppressing" type="submit" value="Create Feed Suppression (Unique)">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-click="feed.updateFeed()" ng-disabled="feed.formSubmitted" type="submit" value="Update Feed">
                </div>
                <div class="form-group col-sm-6">
                    <input class="btn mt2-theme-btn-secondary btn-block" ng-click="feed.resetPassword()" ng-disabled="feed.formSubmitted" type="submit" value="Reset Password">
                </div>
            </div>
        </div>
    </div>
</div>

@stop

<?php Assets::add(
        ['resources/assets/js/feed/FeedController.js',
        'resources/assets/js/feed/FeedApiService.js'],'js','pageLevel') ?>

