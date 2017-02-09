
@extends( 'layout.default' )

@section( 'title' , 'ESP Accounts' )
@section( 'angular-controller' , 'ng-controller="espController as esp"' )
@section( 'cacheTag' , 'Esp' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('esp.add'))
        <li><a ng-href="/esp/create" target="_self">Add ESP Account</a></li>
    @endif


@stop

@section( 'content' )
    <div class="alert alert-info" role="alert"> <strong>Heads up!</strong> Any information uploaded here will likely be replaced by a later API call,  please do not re-upload previously collected campaigns.</div>
<div ng-init="esp.loadAccounts()">
    <md-table-container>
        <table md-table>
            <thead md-head class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="mt2-table-btn-column  mt2-table-header-center">
                        <md-icon style="margin-right: 8px;" md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Before uploading a CSV file, make sure the ESP account's field mapping matches the column order in the CSV file.">help</md-icon>
                    </th>
                    <th md-column class="md-table-header-override-whitetext">ID</th>
                    <th md-column class="md-table-header-override-whitetext">Name</th>
                    <th md-column class="md-table-header-override-whitetext">Nickname</th>
                    <th md-column class="md-table-header-override-whitetext">Open Email ID Field</th>
                    <th md-column class="md-table-header-override-whitetext">Open Email Address Field</th>
                    <th md-column class="md-table-header-override-whitetext">Link Email ID Field</th>
                    <th md-column class="md-table-header-override-whitetext">Link Email Address Field</th>
                    <th md-column class="md-table-header-override-whitetext">Updated</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in esp.accounts track by $index">
                    <td md-cell class="mt2-table-btn-column">
                        <div layout="row" layout-align="center center">
                            <a ng-href="@{{ '/esp/edit/' + record.id }}" aria-label="Edit" target="_self"
                                        data-toggle="tooltip" data-placement="bottom" title="Edit">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                            </a>
                            <a ng-href="@{{ '/esp/mapping/' + record.id }}" aria-label="Edit" target="_self"
                               data-toggle="tooltip" data-placement="bottom" title="Field Mapping">
                                <md-icon md-font-set="material-icons" class="mt2-icon-black">assignment</md-icon>
                            </a>
                            @if (Sentinel::hasAccess('api.esp.mappings.process'))
                                <span flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'csvuploads' , '_token' : '{{ csrf_token() }}' } }"
                                    flow-files-submitted="$flow.upload()"
                                    flow-file-success="esp.fileUploaded($file, record.name); $flow.cancel()" flow-btn>
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" style="cursor:pointer;" data-toggle="tooltip" data-placement="bottom" title="Upload File">file_upload</md-icon>
                                    <input type="file" style="visibility: hidden; position: absolute;"/>
                                </span>
                            @endif
                        </div>
                    </td>
                    <td md-cell ng-bind="record.id"></td>
                    <td md-cell ng-bind="record.name"></td>
                    <td md-cell ng-bind="record.nickname"></td>
                    <td md-cell ng-bind="record.field_options.open_email_id_field"></td>
                    <td md-cell ng-bind="record.field_options.open_email_address_field"></td>
                    <td md-cell ng-bind="record.field_options.email_id_field"></td>
                    <td md-cell ng-bind="record.field_options.email_address_field"></td>
                    <td md-cell ng-bind="::app.formatDate( record.updated_at )" nowrap></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9">
                        <md-content class="md-mt2-zeta-theme">
                            <md-table-pagination md-limit="esp.paginationCount" md-limit-options="esp.paginationOptions" md-page="esp.currentPage" md-total="@{{esp.accountTotal}}" md-on-paginate="esp.loadAccounts" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/esp/EspController.js',
                'resources/assets/js/esp/EspService.js'],'js','pageLevel') ?>
