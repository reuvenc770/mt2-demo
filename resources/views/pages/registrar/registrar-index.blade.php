@extends( 'layout.default' )

@section( 'title' , 'MT2 Registrar List' )

@section ( 'angular-controller' , 'ng-controller="RegistrarController as registrar"' )

@section ( 'page-menu' )
    @if (Sentinel::hasAccess('registrar.add'))
        <md-button ng-click="registrar.viewAdd()" aria-label="Add Registrar">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add Registrar</span>
        </md-button>
    @endif
@stop


@section( 'content' )
    <div ng-init="registrar.loadAccounts()">
        <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
            <md-card>
                <md-table-container>
                    <table md-table md-progress="registrar.queryPromise">
                        <thead md-head md-order="registrar.sort" md-on-reorder="registrar.loadAccounts">
                        <tr md-row>
                            <th md-column></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-cell-left-padding">Registrar Name</th>
                            <th md-column md-order-by="username" class="md-table-header-override-whitetext">Username</th>
                            <th md-column md-order-by="contact_name" class="md-table-header-override-whitetext">Contact Name</th>
                            <th md-column md-order-by="contact_email" class="md-table-header-override-whitetext">Contact Email</th>
                            <th md-column md-order-by="phone_number" class="md-table-header-override-whitetext">Phone</th>
                            <th md-column md-order-by="entity_name" class="md-table-header-override-whitetext">Entity Name</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in registrar.accounts track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }" ng-href="@{{ '/registrar/edit/' + record.id }}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-hide="app.isMobile()"> Edit</span>
                                    </md-button>
                                    <md-button ng-if="record.status == 1" class="md-raised md-accent mt2-button-xs" ng-click="registrar.toggle( record.id , 0 )">Deactivate</md-button>
                                    <md-button ng-if="record.status == 0" class="md-raised md-accent mt2-button-xs" ng-click="registrar.toggle(record.id, 1 )">Activate</span>
                                </md-button>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.name }}</td>
                            <td md-cell>@{{ record.username }}</td>
                            <td md-cell>@{{ record.contact_name }}</td>
                            <td md-cell>@{{ record.contact_email }}</td>
                            <td md-cell>@{{ record.phone_number }}</td>
                            <td md-cell>@{{ record.entity_name }}</td>
                        </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="registrar.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="registrar.currentPage" md-total="@{{registrar.accountTotal}}" md-on-paginate="registrar.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
        </md-content>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/registrar.js"></script>
@stop
