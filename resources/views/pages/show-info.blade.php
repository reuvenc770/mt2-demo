@extends( 'layout.default' )

@section( 'title' , 'Show Info' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Search Record Information</h1></div>
</div>

<div ng-controller="ShowinfoController as info">
    <div class="well">
        <form>
            <div class="form-group">
                <label for="eid">Record ID</label>
                <input type="text" class="form-control" id="eid" required="required" placeholder="Please enter EID or email" ng-model="info.recordId" />
            </div>

            <md-button class="md-primary" ng-click="info.loadData( $event )" layout="row"><span flex>Search</span><md-progress-circular ng-show="info.isLoading" md-mode="indeterminate" md-diameter="24"></md-progress-circular></md-button>
        </form>
    </div>

    <div id="mtTableContainer" class="table-responsive" ng-if="info.isLoaded">
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

    <div class="well" ng-if="info.isLoaded">
        <h3>Add to Suppression</h3>
        <form>
            <div class="form-group">
                <label for="suppressionReason">Reason</label>

                <select class="form-control" ng-model="info.selectedReason" ng-init="info.loadReasons()">
                    <option value="">Please Choose a Suppression Reason</option>
                    <option ng-repeat="reason in info.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
                </select>
            </div>

            <button type="submit" class="btn btn-danger btn-lg" ng-click="info.suppressRecord( $event )">Suppress Record</button>
        </form>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/show-info.js"></script>
@stop
