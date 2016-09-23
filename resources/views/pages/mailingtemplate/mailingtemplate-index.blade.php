
@extends( 'layout.default' )

@section( 'title' , 'Mailing Templates' )

@section( 'navEspClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="MailingTemplateController as mailing"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('mailingtemplate.add'))
        <md-button ng-click="mailing.viewAdd()" aria-label="Add Mailing Templates">
            <md-icon ng-show="app.isMobile()" md-svg-src="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
            <span ng-hide="app.isMobile()">Add Mailing Templates</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="mailing.loadAccounts()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <md-card flex-gt-md="70" flex="100">
            <md-table-container>
                <table md-table md-progress="mailing.queryPromise">
                    <thead md-head md-order="mailing.sort" md-on-reorder="mailing.loadAccounts">
                        <tr md-row>
                            <th md-column ></th>
                            <th md-column md-order-by="id" class="md-table-header-override-whitetext">ID</th>
                            <th md-column md-order-by="template_name" class="md-table-header-override-whitetext">Template Name</th>
                            <th md-column md-order-by="template_type" class="md-table-header-override-whitetext">Template Type</th>
                        </tr>
                    </thead>

                    <tbody md-body>
                        <tr md-row ng-repeat="record in mailing.templates track by $index">
                            <td md-cell>
                                <div layout="row" layout-align="center center">
                                    <md-button class="md-raised" ng-class="{'md-icon-button mt2-icon-button-xs' : app.isMobile() , 'mt2-button-xs' : !app.isMobile() }" ng-href="@{{ '/mailingtemplate/edit/' + record.id }}" target="_self">
                                        <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon><span ng-hide="app.isMobile()"> Edit</span>
                                    </md-button>
                                </div>
                            </td>
                            <td md-cell>@{{ record.id }}</td>
                            <td md-cell>@{{ record.template_name }}</td>
                            <td md-cell>@{{ mailing.templateTypeMap[record.template_type] }}</td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

            <md-content class="md-mt2-zeta-theme md-hue-2">
                <md-table-pagination md-limit="mailing.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="mailing.currentPage" md-total="@{{mailing.templateTotal}}" md-on-paginate="mailing.loadAccounts" md-page-select></md-table-pagination>
            </md-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/mailingtemplate.js"></script>
@stop
