@extends( 'layout.default' )

@section( 'title' , 'MT2 Registrar List' )

@section ( 'angular-controller' , 'ng-controller="RegistrarController as registrar"' )

@section ( 'page-menu' )
    @if (Sentinel::hasAccess('registrar.add'))
        <md-button ng-click="registrar.viewAdd()" aria-label="Add Registrar">
            <md-icon ng-hide="app.largePageWidth()" md-svg-src="img/icons/ic_add_circle_outline_white_24px.svg"></md-icon>
            <span ng-show="app.largePageWidth()">Add Registrar</span>
        </md-button>
    @endif
@stop

@section( 'content' )
    <div ng-controller="RegistrarController as registrar" ng-init="registrar.loadAccounts()">
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
