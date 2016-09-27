@extends( 'layout.default' )

@section( 'title' , 'Show Info' )

@section( 'content' )

<div layout="column" layout-padding ng-controller="ShowinfoController as info">
    <md-card>
        <md-card-content>
            <form name="recordForm" layout="column" novalidate>
                <md-input-container>
                    <label>Record ID</label>
                    <input type="text" name="recordId" ng-required="true" ng-model="info.recordId" />
                    <div class="hint">Enter EID or Email</div>
                    <div ng-messages="recordForm.recordId.$error">
                        <div ng-message="required">EID or Email is required.</div>
                    </div>
                </md-input-container>
                <div layout="row">
                    <md-button class="md-raised md-accent" ng-click="info.loadData( $event , recordForm )" flex-gt-xs="20" flex="100" layout="row">
                        <span flex>Search</span> <md-progress-circular ng-show="info.isLoading" md-mode="indeterminate" md-diameter="24"></md-progress-circular>
                    </md-button>
                </div>
            </form>
        </md-card-content>
    </md-card>

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

    <md-card ng-if="info.records.length > 0">
        <md-card-content>
            <form name="suppressionForm" layout="column" novalidate>
                <md-input-container>
                    <label>Suppression Reason</label>
                    <md-select name="suppressionReason" ng-model="info.selectedReason" ng-init="info.loadReasons()" ng-required="true">
                        <option ng-repeat="reason in info.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
                    </md-select>
                    <div ng-messages="suppressionForm.suppressionReason.$error">
                        <div ng-message="required">Suppression Reason is required.</div>
                    </div>
                </md-input-container>

                <div layout="row">
                    <md-button class="md-raised md-accent" flex-gt-xs="20" flex="100" ng-click="info.suppressRecord( $event , suppressionForm )">Suppress Record</md-button>
                </div>
            </form>
        </md-card-content>
    </md-card>

</div>
@stop

@section( 'pageIncludes' )
<script src="js/show-info.js"></script>
@stop
