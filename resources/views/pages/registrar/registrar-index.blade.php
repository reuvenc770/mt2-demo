@extends( 'layout.default' )

@section( 'title' , 'MT2 Registrar List' )


@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Registrars</h1></div>
    </div>

    <div ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccounts()">
        @if (Sentinel::hasAccess('registrar.add'))
        <div class="row">
            <button type="button" class="btn btn-info btn-lg pull-right mt2-header-btn" ng-click="registrar.viewAdd()"><span class="glyphicon glyphicon-plus"></span> Add Registrar</button>
        </div>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="registrar.paginationCount"
                                          currentpage="registrar.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="registrar.currentPage" maxpage="registrar.pageCount"></pagination>
                    </div>
                </div>
                <div id="mtTableContainer" class="table-responsive">
                    <registrar-table records="registrar.accounts" toggle="registrar.toggle(recordId, direction)"></registrar-table>
                </div>
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                        <pagination-count recordcount="registrar.paginationCount"
                                          currentpage="registrar.currentPage"></pagination-count>
                    </div>

                    <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                        <pagination currentpage="registrar.currentPage" maxpage="registrar.pageCount"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop
