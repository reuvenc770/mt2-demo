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
                    <th md-column class="md-table-header-override-whitetext">Client</th>
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
                    <td md-cell nowrap>@{{ record.date }}</td>
                    <td md-cell nowrap>@{{ record.birthdate }}</td>
                    <td md-cell>@{{ record.gender }}</td>
                    <td md-cell>@{{ record.client }}</td>
                    <td md-cell>@{{ record.action }}</td>
                    <td md-cell nowrap>@{{ record.action_date }}</td>
                    <td md-cell nowrap>@{{ record.subscribe_datetime }}</td>
                    <td md-cell>@{{ record.status }}</td>
                    <td md-cell nowrap>@{{ record.removal_date }}</td>
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
