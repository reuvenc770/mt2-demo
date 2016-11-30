@extends( 'bootstrap.layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Show Info' )

@section( 'content' )

<div layout="column" ng-controller="ShowinfoController as info">
    <div class="panel mt2-theme-panel">
        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : info.formErrors.recordId }">
                <label for="eid">Record ID</label>
                <textarea name="recordId" class="form-control" id="eid" required placeholder="Please enter EID or email" ng-model="info.recordId"></textarea>
                <div class="help-block" ng-show="info.formErrors.recordId">
                    <div ng-repeat="error in info.formErrors.recordId">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-disabled="info.isLoading" type="button" ng-click="info.loadData()" value="Search">
                </div>
            </div>
        </div>
    </div>

    <md-table-container ng-if="info.records.length > 0">
        <table md-table>
            <thead md-head class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext" md-numeric>EID</th>
                    <th md-column class="md-table-header-override-whitetext">Email</th>
                    <th md-column class="md-table-header-override-whitetext">First Name</th>
                    <th md-column class="md-table-header-override-whitetext">Last Name</th>
                    <th md-column class="md-table-header-override-whitetext">Address</th>
                    <th md-column class="md-table-header-override-whitetext">Source</th>
                    <th md-column class="md-table-header-override-whitetext">IP</th>
                    <th md-column class="md-table-header-override-whitetext">Date</th>
                    <th md-column class="md-table-header-override-whitetext">Birth Date</th>
                    <th md-column class="md-table-header-override-whitetext">Gender</th>
                    <th md-column class="md-table-header-override-whitetext">Feed</th>
                    <th md-column class="md-table-header-override-whitetext">Action</th>
                    <th md-column class="md-table-header-override-whitetext">Action Date</th>
                    <th md-column class="md-table-header-override-whitetext">Subscribe Date</th>
                    <th md-column class="md-table-header-override-whitetext">Status</th>
                    <th md-column class="md-table-header-override-whitetext">Removal Date</th>
                    <th md-column class="md-table-header-override-whitetext">Suppressed</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in info.records">
                    <td md-cell ng-bind="record.eid"></td>
                    <td md-cell ng-bind="record.email_address"></td>
                    <td md-cell ng-bind="record.first_name"></td>
                    <td md-cell ng-bind="record.last_name"></td>
                    <td md-cell ng-bind="record.address" nowrap></td>
                    <td md-cell ng-bind="record.source_url"></td>
                    <td md-cell ng-bind="record.ip"></td>
                    <td md-cell ng-bind="record.date" nowrap></td>
                    <td md-cell nowrap>@{{ record.birthdate == '0000-00-00' ? '' : record.birthdate }}</td>
                    <td md-cell ng-bind="record.gender"></td>
                    <td md-cell ng-bind="record.feed_name"></td>
                    <td md-cell ng-bind="record.action"></td>
                    <td md-cell ng-bind="record.action_date" nowrap></td>
                    <td md-cell ng-bind="record.subscribe_datetime" nowrap></td>
                    <td md-cell ng-bind="record.status"></td>
                    <td md-cell nowrap>@{{ record.removal_date == '0000-00-00 00:00:00' ? '' : record.removal_date}}</td>
                    <td md-cell ng-bind="record.suppressed ? 'Suppressed' : ''"></td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

    <h2 class="text-center" ng-if="info.suppression.length > 0">Suppressions</h2>
    <md-table-container ng-if="info.suppression.length > 0">
        <table md-table>
            <thead md-head class="mt2-theme-thead">
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext">Email Address</th>
                    <th md-column class="md-table-header-override-whitetext">Esp Account</th>
                    <th md-column class="md-table-header-override-whitetext">Campaign Name</th>
                    <th md-column class="md-table-header-override-whitetext">Reason</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in info.suppression">
                    <td md-cell ng-bind="record.email_addr"></td>
                    <td md-cell ng-bind="record.espAccountName"></td>
                    <td md-cell ng-bind="record.campaignName"></td>
                    <td md-cell ng-bind="record.suppressionReasonDetails"></td>
                </tr>
            </tbody>
        </table>
    </md-table-container>
    <br/>
    <div class="panel mt2-theme-panel" ng-if="info.records.length > 0">
        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : info.formErrors.selectedReason }">
                <select ng-model="info.selectedReason" name="selectedReason" class="form-control" required="required" ng-init="info.loadReasons()">
                    <option value="">Suppression Reason</option>
                    <option ng-repeat="reason in info.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
                </select>
                <div class="help-block" ng-show="info.formErrors.selectedReason">
                    <div ng-repeat="error in info.formErrors.selectedReason">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-disabled="info.isSuppressing" type="button" ng-click="info.suppressRecord()" value="Suppress Record">
                </div>
            </div>
        </div>
    </div>

</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/pages/ShowinfoController.js',
                'resources/assets/js/bootstrap/pages/ShowinfoApiService.js'],'js','pageLevel') ?>
