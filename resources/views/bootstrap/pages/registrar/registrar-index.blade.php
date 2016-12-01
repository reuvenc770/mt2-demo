@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Registrar List' )

@section ( 'angular-controller' , 'ng-controller="RegistrarController as registrar"' )

@section ( 'page-menu' )
    @if (Sentinel::hasAccess('registrar.add'))
        <li><a ng-href="/registrar/create" target="_self">Add Registrar</a></li>
    @endif
@stop


@section( 'content' )
    <div ng-init="registrar.loadAccounts()">
                <md-table-container>
                    <table md-table md-progress="registrar.queryPromise">
                        <thead md-head md-order="registrar.sort" md-on-reorder="registrar.loadAccounts" class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="name" class="md-table-header-override-whitetext mt2-cell-left-padding">Registrar Name</th>
                            <th md-column class="md-table-header-override-whitetext">Username</th>
                            <th md-column class="md-table-header-override-whitetext">Password</th>
                            <th md-column class="md-table-header-override-whitetext">DBAs</th>
                            <th md-column class="md-table-header-override-whitetext">CC Contact</th>
                            <th md-column class="md-table-header-override-whitetext">CC #</th>
                            <th md-column class="md-table-header-override-whitetext">Notes</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in registrar.accounts track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <a ng-href="@{{ '/registrar/edit/' + record.id }}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    </a>
                                    <md-icon ng-if="record.status == 1" ng-click="registrar.toggle( record.id , 0 )" aria-label="Deactivate"
                                            md-font-set="material-icons" class="mt2-icon-black"
                                            data-toggle="tooltip" data-placement="bottom" title="Deactivate">pause</md-icon>
                                    <md-icon ng-if="record.status == 0" ng-click="registrar.toggle(record.id, 1 )" aria-label="Activate"
                                            md-font-set="material-icons" class="mt2-icon-black"
                                            data-toggle="tooltip" data-placement="bottom" title="Activate">play_arrow</md-icon>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.name }}</td>
                            <td md-cell>@{{ record.username }}</td>
                            <td md-cell>@{{ record.password }}</td>
                            <td md-cell nowrap>
                                <p class="no-margin" ng-repeat="value in record.dba_names">
                                    @{{ value.dba_name }} - Contact: @{{ value.dba_contact_name}} (@{{ value.dba_contact_email }})
                                </p>
                            </td>
                            <td md-cell nowrap>@{{ record.contact_credit_card }}</td>
                            <td md-cell>@{{ record.last_cc }}</td>
                            <td md-cell nowrap>@{{ record.notes }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                <md-content class="md-mt2-zeta-theme md-hue-2">
                                    <md-table-pagination md-limit="registrar.paginationCount" md-limit-options="registrar.paginationOptions" md-page="registrar.currentPage" md-total="@{{registrar.accountTotal}}" md-on-paginate="registrar.loadAccounts" md-page-select></md-table-pagination>
                                </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>

    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/registrar/RegistrarController.js',
                'resources/assets/js/bootstrap/registrar/RegistrarApiService.js'],'js','pageLevel') ?>
