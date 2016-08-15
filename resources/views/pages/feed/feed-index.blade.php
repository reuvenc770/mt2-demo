@extends( 'layout.default' )

@section( 'title' , 'Feed' )

@section( 'navFeedClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Feeds</h1></div>
</div>

<div ng-controller="FeedController as feed" ng-init="feed.loadClients()">
    @if (Sentinel::hasAccess('feed.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="feed.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Feed</button>
    </div>
    @endif
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="feed.paginationCount" currentpage="feed.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="feed.currentPage" maxpage="feed.pageCount" disableceiling="feed.reachedMaxPage" disablefloor="feed.reachedFirstPage"></pagination>
                </div>
            </div>

            <client-table records="feed.clients" loadingflag="feed.currentlyLoading"></client-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="feed.paginationCount" currentpage="feed.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="feed.currentPage" maxpage="feed.pageCount" disableceiling="feed.reachedMaxPage" disablefloor="feed.reachedFirstPage"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/feed.js"></script>
@stop
