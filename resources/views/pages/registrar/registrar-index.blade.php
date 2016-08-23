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
    <div ng-init="registrar.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-card-content>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="registrar.paginationCount"
                                              currentpage="registrar.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="registrar.currentPage" maxpage="registrar.pageCount"></pagination>
                        </md-input-container>
                    </div>
                    <div id="mtTableContainer" class="table-responsive">
                        <registrar-table records="registrar.accounts" toggle="registrar.toggle(recordId, direction)"></registrar-table>
                    </div>
                    <div layout="row">
                        <md-input-container flex-gt-sm="10" flex="30">
                            <pagination-count recordcount="registrar.paginationCount"
                                              currentpage="registrar.currentPage"></pagination-count>
                        </md-input-container>

                        <md-input-container flex="auto">
                            <pagination currentpage="registrar.currentPage" maxpage="registrar.pageCount"></pagination>
                        </md-input-container>
                    </div>
                </md-card-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop
