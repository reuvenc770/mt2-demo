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
        <div style="width:800px">
            <div class="panel mt2-theme-panel center-block">
                <div class="panel-heading">
                    <h3 class="panel-title">Search DBA</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">DBA Name*</span>
                                <input type="text" id="search_dba" class="form-control" value="" ng-model="dba.search.dba_name"/>
                            </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Registrant Name*</span>
                                <input type="text" id="search_dba" class="form-control" value="" ng-model="dba.search.registrant_name"/>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">DBA Email*</span>
                                <input type="text" id="search_dba" class="form-control" value="" ng-model="dba.search.dba_email"/>
                            </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Address*</span>
                                <input type="text" id="search_dba" class="form-control" value="" ng-model="dba.search.address"/>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Entity Name*</span>
                                <input type="text" id="search_dba" class="form-control" value="" ng-model="dba.search.entity_name"/>
                            </div>
                            </div>
                        </div>

                    </div>

                    <div class="pull-right">
                        <button class="btn btn-sm mt2-theme-btn-secondary" ng-click="dba.resetSearch()">Reset</button>
                        <button class="btn btn-sm mt2-theme-btn-primary" ng-click="dba.searchDBA()">Search</button>
                    </div>
                </div>
            </div>
        </div>
                <md-table-container>
                    <table md-table md-progress="dba.queryPromise">
                        <thead md-head md-order="dba.sort" md-on-reorder="dba.sortCurrentRecords" class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                            <th md-column md-order-by="dba_name" class="md-table-header-override-whitetext mt2-cell-left-padding">DBA Name</th>
                            <th md-column class="md-table-header-override-whitetext">Address</th>
                            <th md-column class="md-table-header-override-whitetext">Email</th>
                            <th md-column class="md-table-header-override-whitetext">Phone</th>
                            <th md-column class="md-table-header-override-whitetext">PO Boxes</th>
                            <th md-column class="md-table-header-override-whitetext">Brands</th>
                            <th md-column class="md-table-header-override-whitetext">ESP/ISP Use</th>
                            <th md-column class="md-table-header-override-whitetext">Entity Name</th>
                            <th md-column class="md-table-header-override-whitetext"> Notes</th>
                            <th md-column md-order-by="registrant_name" class="md-table-header-override-whitetext">Registrant Name</th>
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
                                    @if (Sentinel::hasAccess('api.dba.destroy'))
                                        <md-icon  ng-click="dba.delete( record.id )" aria-label="Delete Record"
                                                  md-font-set="material-icons" class="mt2-icon-black"
                                                  data-toggle="tooltip" data-placement="bottom" title="Delete Record">delete</md-icon>
                                    @endif
                                </div>
                            </td>
                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">
                                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td md-cell nowrap class="mt2-cell-left-padding">@{{ record.dba_name }}</td>
                            <td md-cell nowrap>@{{ record.address }} @{{ record.city }} @{{ record.state }} @{{ record.zip }}</td>
                            <td md-cell>@{{ record.dba_email }}</td>
                            <td md-cell nowrap>@{{ record.phone }}</td>
                            <td md-cell nowrap>
                                <span ng-repeat="value in record.po_boxes">
                                    @{{ $index + 1 }} -
                                    @{{value.address}} @{{value.city }} @{{value.state}} @{{value.zip}} <span ng-if="value.phone">- @{{value.phone}}</span>
                                    <span ng-show="record.po_boxes.length > 0 "><br/></span>
                                </span>
                            </td>
                            <td md-cell nowrap>
                                <span ng-repeat="value in record.po_boxes">
                                    @{{ value.brands }}
                                    <span ng-show="record.po_boxes.length > 0 "><br/></span>
                                </span>
                            </td>
                            <td md-cell nowrap>
                                <span ng-repeat="value in record.po_boxes">
                                    <span ng-if="value.esp_account_names.length > 0"><u>ESPs</u>: @{{ value.esp_account_names.join(', ') }}</span>
                                    <span ng-if="value.isp_names.length > 0 && value.esp_account_names.length > 0">, </span> <span ng-if="value.isp_names.length > 0"><u>ISPs</u>: @{{ value.isp_names.join(', ') }} </span>
                                    <span ng-show="record.po_boxes.length > 0 "><br/></span>
                                </span>
                            </td>
                            <td md-cell nowrap>@{{ record.entity_name }}</td>
                            <td md-cell nowrap>@{{ record.notes }}</td>
                            <td md-cell nowrap>@{{ record.registrant_name }}</td>
                        </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="12">
                                    <md-content class="md-mt2-zeta-theme md-hue-2">
                                        <md-table-pagination md-limit="dba.paginationCount" md-limit-options="dba.paginationOptions" md-page="dba.currentPage" md-total="@{{dba.accountTotal}}" md-on-paginate="dba.loadAccounts" md-page-select></md-table-pagination>
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
