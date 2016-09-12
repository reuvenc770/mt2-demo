
@extends( 'layout.default' )

@section( 'title' , 'Edit Feed' )

@section( 'navFeedClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )

<div ng-init="feed.loadFeed()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-md="50" flex="100">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disable' : feed.updatingFeed }" ng-click="feed.updateFeed( $event )"><span class="glyphicon glyphicon-repeat" ng-class="{ 'rotateMe' : feed.updatingFeed }"></span> Update</button>
            <button type="button" class="btn btn-info btn-md pull-right" ng-class="{ 'disable' : feed.generatingLinks }" ng-click="feed.generateLinks()"><span class="glyphicon glyphicon-link" ng-class="{ 'rotateMe' : feed.generatingLinks }"></span> Generate Links</button>
            <button type="button" class="btn btn-danger btn-md pull-right"  ng-click="feed.resetPassword()"><span class="glyphicon glyphicon-cog" ng-class="{ 'rotateMe' : feed.generatingLinks }"></span> Reset FTP Password</button>

            <div class="clearfix"></div>

            @include( 'pages.feed.feed-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disable' : feed.updatingFeed }" ng-click="feed.updateFeed( $event )"><span class="glyphicon glyphicon-repeat" ng-class="{ 'rotateMe' : feed.updatingFeed }"></span> Update</button>
            <button type="button" class="btn btn-info btn-md pull-right" ng-class="{ 'disable' : feed.generatingLinks }" ng-click="feed.generateLinks()"><span class="glyphicon glyphicon-link" ng-class="{ 'rotateMe' : feed.generatingLinks }"></span> Generate Links</button>
        </div>
    </md-content>

    <feed-url-modal records="feed.urlList"></feed-url-modal>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
