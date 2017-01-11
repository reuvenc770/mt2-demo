@extends( 'layout.default' )

@section( 'title' , 'Add Feed' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<div>
    <div class="panel mt2-theme-panel">
        <div class="panel-heading">
            <div class="panel-title">Add Feed</div>
        </div>
            <fieldset>

            @include( 'pages.feed.feed-form' )

            </fieldset>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn mt2-theme-btn-primary btn-block" ng-click="feed.saveFeed()" ng-disabled="feed.formSubmitted" type="submit" value="Add Feed">
            </div>
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/feed/FeedController.js',
        'resources/assets/js/feed/FeedApiService.js'],'js','pageLevel') ?>
