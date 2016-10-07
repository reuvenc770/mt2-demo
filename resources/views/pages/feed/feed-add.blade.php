@extends( 'layout.default' )

@section( 'title' , 'Add Feed' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )
<md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

    <div flex-gt-md="50" flex="100">
        <div layout="row" layout-align="end end">
            <md-button layout="row" class="md-raised md-accent" ng-disabled="feed.creatingFeed" ng-click="feed.saveFeed( $event , feedForm )">
                <md-icon class="material-icons" md-font-set="material-icons" ng-hide="feed.creatingFeed">save</md-icon>
                <md-progress-circular ng-show="feed.creatingFeed" md-mode="indeterminate" md-diameter="16"></md-progress-circular> <span flex>Save</span>
            </md-button>
        </div>

        @include( 'pages.feed.feed-form' )

        <div layout="row" layout-align="end end">
            <md-button layout="row" class="md-raised md-accent" ng-disabled="feed.creatingFeed" ng-click="feed.saveFeed( $event , feedForm )">
                <md-icon class="material-icons" md-font-set="material-icons" ng-hide="feed.creatingFeed">save</md-icon>
                <md-progress-circular ng-show="feed.creatingFeed" md-mode="indeterminate" md-diameter="16"></md-progress-circular> <span flex>Save</span>
            </md-button>
        </div>
    </div>
</md-content>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
