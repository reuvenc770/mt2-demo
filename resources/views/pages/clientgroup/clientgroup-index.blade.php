@extends( 'layout.default' )

@section( 'title' , 'Client Group' )

@section( 'angular-controller' , 'ng-controller="ClientGroupController as clientGroup"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('clientgroup.add'))
        <md-button ng-click="clientGroup.viewAdd()" aria-label="Add Client Group">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Client Group</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="clientGroup.loadClientGroups()">
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="clientGroup.paginationCount" currentpage="clientGroup.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="clientGroup.currentPage" maxpage="clientGroup.pageCount"></pagination>
                </div>
            </div>

            <clientgroup-table records="clientGroup.clientGroups" children="clientGroup.clientMap" loadingflag="clientGroup.currentlyLoading" loadchildren="clientGroup.loadClients( groupID )" copygroup="clientGroup.copyClientGroup( groupID )" deletegroup="clientGroup.deleteClientGroup( groupID )" copyingflag="clientGroup.copyingClientGroup" deletingflag="clientGroup.deletingClientGroup"></clientgroup-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="clientGroup.paginationCount" currentpage="clientGroup.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="clientGroup.currentPage" maxpage="clientGroup.pageCount"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
