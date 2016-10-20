
@extends( 'bootstrap.layout.default' )

@section( 'title' , 'ESP Accounts' )

@section( 'angular-controller' , 'ng-controller="espController as esp"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('esp.add'))
        <li><a ng-click="esp.viewAdd()">Add ESP Account</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="esp.loadAccounts()">
    <md-table-container>
        <table md-table>
            <thead md-head>
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext"></th>
                    <th md-column class="md-table-header-override-whitetext">ID</th>
                    <th md-column class="md-table-header-override-whitetext">Name</th>
                    <th md-column class="md-table-header-override-whitetext">Email ID Field</th>
                    <th md-column class="md-table-header-override-whitetext">Email Address Field</th>
                    <th md-column class="md-table-header-override-whitetext">Updated</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in esp.accounts track by $index">
                    <td md-cell>
                        <div layout="row" layout-align="center center">
                            <md-button class="md-icon-button" ng-href="@{{ '/esp/edit/' + record.id }}" aria-label="Edit" target="_self">
                                <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                                <md-tooltip md-direction="bottom">Edit</md-tooltip>
                            </md-button>
                        </div>
                    </td>
                    <td md-cell>@{{ record.id }}</td>
                    <td md-cell>@{{ record.name }}</td>
                    <td md-cell>@{{ record.field_options.email_id_field }}</td>
                    <td md-cell>@{{ record.field_options.email_address_field }}</td>
                    <td md-cell>@{{ record.updated_at }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <md-content class="md-mt2-zeta-theme md-hue-2">
                            <md-table-pagination md-limit="esp.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="esp.currentPage" md-total="@{{esp.accountTotal}}" md-on-paginate="esp.loadAccounts" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/esp/EspController.js',
                'resources/assets/js/bootstrap/esp/EspService.js'],'js','pageLevel') ?>
