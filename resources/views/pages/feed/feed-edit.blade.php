
@extends( 'layout.default' )

@section( 'title' , 'Edit Feed' )

@section( 'navFeedClasses' , 'active' )


@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Edit Client</h1></div>
</div>

<div ng-controller="FeedController as feed" ng-init="feed.loadClient()">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disable' : feed.updatingClient }" ng-click="feed.updateClient( $event )"><span class="glyphicon glyphicon-repeat" ng-class="{ 'rotateMe' : feed.updatingClient }"></span> Update</button>
            <button type="button" class="btn btn-info btn-md pull-right" ng-class="{ 'disable' : feed.generatingLinks }" ng-click="feed.generateLinks()"><span class="glyphicon glyphicon-link" ng-class="{ 'rotateMe' : client.generatingLinks }"></span> Generate Links</button>
            <button type="button" class="btn btn-danger btn-md pull-right"  ng-click="feed.resetPassword()"><span class="glyphicon glyphicon-cog" ng-class="{ 'rotateMe' : feed.generatingLinks }"></span> Reset FTP Password</button>

            <div class="clearfix"></div>

            @include( 'pages.feed.client-form' )

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disable' : feed.updatingClient }" ng-click="feed.updateClient( $event )"><span class="glyphicon glyphicon-repeat" ng-class="{ 'rotateMe' : feed.updatingClient }"></span> Update</button>
            <button type="button" class="btn btn-info btn-md pull-right" ng-class="{ 'disable' : feed.generatingLinks }" ng-click="feed.generateLinks()"><span class="glyphicon glyphicon-link" ng-class="{ 'rotateMe' : feed.generatingLinks }"></span> Generate Links</button>
        </div>
    </div>

    <client-url-modal records="feed.urlList"></client-url-modal>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
