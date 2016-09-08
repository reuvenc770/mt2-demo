
@extends( 'layout.default' )

@section( 'title' , 'Domain List' )

@section( 'angular-controller' , 'ng-controller="domainController as domain"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('domain.add'))
        <md-button ng-click="domain.viewAdd()" aria-label="Add Domain">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Domain</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-init="domain.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-card-content>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="domain.paginationCount" currentpage="domain.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="domain.currentPage" maxpage="domain.pageCount"></pagination>
                        </md-input-container>
                    </div>

                    <domain-table records="domain.accounts"></domain-table>

                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="domain.paginationCount" currentpage="domain.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="domain.currentPage" maxpage="domain.pageCount"></pagination>
                        </md-input-container>
                    </div>
                </md-card-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
