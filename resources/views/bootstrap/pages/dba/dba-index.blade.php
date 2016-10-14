@extends( 'bootstrap.layout.default' )

@section( 'title' , 'DBA List' )
@section( 'container' , 'container-fluid' )
@section ( 'angular-controller' , 'ng-controller="DBAController as dba"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('dba.add'))
       <li><a ng-click="dba.viewAdd()" >Add DBA Account</a></li>
    @endif
@stop

@section( 'content' )
    <div ng-init="dba.loadAccounts()">
            <md-card>
                <md-table-container>
                    <table md-table md-progress="dba.queryPromise">
                        <thead md-head md-order="dba.sort" md-on-reorder="dba.loadAccounts">
                        <tr md-row>
                            <th md-column></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="dba_name" class="md-table-header-override-whitetext mt2-cell-left-padding">DBA Name</th>
                            <th md-column md-order-by="registrant_name" class="md-table-header-override-whitetext">Registrant Name</th>
                            <th md-column md-order-by="address" class="md-table-header-override-whitetext">Address</th>
                            <th md-column md-order-by="dba_email" class="md-table-header-override-whitetext">Email</th>
                            <th md-column md-order-by="password" class="md-table-header-override-whitetext">Password</th>
                            <th md-column md-order-by="phone" class="md-table-header-override-whitetext">Phone</th>
                            <th md-column md-order-by="po_boxes" class="md-table-header-override-whitetext">PO Boxes</th>
                            <th md-column md-order-by="entity_name" class="md-table-header-override-whitetext">Entity Name</th>
                            <th md-column md-order-by="notes" class="md-table-header-override-whitetext"> Notes</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="record in dba.accounts track by $index">
                            <td md-cell>
                                <div layout-gt-md="row" layout="column" layout-align="center center">
                                    <md-button class="md-icon-button" aria-label="Edit"
                                                ng-href="@{{ '/dba/edit/' + record.id }}" target="_self">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                        <md-tooltip md-direction="bottom">Edit</md-tooltip>
                                    </md-button>
                                    <md-button ng-if="record.status == 1" class="md-icon-button" ng-click="dba.toggle( record.id , 0 )">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                                        <md-tooltip md-direction="bottom">Deactivate</md-tooltip>
                                    </md-button>
                                    <md-button ng-if="record.status == 0" class="md-icon-button" ng-click="dba.toggle( record.id , 1 )">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                                        <md-tooltip md-direction="bottom">Activate</md-tooltip>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell class="mt2-cell-left-padding">@{{ record.dba_name }}</td>
                            <td md-cell>@{{ record.registrant_name }}</td>
                            <td md-cell>@{{ record.address }} @{{ record.city }} @{{ record.state }} @{{ record.zip }}</td>
                            <td md-cell>@{{ record.dba_email }}</td>
                            <td md-cell>@{{ record.password }}</td>
                            <td md-cell>@{{ record.phone }}</td>
                            <td md-cell><p ng-repeat="value in record.po_boxes">@{{ value.sub  }} - @{{value.address}} @{{value.city }} @{{value.state}} @{{value.zip}} - @{{value.phone}} <span ng-if="value.brands.length > 0">- Brands:</span> @{{ value.brands.join(', ') }}</p></td>
                            <td md-cell>@{{ record.entity_name }}</td>
                            <td md-cell>@{{ record.notes }}</td>
                        </tr>
                        </tbody>
                    </table>
                </md-table-container>

                <md-content class="md-mt2-zeta-theme md-hue-2">
                    <md-table-pagination md-limit="dba.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="dba.currentPage" md-total="@{{dba.accountTotal}}" md-on-paginate="dba.loadAccounts" md-page-select></md-table-pagination>
                </md-content>
            </md-card>
    </div>
@stop


<?php
Assets::add( [
    'resources/assets/js/bootstrap/dba/DBAController.js' ,
    'resources/assets/js/bootstrap/dba/DBAApiService.js' ,
] , 'js' , 'pageLevel' );
?>
