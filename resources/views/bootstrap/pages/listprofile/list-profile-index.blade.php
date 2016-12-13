@extends( 'bootstrap.layout.default' )

@section( 'title' , 'List Profile' )

@section( 'angular-controller' , 'ng-controller="ListProfileController as listProfile"')

@section( 'page-menu' )
    @if (Sentinel::hasAccess('listprofile.add'))
        <li><a href="/listprofile/create" target="_self">Add List Profile</a></li>
        @endif
        @stop

        @section( 'content' )

                <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#list_profile" aria-controls="list_profile" ng-click="listProfile.clearSelection()" role="tab" data-toggle="tab">3rd Party List Profiles</a></li>
            <li role="presentation" ><a href="#list_profile_first" aria-controls="list_profile" ng-click="listProfile.clearSelection()" role="tab" data-toggle="tab">1st Party List Profiles</a></li>
            <li role="presentation"><a href="#list_combines" aria-controls="list_combines" ng-click="listProfile.clearSelection()" role="tab" data-toggle="tab">List Combines</a></li>
            <li ng-show="listProfile.showCombine" class="pull-right"><button ng-click="listProfile.nameCombine()" class="btn btn-primary">Create List Combine</button></li>
        </ul>
        <!-- Tab panes -->
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="list_profile">
                <md-table-container ng-init="listProfile.loadListProfiles(3)">
                    <table md-table md-progress="listProfile.queryPromise">
                        <thead md-head class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column  mt2-table-header-center">
                                <md-icon style="margin-right: 8px;" md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Select 2 or more list profiles to create a list profile combine.">help</md-icon>
                            </th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                            <th md-column class="md-table-header-override-whitetext">Deliverable Range</th>
                            <th md-column class="md-table-header-override-whitetext">Opener Range</th>
                            <th md-column class="md-table-header-override-whitetext">Clicker Range</th>
                            <th md-column class="md-table-header-override-whitetext">Converter Range</th>
                            <th md-column class="md-table-header-override-whitetext">Pull Frequency</th>
                            <th md-column class="md-table-header-override-whitetext">Record Count</th>
                            <th md-column class="md-table-header-override-whitetext">Updated</th>
                            <th md-column class="md-table-header-override-whitetext">Generated</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="profile in listProfile.thirdPartyListProfiles track by $index">
                            <td md-cell class="mt2-table-btn-column" nowrap>
                                <md-checkbox  aria-label="Select" name="selectedRows" ng-checked="listProfile.isCreatingCombine(profile.id)" ng-click="listProfile.toggleRow(profile.id, profile.party)"> </md-checkbox>
                                <a ng-href="@{{ ( '/listprofile/edit/' + profile.id ) }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon></a>
                                @if (Sentinel::hasAccess('api.listprofile.copy'))
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-click="listProfile.copyListProfile($event, profile.id, profile.name)" aria-label="Copy" data-toggle="tooltip" data-placement="bottom" title="Copy">content_copy</md-icon>
                                @endif
                            </td>
                            <td md-cell ng-bind="profile.name" nowrap></td>
                            <td md-cell nowrap>@{{ ( profile.deliverable_start + ' to ' + profile.deliverable_end ) }}</td>
                            <td md-cell nowrap>@{{ ( profile.openers_start + ' to ' + profile.openers_end ) }} (@{{ ::( profile.open_count + 'x' ) }})</td>
                            <td md-cell nowrap>@{{ ( profile.clickers_start + ' to ' + profile.clickers_end ) }} (@{{ ::( profile.click_count + 'x' ) }})</td>
                            <td md-cell nowrap>@{{ ( profile.converters_start + ' to ' + profile.converters_end ) }} (@{{ ::( profile.conversion_count + 'x' ) }})</td>
                            <td md-cell ng-bind="profile.run_frequency" nowrap></td>
                            <td md-cell ng-bind="profile.total_count" nowrap></td>
                            <td md-cell ng-bind="app.formatDate( profile.updated_at )" nowrap></td>
                            <td md-cell ng-bind="profile.schedule.last_run" nowrap></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="10">
                                <md-content class="md-mt2-zeta-theme md-hue-2">
                                    <md-table-pagination md-limit="listProfile.paginationCount" md-limit-options="listProfile.paginationOptions" md-page="listProfile.currentPage" md-total="@{{listProfile.profileTotal}}" md-on-paginate="listProfile.loadListProfiles" md-page-select></md-table-pagination>
                                </md-content>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </md-table-container>
            </div>
            <div role="tabpanel" class="tab-pane" id="list_profile_first">
                <md-table-container>
                    <table md-table md-progress="listProfile.queryPromise">
                        <thead md-head class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                            <th md-column class="md-table-header-override-whitetext">Deliverable Range</th>
                            <th md-column class="md-table-header-override-whitetext">Opener Range</th>
                            <th md-column class="md-table-header-override-whitetext">Clicker Range</th>
                            <th md-column class="md-table-header-override-whitetext">Converter Range</th>
                            <th md-column class="md-table-header-override-whitetext">Pull Frequency</th>
                            <th md-column class="md-table-header-override-whitetext">Record Count</th>
                            <th md-column class="md-table-header-override-whitetext">Updated</th>
                            <th md-column class="md-table-header-override-whitetext">Generated</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="profile in listProfile.firstPartyListProfiles track by $index">
                            <td md-cell class="mt2-table-btn-column" nowrap>
                                <md-checkbox  aria-label="Select" name="selectedRows" ng-checked="listProfile.isCreatingCombine(profile.id)" ng-click="listProfile.toggleRow(profile.id)"> </md-checkbox>
                                <a ng-href="@{{ ( '/listprofile/edit/' + profile.id ) }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon></a>
                                @if (Sentinel::hasAccess('api.listprofile.copy'))
                                    <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-click="listProfile.copyListProfile($event, profile.id, profile.name)" aria-label="Copy" data-toggle="tooltip" data-placement="bottom" title="Copy">content_copy</md-icon>
                                @endif
                            </td>
                            <td md-cell ng-bind="profile.name" nowrap></td>
                            <td md-cell nowrap>@{{ ( profile.deliverable_start + ' to ' + profile.deliverable_end ) }}</td>
                            <td md-cell nowrap>@{{ ( profile.openers_start + ' to ' + profile.openers_end ) }} (@{{ ::( profile.open_count + 'x' ) }})</td>
                            <td md-cell nowrap>@{{ ( profile.clickers_start + ' to ' + profile.clickers_end ) }} (@{{ ::( profile.click_count + 'x' ) }})</td>
                            <td md-cell nowrap>@{{ ( profile.converters_start + ' to ' + profile.converters_end ) }} (@{{ ::( profile.conversion_count + 'x' ) }})</td>
                            <td md-cell ng-bind="profile.run_frequency" nowrap></td>
                            <td md-cell ng-bind="profile.total_count" nowrap></td>
                            <td md-cell ng-bind="app.formatDate( profile.updated_at )" nowrap></td>
                            <td md-cell ng-bind="profile.schedule.last_run" nowrap></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="10">
                                <md-content class="md-mt2-zeta-theme md-hue-2">
                                    <md-table-pagination md-limit="listProfile.paginationCount" md-limit-options="listProfile.paginationOptions" md-page="listProfile.currentPage" md-total="@{{listProfile.profileTotal}}" md-on-paginate="listProfile.loadListProfiles" md-page-select></md-table-pagination>
                                </md-content>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </md-table-container>
            </div>
            <div role="tabpanel" class="tab-pane" id="list_combines">
                <md-table-container>
                    <table md-table>
                        <thead md-head class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="mt2-table-btn-column"></th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                            <th md-column class="md-table-header-override-whitetext">Party</th>
                            <th md-column class="md-table-header-override-whitetext">List Profiles Used</th>
                        </tr>
                        </thead>

                        <tbody md-body>
                        <tr md-row ng-repeat="profile in listProfile.listCombines track by $index">
                            <td md-cell class="mt2-table-btn-column" style="width:80px;" nowrap>
                                <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-click="listProfile.exportCombine(profile.id)" data-toggle="tooltip" data-placement="bottom" title="Export List Combine">file_upload</md-icon>
                                <a ng-href="@{{ ( '/listprofile/combine/edit/' + profile.id ) }}" target="_self"><md-icon md-font-set="material-icons" class="mt2-icon-black" data-toggle="tooltip" data-placement="bottom" title="Edit List Combine">edit</md-icon></a>
                            </td>
                            <td md-cell ng-bind="profile.name" nowrap></td>
                            <td md-cell ng-bind="profile.party" ></td>
                            <td md-cell nowrap>
                                <span ng-repeat="listCombine in profile.list_profiles">
                                    @{{ listCombine.name }} @{{ !$last ? ',' : '' }}
                                </span>
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </md-table-container>
            </div>
        </div>
        <listprofile-combine-create combine-name="listProfile.combineName" combine-error="listProfile.formErrors.combineName"
                                    create-combine="listProfile.createCombine()"></listprofile-combine-create>
@stop

<?php
Assets::add( [
        'resources/assets/js/bootstrap/listprofile/ListProfileController.js' ,
        'resources/assets/js/bootstrap/listprofile/ListProfileApiService.js' ,
        'resources/assets/js/bootstrap/listprofile/ListProfileCombineCreationModalDirective.js'
] , 'js' , 'pageLevel' );
?>
