@extends( 'layout.default' )

@section( 'title' , 'Client Group' )

@section( 'content' )

<div ng-controller="ClientGroupController as clientGroup" ng-init="clientGroup.loadClientGroups()">
    @if (Sentinel::hasAccess('clientgroup.add'))
    <div class="row">
        <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="clientGroup.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Client Group</button>
    </div>
    @endif
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
