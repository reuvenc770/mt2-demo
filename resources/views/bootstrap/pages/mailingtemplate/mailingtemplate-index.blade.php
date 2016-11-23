
@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Mailing Templates' )

@section( 'angular-controller' , 'ng-controller="MailingTemplateController as mailing"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('mailingtemplate.add'))
        <li> <a ng-href="/mailingtemplate/create" target="_self" aria-label="Add Mailing Templates">Add Mailing Templates</a>
        </li>
    @endif
@stop

@section( 'content' )
<div ng-init="mailing.loadAccounts()">
            <md-table-container>
                <table md-table md-progress="mailing.queryPromise">
                    <thead md-head md-order="mailing.sort" md-on-reorder="mailing.loadAccounts">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column md-order-by="id" class="md-table-header-override-whitetext">ID</th>
                            <th md-column md-order-by="template_name" class="md-table-header-override-whitetext">Template Name</th>
                            <th md-column md-order-by="template_type" class="md-table-header-override-whitetext">Template Type</th>
                        </tr>
                    </thead>

                    <tbody md-body>
                        <tr md-row ng-repeat="record in mailing.templates track by $index">
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <a ng-href="@{{ '/mailingtemplate/edit/' + record.id }}" target="_self" aria-label="Edit" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                                    </a>
                                </div>
                            </td>
                            <td md-cell>@{{ record.id }}</td>
                            <td md-cell>@{{ record.template_name }}</td>
                            <td md-cell>@{{ mailing.templateTypeMap[record.template_type] }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <md-content class="md-mt2-zeta-theme md-hue-2">
                                    <md-table-pagination md-limit="mailing.paginationCount" md-limit-options="mailing.paginationOptions" md-page="mailing.currentPage" md-total="@{{mailing.templateTotal}}" md-on-paginate="mailing.loadAccounts" md-page-select></md-table-pagination>
                                </md-content>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </md-table-container>

</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/mailingtemplate/MailingTemplateController.js',
                'resources/assets/js/bootstrap/mailingtemplate/MailingTemplateApiService.js'],'js','pageLevel') ?>