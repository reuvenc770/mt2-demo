@extends( 'layout.default' )

@section( 'title' , 'Client' )

@section( 'navClientClasses' , 'active' )

@section( 'angular-controller', 'ng-controller="ClientController as client"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('client.add'))
        <md-button ng-click="client.viewAdd()" aria-label="Add Client">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Client</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="client.loadClients()">
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <md-card>
            <md-card-content>
                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="client.paginationCount" currentpage="client.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="client.currentPage" maxpage="client.pageCount" disableceiling="client.reachedMaxPage" disablefloor="client.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>

                <client-table records="client.clients" loadingflag="client.currentlyLoading"></client-table>

                <div layout="row">
                    <md-input-container flex-gt-sm="10" flex="30">
                        <pagination-count recordcount="client.paginationCount" currentpage="client.currentPage"></pagination-count>
                    </md-input-container>

                    <md-input-container flex="auto">
                        <pagination currentpage="client.currentPage" maxpage="client.pageCount" disableceiling="client.reachedMaxPage" disablefloor="client.reachedFirstPage"></pagination>
                    </md-input-container>
                </div>
            </md-card-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/client.js"></script>
@stop
