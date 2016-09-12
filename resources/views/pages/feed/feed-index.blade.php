@extends( 'layout.default' )

@section( 'title' , 'Feed' )

@section( 'navFeedClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('feed.add'))
        <md-button ng-click="feed.viewAdd()">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Feed</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="feed.loadFeeds()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="feed.paginationCount" currentpage="feed.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="feed.currentPage" maxpage="feed.pageCount" disableceiling="feed.reachedMaxPage" disablefloor="feed.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>

                <feed-table records="feed.feeds" loadingflag="feed.currentlyLoading"></feed-table>

                <div class="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="feed.paginationCount" currentpage="feed.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="feed.currentPage" maxpage="feed.pageCount" disableceiling="feed.reachedMaxPage" disablefloor="feed.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
