<input name="_token" type="hidden" ng-init="feed.current._token = '{{ csrf_token() }}'" ng-model="feed.current._token" />

<div class="panel-body form-horizontal">
    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_id }">
        <label class="col-sm-2 control-label">Client</label>
        <div class="col-sm-10">
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
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.name }">
        <label class="col-sm-2 control-label">Feed Name</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="name" value="" placeholder="Feed Name" required="required" ng-model="feed.current.name" />
        <div class="help-block" ng-show="feed.formErrors.name">
            <div ng-repeat="error in feed.formErrors.name">
                <div ng-bind="error"></div>
            </div>
        </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.short_name }">
        <label class="col-sm-2 control-label">Feed Short Name</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="short_name" value="" placeholder="Feed Short Name" required="required" ng-model="feed.current.short_name" />
        <div class="help-block" ng-show="feed.formErrors.short_name">
            <div ng-repeat="error in feed.formErrors.short_name">
                <div ng-bind="error"></div>
            </div>
        </div>
        </div>
    </div>
    @if ( Sentinel::inRole( 'fleet-admiral' ) )
    <div class="form-group">
            <label class="col-sm-2 control-label">Feed FTP Host <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Due to security concerns, all new feeds must use the SFTP server.  There will be a separate process to move existing feeds files to the new server while migrating">lock</md-icon></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" disabled id="host_ip" value="sftp-01.mtroute.com"  ng-model="feed.current.host_ip" />
            </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Feed FTP Password</label>
        <div class="col-sm-10">
            <input type="text" disabled class="form-control" id="pass" value=""  ng-model="feed.current.password" />
        </div>
    </div>
    @endif
    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.vertical_id }">
        <label class="col-sm-2 control-label">Feed Vertical</label>
        <div class="col-sm-10">
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
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.frequency }">
        <label class="col-sm-2 control-label">Frequency</label>
        <div class="col-sm-10">
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
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.type_id }">
        <label class="col-sm-2 control-label">Feed Type</label>
        <div class="col-sm-10">
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
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.country_id }">
        <label class="col-sm-2 control-label">Country</label>
        <div class="col-sm-10">
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
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.party }">
        <label class="col-sm-2 control-label">Party</label>
        <div class="col-sm-10">
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
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.status }">
        <label class="col-sm-2 control-label">Status</label>
        <div class="col-sm-10">
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
        </div>
        <div class="help-block" ng-show="feed.formErrors.status">
            <div ng-repeat="error in feed.formErrors.status">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.source_url }">
        <label class="col-sm-2 control-label">Source URL</label>
        <div class="col-sm-10">
        <input type="text" class="form-control" id="source_url" value="" placeholder="Source URL" required="required" ng-model="feed.current.source_url" />
        <div class="help-block" ng-show="feed.formErrors.source_url">
            <div ng-repeat="error in feed.formErrors.source_url">
                <div ng-bind="error"></div>
            </div>
        </div>
        </div>
    </div>

</div>
