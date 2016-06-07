@extends( 'layout.default' )

@section( 'title' , 'Show Info' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Search Record Information</h1></div>
</div>

<div ng-controller="ShowinfoController as info">
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

    <div id="mtTableContainer" class="table-responsive" ng-if="info.records.length > 0">
        <table class="table table-striped table-bordered tabel-hover text-center">
            <thead>
                <th class="text-center">EID</th>
                <th class="text-center">Email</th>
                <th class="text-center">First Name</th>
                <th class="text-center">Last Name</th>
                <th class="text-center">Address</th>
                <th class="text-center">Source</th>
                <th class="text-center">IP</th>
                <th class="text-center">Date</th>
                <th class="text-center">Birth Date</th>
                <th class="text-center">Gender</th>
                <th class="text-center">Network</th>
                <th class="text-center">Action</th>
                <th class="text-center">Action Date</th>
                <th class="text-center">Subscribe Date</th>
                <th class="text-center">Status</th>
                <th class="text-center">Removal Date</th>
                <th class="text-center">Suppressed</th>
            </thead>

            <tbody>
                <tr ng-repeat="record in info.records">
                    <td>@{{ record.eid }}</td>
                    <td>@{{ record.email_addr }}</td>
                    <td>@{{ record.first_name }}</td>
                    <td>@{{ record.last_name }}</td>
                    <td>@{{ record.address }}</td>
                    <td>@{{ record.source_url }}</td>
                    <td>@{{ record.ip }}</td>
                    <td>@{{ record.date }}</td>
                    <td>@{{ record.birthdate }}</td>
                    <td>@{{ record.gender }}</td>
                    <td>@{{ record.network }}</td>
                    <td>@{{ record.action }}</td>
                    <td>@{{ record.action_date }}</td>
                    <td>@{{ record.subscribe_datetime }}</td>
                    <td>@{{ record.status }}</td>
                    <td>@{{ record.removal_date }}</td>
                    <td>@{{ record.suppressed ? 'Suppressed' : '' }}</td>
                </tr>

            </tbody>
        </table>
    </div>
    <h2 class="text-center" ng-if="info.suppression.length > 0">Suppressions</h2>
    <div id="mtTableContainer" class="table-responsive" ng-if="info.suppression.length > 0">
        <table class="table table-striped table-bordered tabel-hover text-center">
            <thead>
            <th class="text-center">Email Address</th>
            <th class="text-center">Esp Account</th>
            <th class="text-center">Campaign Name</th>
            <th class="text-center">Reason</th>
            </thead>

            <tbody>
            <tr ng-repeat="record in info.suppression">
                <td>@{{ record.email_addr }}</td>
                <td>@{{ record.espAccountName }}</td>
                <td>@{{ record.campaignName }}</td>
                <td>@{{ record.suppressionReasonDetails }}</td>
            </tr>

            </tbody>
        </table>
    </div>

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
