
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
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="domain.paginationCount" currentpage="domain.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="domain.currentPage" maxpage="domain.pageCount"></pagination>
                    </div>
                </div>

                <domain-table records="domain.accounts"></domain-table>

                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="domain.paginationCount" currentpage="domain.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="domain.currentPage" maxpage="domain.pageCount"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/domain.js"></script>
@stop
