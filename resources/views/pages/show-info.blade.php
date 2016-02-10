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
                <label for="eid">EID</label>
                <input type="text" class="form-control" id="eid" required="required" placeholder="Please enter EID" ng-model="info.recordId" />
            </div>

            <button type="submit" class="btn btn-success btn-md" ng-click="info.loadMockData( $event )">Search</button>
        </form>
    </div>

    <ul class="list-group" ng-if="info.isLoaded">
        <li class="list-group-item" ng-if="info.record.email">
            <span class="badge">Email</span> <span ng-bind="info.record.email"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.firstname">
            <span class="badge">First Name</span> <span ng-bind="info.record.firstname"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.lastname">
            <span class="badge">Last Name</span> <span ng-bind="info.record.lastname"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.address">
            <span class="badge">Address</span> <span ng-bind="info.record.address"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.source">
            <span class="badge">Source</span> <span ng-bind="info.record.source"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.ip">
            <span class="badge">IP</span> <span ng-bind="info.record.ip"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.date">
            <span class="badge">Date</span> <span ng-bind="info.record.date"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.birthday">
            <span class="badge">Birthday</span> <span ng-bind="info.record.birthday"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.gender">
            <span class="badge">Gender</span> <span ng-bind="info.record.gender"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.listname">
            <span class="badge">List Name</span> <span ng-bind="info.record.listname"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.network">
            <span class="badge">Network</span> <span ng-bind="info.record.network"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.action">
            <span class="badge">Action</span> <span ng-bind="info.record.action"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.actiondate">
            <span class="badge">Action Date</span> <span ng-bind="info.record.actiondate"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.subscribedate">
            <span class="badge">Subscribe Date</span> <span ng-bind="info.record.subscribedate"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.status">
            <span class="badge">Status</span> <span ng-bind="info.record.status"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.archived">
            <span class="badge">Archived</span> <span ng-bind="info.record.archived"></span>
        </li>
        <li class="list-group-item" ng-if="info.record.removaldate">
            <span class="badge">Removal Date</span> <span ng-bind="info.record.removaldate"></span>
        </li>
    </ul>

    <div class="well" ng-if="info.isLoaded">
        <h3>Add to Suppression</h3>
        <form>
            <div class="form-group">
                <label for="suppressionReason">Reason</label>

                <select class="form-control" ng-model="info.selectedReason" ng-init="info.loadReasons()">
                    <option value="">Suppression Reason</option>
                    <option ng-repeat="reason in info.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
                </select>
            </div>

            <button type="submit" class="btn btn-danger btn-lg" ng-click="info.addToSuppression( $event )">Suppress Record</button>
        </form>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/show-info.js"></script>
@stop
