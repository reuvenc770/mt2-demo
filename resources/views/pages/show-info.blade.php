@extends( 'layout.default' )

@section( 'title' , 'Show Info' )

@section( 'content' )

<div layout="column" layout-padding ng-controller="ShowinfoController as info">
    <div class="well">
        <form name="recordForm" novalidate>
            <div class="form-group" ng-class="{ 'has-error' : ( recordForm.recordId.$touched && recordForm.recordId.$error.required ) }">
                <label for="eid">Record ID</label>
                <input name="recordId" type="text" class="form-control" id="eid" required placeholder="Please enter EID or email" ng-model="info.recordId" />
                <div ng-show="recordForm.recordId.$touched">
                    <span class="help-block" ng-show="recordForm.recordId.$error.required">EID or Email is required</span>
                </div>
            </div>

            <md-button class="md-primary" ng-click="info.loadData( $event , recordForm )" layout="row"><span flex>Search</span><md-progress-circular ng-show="info.isLoading" md-mode="indeterminate" md-diameter="24"></md-progress-circular></md-button>
        </form>
    </div>

    <md-table-container ng-if="info.records.length > 0">
        <table md-table>
            <thead md-head>
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
                    <th md-column class="md-table-header-override-whitetext">Network</th>
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
                    <td md-cell>@{{ record.eid }}</td>
                    <td md-cell>@{{ record.email_addr }}</td>
                    <td md-cell>@{{ record.first_name }}</td>
                    <td md-cell>@{{ record.last_name }}</td>
                    <td md-cell>@{{ record.address }}</td>
                    <td md-cell>@{{ record.source_url }}</td>
                    <td md-cell>@{{ record.ip }}</td>
                    <td md-cell>@{{ record.date }}</td>
                    <td md-cell>@{{ record.birthdate }}</td>
                    <td md-cell>@{{ record.gender }}</td>
                    <td md-cell>@{{ record.network }}</td>
                    <td md-cell>@{{ record.action }}</td>
                    <td md-cell>@{{ record.action_date }}</td>
                    <td md-cell>@{{ record.subscribe_datetime }}</td>
                    <td md-cell>@{{ record.status }}</td>
                    <td md-cell>@{{ record.removal_date }}</td>
                    <td md-cell>@{{ record.suppressed ? 'Suppressed' : '' }}</td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

    <h2 class="text-center" ng-if="info.suppression.length > 0">Suppressions</h2>
    <md-table-container ng-if="info.suppression.length > 0">
        <table md-table>
            <thead md-head>
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext">Email Address</th>
                    <th md-column class="md-table-header-override-whitetext">Esp Account</th>
                    <th md-column class="md-table-header-override-whitetext">Campaign Name</th>
                    <th md-column class="md-table-header-override-whitetext">Reason</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in info.suppression">
                    <td md-cell>@{{ record.email_addr }}</td>
                    <td md-cell>@{{ record.espAccountName }}</td>
                    <td md-cell>@{{ record.campaignName }}</td>
                    <td md-cell>@{{ record.suppressionReasonDetails }}</td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

    <div class="well" ng-if="info.records.length > 0">
        <h3>Add to Suppression</h3>
        <form name="suppressionForm" novalidate>
            <div class="form-group" ng-class="{ 'has-error' : ( suppressionForm.suppressionReason.$touched && suppressionForm.suppressionReason.$error.required ) }">
                <label for="suppressionReason">Reason</label>

                <select name="suppressionReason" class="form-control" ng-model="info.selectedReason" ng-init="info.loadReasons()" required>
                    <option value="">Please Choose a Suppression Reason</option>
                    <option ng-repeat="reason in info.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
                </select>

                <div ng-show="suppressionForm.suppressionReason.$touched">
                    <span class="help-block" ng-show="suppressionForm.suppressionReason.$error.required">Suppression Reason is required</span>
                </div>
            </div>

            <button type="submit" class="btn btn-danger btn-lg" ng-click="info.suppressRecord( $event , suppressionForm )">Suppress Record</button>
        </form>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/show-info.js"></script>
@stop
