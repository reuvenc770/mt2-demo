<div class="form-horizontal">
<input name="_token" type="hidden" ng-init="client.current._token = '{{ csrf_token() }}' " ng-model="client.current._token">
<div class="form-group" ng-class="{ 'has-error' : client.formErrors.name }">
    <label class="col-sm-2 control-label">Client Name</label>
    <div class="col-sm-10">
    <input placeholder="Client Name" value="" class="form-control" ng-model="client.current.name" required="required" name="name" type="text">
    <div class="help-block" ng-show="client.formErrors.name">
        <div ng-repeat="error in client.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : client.formErrors.address }">
    <label class="col-sm-2 control-label">Address</label>
    <div class="col-sm-10">
    <input placeholder="Address" value="" class="form-control" ng-model="client.current.address" required="required" name="address" type="text">
    <div class="help-block" ng-show="client.formErrors.address">
        <div ng-repeat="error in client.formErrors.address">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : client.formErrors.address2 }">
    <label class="col-sm-2 control-label">Address Line 2</label>
    <div class="col-sm-10">
    <input placeholder="Address Line 2" value="" class="form-control" ng-model="client.current.address2" name="address2" type="text">
    <div class="help-block" ng-show="client.formErrors.address2">
        <div ng-repeat="error in client.formErrors.address2">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
    <div class="row form-group">
        <label class="col-sm-2 control-label">City</label>
        <div class="col-sm-4" ng-class="{ 'has-error' : client.formErrors.city }">
            <input placeholder="City" value="" class="form-control" ng-model="client.current.city"
                   required="required" name="city" type="text">
            <div class="help-block" ng-show="client.formErrors.city">
                <div ng-repeat="error in client.formErrors.city">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-3" ng-class="{ 'has-error' : client.formErrors.state }">
            <select ng-model="client.current.state" name="state" class="form-control">
                <option value="">Pick A State</option>
                @foreach ( $states as $state )
                    <option value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                @endforeach
            </select>
            <div class="help-block" ng-show="client.formErrors.address">
                <div ng-repeat="error in client.formErrors.address">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-3" ng-class="{ 'has-error' : client.formErrors.zip }">
            <input placeholder="Zip Code" value="" class="form-control" ng-model="client.current.zip"
                   required="required" name="zip" type="text">
            <div class="help-block" ng-show="client.formErrors.zip">
                <div ng-repeat="error in client.formErrors.zip">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
<div class="form-group" ng-class="{ 'has-error' : client.formErrors.email_address }">
    <label class="col-sm-2 control-label">Email Address</label>
    <div class="col-sm-10">
    <input placeholder="Email Address" value="" class="form-control" ng-model="client.current.email_address" required="required" name="email_address" type="text">
    <div class="help-block" ng-show="client.formErrors.email_address">
        <div ng-repeat="error in client.formErrors.email_address">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : client.formErrors.phone }">
    <label class="col-sm-2 control-label">Phone Number</label>
    <div class="col-sm-10">
    <input placeholder="Phone" value="" class="form-control" ng-model="client.current.phone" required="required" name="phone" type="text">
    <div class="help-block" ng-show="client.formErrors.phone">
        <div ng-repeat="error in client.formErrors.phone">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : client.formErrors.status }">
    <label class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10">
    <div class="btn-group btn-group-justified" role="group" aria-label="...">
        <input type="hidden" ng-model="client.current.status" />

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="client.current.status = 'Active'" ng-class="{ active : client.current.status == 'Active' }">Active</button>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="client.current.status = 'Paused'" ng-class="{ active : client.current.status == 'Paused' }">Paused</button>
        </div>

        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default" ng-click="client.current.status = 'Inactive'" ng-class="{ active : client.current.status == 'Inactive' }">Inactive</button>
        </div>
    </div>
    <div class="help-block" ng-show="client.formErrors.status">
        <div ng-repeat="error in client.formErrors.status">
            <div ng-bind="error"></div>
        </div>
    </div>
    </div>
</div>
</div>