
@extends( 'layout.default' )

@section( 'title' , 'Clients' )

@section( 'angular-controller' , 'ng-controller="ClientController as client"' )
@section( 'cacheTag' , 'Client' )
@section( 'page-menu' )
    @if (Sentinel::hasAccess('client.add'))
        <li><a ng-href="/client/create" target="_self">Add Client</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="client.loadAccounts()">
    <md-table-container>
        <table md-table md-progress="client.queryPromise">
            <thead md-head md-order="client.sort" md-on-reorder="client.loadAccounts" class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext"></th>
                    <th md-column class="md-table-header-override-whitetext" md-order-by="id">ID</th>
                    <th md-column class="md-table-header-override-whitetext" md-order-by="name">Name</th>
                    <th md-column class="md-table-header-override-whitetext">Address</th>
                    <th md-column class="md-table-header-override-whitetext">Email Address</th>
                    <th md-column class="md-table-header-override-whitetext">Phone</th>
                    <th md-column class="md-table-header-override-whitetext mt2-table-header-center" md-order-by="status">Status</th>
                    <th md-column class="md-table-header-override-whitetext" md-order-by="created_at" class="mt2-cell-left-padding">Created</th>
                    <th md-column class="md-table-header-override-whitetext" md-order-by="updated_at">Updated</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in client.accounts track by $index">
                    <td md-cell class="mt2-table-btn-column">
                        <div layout="row" layout-align="center center">
                            <a ng-href="@{{ '/client/edit/' + record.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                            </a>
                        </div>
                    </td>
                    <td md-cell ng-bind="record.id"></td>
                    <td md-cell ng-bind="record.name" nowrap></td>
                    <td md-cell nowrap>@{{ record.address }} @{{ record.address2 }}, @{{ record.city }} @{{ record.state }} @{{ record.zip }}</td>
                    <td md-cell ng-bind="record.email_address"></td>
                    <td md-cell ng-bind="record.phone"></td>
                    <td md-cell ng-bind="record.status" class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 'Active' , 'bg-warning' : record.status == 'Paused' , 'bg-danger' : record.status == 'Inactive' }"></td>
                    <td md-cell ng-bind="record.created_at" nowrap class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.updated_at" nowrap></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9">
                        <md-content class="md-mt2-zeta-theme md-hue-2">
                            <md-table-pagination md-limit="client.paginationCount" md-limit-options="client.paginationOptions" md-page="client.currentPage" md-total="@{{client.accountTotal}}" md-on-paginate="client.loadAccounts" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/client/ClientController.js',
                'resources/assets/js/bootstrap/client/ClientApiService.js'],'js','pageLevel') ?>
