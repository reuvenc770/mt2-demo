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
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="clientGroup.paginationCount" currentpage="clientGroup.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="clientGroup.currentPage" maxpage="clientGroup.pageCount"></pagination>
                    </md-input-container>
                </div>

                <clientgroup-table records="clientGroup.clientGroups" children="clientGroup.clientMap" loadingflag="clientGroup.currentlyLoading" loadchildren="clientGroup.loadClients( groupID )" copygroup="clientGroup.copyClientGroup( groupID )" deletegroup="clientGroup.deleteClientGroup( groupID )" copyingflag="clientGroup.copyingClientGroup" deletingflag="clientGroup.deletingClientGroup"></clientgroup-table>

                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="clientGroup.paginationCount" currentpage="clientGroup.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="clientGroup.currentPage" maxpage="clientGroup.pageCount"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientgroup.js"></script>
@stop
