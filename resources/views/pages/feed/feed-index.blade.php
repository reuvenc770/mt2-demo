@extends( 'layout.default' )

@section( 'title' , 'Feed' )

@section( 'navFeedClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('feed.add'))
        <md-button ng-click="feed.viewAdd()">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Feed</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="feed.loadFeeds()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            @include( 'pages.feed.feed-table' )
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
