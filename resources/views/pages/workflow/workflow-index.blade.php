@extends('layout.default')

@section('title', 'Workflows')

@section('angular-controller', 'ng-controller="WorkflowController as workflow"')
@section('cacheTag', 'Workflow')
@section('page-menu')
    @if (Sentinel::hasAccess('workflow.add'))
        <li><a ng-href="/workflow/create" target="_self">Add Workflow</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="workflow.loadWorkflows()">
    <md-table-container>
        <table md-table md-progress="workflow.queryPromise">
            <thead md-head md-order="workflow.sort" class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="status" class="md-table-header-override-whitetext mt2-table-header-left">Status</th>
                    <th md-column md-order-by="name" class="md-table-header-override-whitetext">Name</th>
                    <th md-column md-order-by="esp_account" class="md-table-header-override-whitetext">ESP Account</th>
                    <th md-column md-order-by="created_at" class="md-table-header-override-whitetext">Created</th>
                    <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext">Updated</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="path in workflow.workflows track by $index" ng-class="{'bg-danger': !path.status, 'bg-success': path.status}">
                    <td md-cell class="mt2-table-btn-column">
                        <div layout="row" layout-align="center center">
                            <a ng-href="@{{ '/workflow/edit/' + path.id }}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                            </a>
                            <md-icon    
                                ng-if="!path.status"
                                ng-click="workflow.activate(path.id)"
                                aria-label="Activate Workflow"
                                md-font-set="material-icons"
                                class="mt2-icon-black"
                                data-toggle="tooltip"
                                data-placement="bottom"
                                title="Activate Workflow">play_circle_filled</md-icon>

                            <md-icon    
                                ng-if="path.status"
                                ng-click="workflow.pause(path.id)"
                                aria-label="Deactivate Workflow"
                                md-font-set="material-icons"
                                class="mt2-icon-black"
                                data-toggle="tooltip"
                                data-placement="bottom"
                                title="Pause Workflow">pause_circle_filled</md-icon>
                        </div>
                        </div>
                    </td>
                    <th md-cell>@{{ path.status === 1 ? 'Active' : 'Paused'}}</th>
                    <td md-cell>@{{ path.name }}</td>
                    <td md-cell>@{{ path.account_name }}</td>
                    <td md-cell nowrap ng-bind="::app.formatDate(path.created_at)"></td>
                    <td md-cell nowrap ng-bind="::app.formatDate(path.updated_at)"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">
                        <md-content class="md-mt2-zeta-theme">
                            <md-table-pagination md-limit="workflow.pageCount" md-limit-options="workflow.paginationOptions" md-page="workflow.currentPage" md-total="@{{workflow.workflowTotal}}" md-on-paginate="workflow.loadWorkflows" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/workflow/WorkflowController.js',
                'resources/assets/js/workflow/WorkflowApiService.js'],'js','pageLevel') ?>