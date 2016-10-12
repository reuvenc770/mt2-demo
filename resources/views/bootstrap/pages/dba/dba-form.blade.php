<input name="_token" type="hidden" value="{{ csrf_token() }}">
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.dba_name }">
    <input placeholder="DBA Name" value="" class="form-control" ng-model="dba.currentAccount.dba_name"
           required="required" name="dba_name" type="text">
    <div class="help-block" ng-show="dba.formErrors.dba_name">
        <div ng-repeat="error in dba.formErrors.dba_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.registrant_name }">
    <input placeholder="Registrant Name" value="" class="form-control" ng-model="dba.currentAccount.registrant_name"
           required="required" name="registrant_name" type="text">
    <div class="help-block" ng-show="dba.formErrors.registrant_name">
        <div ng-repeat="error in dba.formErrors.registrant_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.address }">
    <input placeholder="Address" value="" class="form-control" ng-model="dba.currentAccount.address" required="required"
           name="address" type="text">
    <div class="help-block" ng-show="dba.formErrors.address">
        <div ng-repeat="error in dba.formErrors.address">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.address_2 }">
    <input placeholder="Address Line 2" value="" class="form-control" ng-model="dba.currentAccount.address_2"
           required="required" name="address_2" type="text">
    <div class="help-block" ng-show="dba.formErrors.address_2">
        <div ng-repeat="error in dba.formErrors.address_2">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.city }">
            <input placeholder="City" value="" class="form-control" ng-model="dba.currentAccount.city"
                   required="required" name="city" type="text">
            <div class="help-block" ng-show="dba.formErrors.city">
                <div ng-repeat="error in dba.formErrors.city">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.state }">
            <select ng-model="dba.currentAccount.state" name="state" class="form-control">
                <option value="">Pick A State</option>
                @foreach ( $states as $state )
                    <option value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                @endforeach
            </select>
            <div class="help-block" ng-show="dba.formErrors.address">
                <div ng-repeat="error in dba.formErrors.address">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.zip }">
            <input placeholder="Zip Code" value="" class="form-control" ng-model="dba.currentAccount.zip"
                   required="required" name="zip" type="text">
            <div class="help-block" ng-show="dba.formErrors.zip">
                <div ng-repeat="error in dba.formErrors.zip">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.dba_email }">
    <input placeholder="Contact Email" value="" class="form-control" ng-model="dba.currentAccount.dba_email"
           required="required" name="dba_email" type="text">
    <div class="help-block" ng-show="dba.formErrors.dba_email">
        <div ng-repeat="error in dba.formErrors.dba_email">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.entity_name }">
    <input placeholder="Entity Name" value="" class="form-control" ng-model="dba.currentAccount.entity_name"
           required="required" name="entity_name" type="text">
    <div class="help-block" ng-show="dba.formErrors.entity_name">
        <div ng-repeat="error in dba.formErrors.entity_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.phone }">
    <input placeholder="Phone Number" value="" class="form-control" ng-model="dba.currentAccount.phone"
           required="required" name="phone" type="text">
    <div class="help-block" ng-show="dba.formErrors.phone">
        <div ng-repeat="error in dba.formErrors.phone">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title">P.O. Boxes</div>
    </div>
    <div class="panel-body">
        <fieldset>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.sub }">
                <input placeholder="Sub #" value="" class="form-control" ng-model="dba.po_box.sub" required="required"
                       name="po_box_sub" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.sub">
                    <div ng-repeat="error in dba.formErrors.po_box.sub">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.address }">
                <input placeholder="Address" value="" class="form-control" ng-model="dba.po_box.address"
                       required="required" name="po_box_address" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.address">
                    <div ng-repeat="error in dba.formErrors.po_box.address">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.address_2 }">
                <input placeholder="Address Line 2" value="" class="form-control" ng-model="dba.po_box.address_2"
                       required="required" name="po_box_address_2" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.address_2">
                    <div ng-repeat="error in dba.formErrors.po_box.address_2">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.city }">
                        <input placeholder="City" value="" class="form-control" ng-model="dba.po_box.city"
                               required="required" name="po_box_city" type="text">
                        <div class="help-block" ng-show="dba.formErrors.po_box.city">
                            <div ng-repeat="error in dba.formErrors.po_box.city">
                                <span ng-bind="error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.state }">
                        <select ng-model="dba.po_box.state" name="po_box_state" class="form-control">
                            <option value="">Pick A State</option>
                            @foreach ( $states as $state )
                                <option value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <div class="help-block" ng-show="dba.formErrors.po_box.state">
                            <div ng-repeat="error in dba.formErrors.po_box.state">
                                <span ng-bind="error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.zip }">
                        <input placeholder="Zip Code" value="" class="form-control" ng-model="dba.po_box.zip"
                               required="required" name="po_box_zip" type="text">
                        <div class="help-block" ng-show="dba.formErrors.po_box.zip">
                            <div ng-repeat="error in dba.formErrors.po_box.zip">
                                <span ng-bind="error"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.phone }">
                <input placeholder="Phone Number" value="" class="form-control" ng-model="dba.po_box.phone"
                       required="required" name="po_box_phone" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.phone">
                    <div ng-repeat="error in dba.formErrors.po_box.phone">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.brands }">
                <input placeholder="Comma Delimited Brand List" value="" class="form-control"
                       ng-model="dba.po_box.brands" required="required" name="po_box_brands" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.brands">
                    <div ng-repeat="error in dba.formErrors.po_box.brands">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </div>
        </fieldset>
        <button class="btn btn-success btn-block" ng-click="dba.addPOBox()">
            <span ng-show="!dba.editingPOBox">Create </span>
            <span ng-show="dba.editingPOBox">Update </span>P.O. Box
        </button>
    </div>
    <div class="panel-footer" ng-show="dba.poBoxHolder.length > 0">
        <div class="thumbnail" ng-repeat="(key, value) in dba.poBoxHolder track by $index">
            <div class="caption clearfix">
                <strong>PO Box:</strong> <span ng-if="value.sub">(Sub# @{{value.sub}})</span> @{{value.address}}
                @{{value.address_2}} @{{value.city}} @{{value.state}} @{{value.zip}} <span
                        ng-if="value.phone"><b>tel: </b>@{{value.phone}}</span>
                <span ng-if="value.brands"><b>Brands: </b>@{{value.brands}}</span>
                <div class="pull-right">
                    <a href="#" class="btn btn-success btn-small" ng-click="dba.editPOBox(key)" role="button">Edit</a>
                    <a href="#" class="btn btn-danger btn-small" ng-click="dba.removePOBox(key)"
                       role="button">Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.notes }">
        <textarea placeholder="Notes" value="" class="form-control" ng-model="dba.currentAccount.notes"
                  name="notes"></textarea>
    <div class="help-block" ng-show="dba.formErrors.notes">
        <div ng-repeat="error in dba.formErrors.notes">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
