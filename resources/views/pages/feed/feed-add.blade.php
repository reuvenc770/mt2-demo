@extends( 'layout.default' )

@section( 'title' , 'Add Feed' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Add Feed</h1></div>
</div>

<div ng-controller="FeedController as feed">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : feed.creatingFeed }" ng-click="feed.saveFeed( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : feed.creatingFeed }"></span> Save</button>

            <div class="clearfix"></div>

            @include( 'pages.feed.feed-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : feed.creatingFeed }" ng-click="feed.saveFeed( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : feed.creatingFeed }"></span> Save</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
