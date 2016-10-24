
<div class="panel-body">
    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_id }">
        <select class="form-control" name="client_id" ng-model="feed.current.client_id">
            <option value="">Select Client</option>
            @foreach ( $clients as $client )
                <option value="{{ $client['id'] }}">{{ $client['name'] }}</option>
            @endforeach
        </select>
        <div class="help-block" ng-show="feed.formErrors.client_id">
            <div ng-repeat="error in feed.formErrors.client_id">
                <span ng-bind="error"></span>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.short_name }">
        <input type="text" class="form-control" id="short_name" value="" placeholder="Feed Short Name" required="required" ng-model="feed.current.short_name" />
        <div class="help-block" ng-show="feed.formErrors.short_name">
            <div ng-repeat="error in feed.formErrors.short_name">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.vertical_id }">
        <select class="form-control" name="vertical_id" ng-model="feed.current.vertical_id">
            <option value="">Feed Vertical</option>
            @foreach ( $clientTypes as $clientType )
                <option value="{{ $clientType['id'] }}">{{ $clientType['name'] }}</option>
            @endforeach
        </select>
        <div class="help-block" ng-show="feed.formErrors.vertical_id">
            <div ng-repeat="error in feed.formErrors.vertical_id">
                <span ng-bind="error"></span>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.frequency }">
        <select class="form-control" name="frequency" ng-model="feed.current.frequency">
            <option value="">Frequency</option>
                <option ng-repeat="option in feed.frequency">@{{ option }}</option>
        </select>
        <div class="help-block" ng-show="feed.formErrors.frequency">
            <div ng-repeat="error in feed.formErrors.frequency">
                <span ng-bind="error"></span>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.type_id }">
        <select class="form-control" name="type_id" ng-model="feed.current.type_id">
            <option value="">Feed Type</option>
            @foreach ( $feedTypes as $feedType )
                <option value="{{ $feedType['id'] }}">{{ $feedType['name'] }}</option>
            @endforeach
        </select>
        <div class="help-block" ng-show="feed.formErrors.type_id">
            <div ng-repeat="error in feed.formErrors.type_id">
                <span ng-bind="error"></span>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.country_id }">
        <label>Country</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <input type="hidden" ng-model="feed.current.country_id" />

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.country_id = 1 " ng-class="{ active : feed.current.country_id == 1 }">US</button>
            </div>

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.country_id = 2" ng-class="{ active : feed.current.country_id == 2 }">UK</button>
            </div>
        </div>
        <div class="help-block" ng-show="feed.formErrors.country_id">
            <div ng-repeat="error in feed.formErrors.country_id">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.party }">
        <label>Party</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <input type="hidden" ng-model="feed.current.party" />

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.party = 1 " ng-class="{ active : feed.current.party == 1 }">1<sup>st</sup></button>
            </div>

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.party = 2" ng-class="{ active : feed.current.party == 2 }">2<sup>nd</sup></button>
            </div>

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.party = 3" ng-class="{ active : feed.current.party == 3 }">3<sup>rd</sup></button>
            </div>
        </div>
        <div class="help-block" ng-show="feed.formErrors.party">
            <div ng-repeat="error in feed.formErrors.party">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.status }">
        <label>Status</label>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <input type="hidden" ng-model="feed.current.status" />

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.status = 'Active'" ng-class="{ active : feed.current.status == 'Active' }">Active</button>
            </div>

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.status = 'Paused'" ng-class="{ active : feed.current.status == 'Paused' }">Paused</button>
            </div>

            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" ng-click="feed.current.status = 'Inactive'" ng-class="{ active : feed.current.status == 'Inactive' }">Inactive</button>
            </div>
        </div>
        <div class="help-block" ng-show="feed.formErrors.status">
            <div ng-repeat="error in feed.formErrors.status">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.source_url }">
        <input type="text" class="form-control" id="source_url" value="" placeholder="Source URL" required="required" ng-model="feed.current.source_url" />
        <div class="help-block" ng-show="feed.formErrors.source_url">
            <div ng-repeat="error in feed.formErrors.source_url">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

</div>