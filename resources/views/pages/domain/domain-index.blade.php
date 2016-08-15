
@extends( 'layout.default' )

@section( 'title' , 'Domain List' )


@section( 'content' )
    <div ng-controller="domainController as domain" ng-init="domain.loadAccounts()">
        @if (Sentinel::hasAccess('domain.add'))
            <div class="row">
                <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="domain.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Domain</button>
            </div>
        @endif

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
