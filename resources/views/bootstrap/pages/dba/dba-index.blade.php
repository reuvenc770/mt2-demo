@extends( 'bootstrap.layout.default' )

@section( 'title' , 'DBA List' )
@section( 'container' , 'container-fluid' )
@section ( 'angular-controller' , 'ng-controller="DBAController as dba"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('dba.add'))
       <li><a ng-href="/dba/create" target="_self">Add DBA Account</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="dba.loadAccounts()">

                <md-table-container>
                    <table md-table md-progress="dba.queryPromise">
                        <thead md-head md-order="dba.sort" md-on-reorder="dba.loadAccounts">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="dba_name" class="md-table-header-override-whitetext mt2-cell-left-padding">DBA Name</th>
                            <th md-column md-order-by="registrant_name" class="md-table-header-override-whitetext">Registrant Name</th>
                            <th md-column md-order-by="address" class="md-table-header-override-whitetext">Address</th>
                            <th md-column md-order-by="dba_email" class="md-table-header-override-whitetext">Email</th>
                            <th md-column md-order-by="password" class="md-table-header-override-whitetext">Password</th>
                            <th md-column md-order-by="phone" class="md-table-header-override-whitetext">Phone</th>
                            <th md-column md-order-by="po_boxes" class="md-table-header-override-whitetext">PO Boxes</th>
                            <th md-column class="md-table-header-override-whitetext">ESP/ISP Use</th>
                            <th md-column md-order-by="entity_name" class="md-table-header-override-whitetext">Entity Name</th>
                            <th md-column md-order-by="notes" class="md-table-header-override-whitetext"> Notes</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in dba.accounts track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <a ng-href="@{{ '/dba/edit/' + record.id }}" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black" aria-label="Edit">edit</md-icon>
                                    </a>
                                    <md-icon ng-if="record.status == 1" ng-click="dba.toggle( record.id , 0 )" md-font-set="material-icons"
                                            class="mt2-icon-black" data-toggle="tooltip" data-placement="bottom" title="Deactivate" aria-label="Deactivate">pause</md-icon>
                                    <md-icon ng-if="record.status == 0" ng-click="dba.toggle( record.id , 1 )" md-font-set="material-icons"
                                            class="mt2-icon-black" data-toggle="tooltip" data-placement="bottom" title="Activate" aria-label="Activate">play_arrow</md-icon>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.dba_name }}</td>
                            <td md-cell>@{{ record.registrant_name }}</td>
                            <td md-cell nowrap>@{{ record.address }} @{{ record.city }} @{{ record.state }} @{{ record.zip }}</td>
                            <td md-cell>@{{ record.dba_email }}</td>
                            <td md-cell>@{{ record.password }}</td>
                            <td md-cell>@{{ record.phone }}</td>
                            <td md-cell nowrap>
                                <p ng-repeat="value in record.po_boxes">
                                    @{{value.address}} @{{value.city }} @{{value.state}} @{{value.zip}} - @{{value.phone}} <span ng-if="value.brands.length > 0">- Brands:</span> @{{ value.brands }}
                                </p>
                            </td>
                            <td md-cell nowrap>
                                <p ng-repeat="value in record.po_boxes">
                                    <span ng-if="value.esp_account_names.length > 0">ESPs: @{{ value.esp_account_names.join(', ') }} </span>
                                    <span ng-if="value.isp_names.length > 0"><br/>ISPs: @{{ value.isp_names.join(', ') }} </span>
                                </p>
                            </td>
                            <td md-cell>@{{ record.entity_name }}</td>
                            <td md-cell nowrap>@{{ record.notes }}</td>
                        </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="12">
                                    <md-content class="md-mt2-zeta-theme md-hue-2">
                                        <md-table-pagination md-limit="dba.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="dba.currentPage" md-total="@{{dba.accountTotal}}" md-on-paginate="dba.loadAccounts" md-page-select></md-table-pagination>
                                    </md-content>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </md-table-container>


    </div>
@stop


<?php
Assets::add( [
    'resources/assets/js/bootstrap/dba/DBAController.js' ,
    'resources/assets/js/bootstrap/dba/DBAApiService.js' ,
] , 'js' , 'pageLevel' );
?>
