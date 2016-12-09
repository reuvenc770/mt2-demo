<div class="form-horizontal">
<input name="_token" type="hidden" value="{{ csrf_token() }}">
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.dba_name }">
    <label class="col-sm-2 control-label">DBA Name</label>
    <div class="col-sm-10">
    <input placeholder="DBA Name" value="" class="form-control" ng-model="dba.currentAccount.dba_name"
           required="required" name="dba_name" type="text">
    <div class="help-block" ng-show="dba.formErrors.dba_name">
        <div ng-repeat="error in dba.formErrors.dba_name">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.registrant_name }">
    <label class="col-sm-2 control-label">Registrant Name</label>
    <div class="col-sm-10">
    <input placeholder="Registrant Name" value="" class="form-control" ng-model="dba.currentAccount.registrant_name"
           required="required" name="registrant_name" type="text">
    <div class="help-block" ng-show="dba.formErrors.registrant_name">
        <div ng-repeat="error in dba.formErrors.registrant_name">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.address }">
    <label class="col-sm-2 control-label">Address</label>
    <div class="col-sm-10">
    <input placeholder="Address" value="" class="form-control" ng-model="dba.currentAccount.address" required="required"
           name="address" type="text">
    <div class="help-block" ng-show="dba.formErrors.address">
        <div ng-repeat="error in dba.formErrors.address">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.address_2 }">
    <label class="col-sm-2 control-label">Address Line 2</label>
    <div class="col-sm-10">
    <input placeholder="Address Line 2" value="" class="form-control" ng-model="dba.currentAccount.address_2"
           required="required" name="address_2" type="text">
    <div class="help-block" ng-show="dba.formErrors.address_2">
        <div ng-repeat="error in dba.formErrors.address_2">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="row form-group">
    <label class="col-sm-2 control-label">City</label>
        <div class="col-sm-4" ng-class="{ 'has-error' : dba.formErrors.city }">
            <input placeholder="City" value="" class="form-control" ng-model="dba.currentAccount.city"
                   required="required" name="city" type="text">
            <div class="help-block" ng-show="dba.formErrors.city">
                <div ng-repeat="error in dba.formErrors.city">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-3" ng-class="{ 'has-error' : dba.formErrors.state }">
            <select ng-model="dba.currentAccount.state" name="state" class="form-control">
                <option value="">Pick A State</option>
                @foreach ( $states as $state )
                    <option value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                @endforeach
            </select>
            <div class="help-block" ng-show="dba.formErrors.state">
                <div ng-repeat="error in dba.formErrors.state">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="col-sm-3" ng-class="{ 'has-error' : dba.formErrors.zip }">
            <input placeholder="Zip Code" value="" class="form-control" ng-model="dba.currentAccount.zip"
                   required="required" name="zip" type="text">
            <div class="help-block" ng-show="dba.formErrors.zip">
                <div ng-repeat="error in dba.formErrors.zip">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.dba_email }">
    <label class="col-sm-2 control-label">Contact Email</label>
    <div class="col-sm-10">
    <input placeholder="Contact Email" value="" class="form-control" ng-model="dba.currentAccount.dba_email"
           required="required" name="dba_email" type="text">
    <div class="help-block" ng-show="dba.formErrors.dba_email">
        <div ng-repeat="error in dba.formErrors.dba_email">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.entity_name }">
    <label class="col-sm-2 control-label">Entity Name</label>
    <div class="col-sm-10">
    <input placeholder="Entity Name" value="" class="form-control" ng-model="dba.currentAccount.entity_name"
           required="required" name="entity_name" type="text">
    <div class="help-block" ng-show="dba.formErrors.entity_name">
        <div ng-repeat="error in dba.formErrors.entity_name">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.phone }">
    <label class="col-sm-2 control-label">Phone Number</label>
    <div class="col-sm-10">
    <input placeholder="Phone Number" value="" class="form-control" ng-model="dba.currentAccount.phone"
           required="required" name="phone" type="text">
    <div class="help-block" ng-show="dba.formErrors.phone">
        <div ng-repeat="error in dba.formErrors.phone">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <div class="panel-title">P.O. Boxes</div>
    </div>
    <div class="panel-body">
        <fieldset>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.address }">
                <label class="col-sm-2 control-label">Address</label>
                <div class="col-sm-10">
                <input placeholder="Address" value="" class="form-control" ng-model="dba.po_box.address"
                       required="required" name="po_box_address" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.address">
                    <div ng-repeat="error in dba.formErrors.po_box.address">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.address_2 }">
                <label class="col-sm-2 control-label">Address Line 2</label>
                <div class="col-sm-10">
                <input placeholder="Address Line 2" value="" class="form-control" ng-model="dba.po_box.address_2"
                       required="required" name="po_box_address_2" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.address_2">
                    <div ng-repeat="error in dba.formErrors.po_box.address_2">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="row form-group">
                <label class="col-sm-2 control-label">City</label>
                    <div class="col-sm-4" ng-class="{ 'has-error' : dba.formErrors.po_box.city }">
                        <input placeholder="City" value="" class="form-control" ng-model="dba.po_box.city"
                               required="required" name="po_box_city" type="text">
                        <div class="help-block" ng-show="dba.formErrors.po_box.city">
                            <div ng-repeat="error in dba.formErrors.po_box.city">
                                <span ng-bind="error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3" ng-class="{ 'has-error' : dba.formErrors.po_box.state }">
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
                    <div class="col-sm-3" ng-class="{ 'has-error' : dba.formErrors.po_box.zip }">
                        <input placeholder="Zip Code" value="" class="form-control" ng-model="dba.po_box.zip"
                               required="required" name="po_box_zip" type="text">
                        <div class="help-block" ng-show="dba.formErrors.po_box.zip">
                            <div ng-repeat="error in dba.formErrors.po_box.zip">
                                <span ng-bind="error"></span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.phone }">
                <label class="col-sm-2 control-label">Phone Number</label>
                <div class="col-sm-10">
                <input placeholder="Phone Number" value="" class="form-control" ng-model="dba.po_box.phone"
                       required="required" name="po_box_phone" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.phone">
                    <div ng-repeat="error in dba.formErrors.po_box.phone">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.brands }">
                <label class="col-sm-2 control-label">Brand List</label>
                <div class="col-sm-10">
                <input placeholder="ie. Brand 1, Brand 2" value="" class="form-control"
                       ng-model="dba.po_box.brands" required="required" name="po_box_brands" type="text">
                <div class="help-block" ng-show="dba.formErrors.po_box.brands">
                    <div ng-repeat="error in dba.formErrors.po_box.brands">
                        <span ng-bind="error"></span>
                    </div>
                </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.esp_account_names }">
                <label class="col-sm-2 control-label">ESP Use</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <select class="form-control" name="po_box_esp_account" ng-model="dba.esp_account_name">
                            <option value="">ESP Use</option>
                            @foreach ( $espAccounts as $espAccount )
                                <option value="{{ $espAccount['account_name'] }}">{{ $espAccount['account_name'] }}</option>
                            @endforeach
                        </select>
                    <span class="input-group-btn">
                        <button class="btn mt2-theme-btn-primary" ng-click="dba.addEspAccount()" type="button">Add ESP</button>
                      </span>
                    </div>
                    <div class="help-block" ng-show="dba.formErrors.po_box.esp_account_names">
                        <div ng-repeat="error in dba.formErrors.po_box.esp_account_names">
                            <span ng-bind="error"></span>
                        </div>
                    </div>
                    <ul class="list-group" ng-show="dba.po_box.esp_account_names.length > 0">
                        <li ng-repeat="(key, value) in dba.po_box.esp_account_names track by $index" class="list-group-item mt2-list-group-item-grey cmp-list-item-condensed">@{{value}} - <a ng-click="dba.removeEspAccount(key)">Remove</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.isp_names }">
                <label class="col-sm-2 control-label">ISP Use</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <select class="form-control" name="po_box_isp_name" ng-model="dba.isp_name">
                            <option value="">ISP Use</option>
                            @foreach ( $isps as $isp )
                                <option value="{{ $isp['name'] }}">{{ $isp['name'] }}</option>
                            @endforeach
                        </select>
                    <span class="input-group-btn">
                        <button class="btn mt2-theme-btn-primary" ng-click="dba.addIsp()" type="button">Add ISP</button>
                      </span>
                    </div>
                    <div class="help-block" ng-show="dba.formErrors.po_box.isp_names">
                        <div ng-repeat="error in dba.formErrors.po_box.isp_names">
                            <span ng-bind="error"></span>
                        </div>
                    </div>

                    <ul class="list-group" ng-show="dba.po_box.isp_names.length > 0">
                        <li ng-repeat="(key, value) in dba.po_box.isp_names track by $index" class="list-group-item cmp-list-item-condensed mt2-list-group-item-grey">@{{value}} - <a ng-click="dba.removeIsp(key)">Remove</a></li>
                    </ul>
                </div>
            </div>

        </fieldset>
        <button class="btn mt2-theme-btn-primary btn-block" ng-click="dba.addPOBox()">
            <span ng-show="!dba.editingPOBox">Create </span>
            <span ng-show="dba.editingPOBox">Update </span>P.O. Box
        </button>
    </div>
    <div class="panel-footer" ng-show="dba.poBoxHolder.length > 0">
        <div class="thumbnail" ng-repeat="(key, value) in dba.poBoxHolder track by $index">
            <div class="caption clearfix">
                <strong>PO Box:</strong>
                @{{value.address}} @{{value.address_2}} @{{value.city}} @{{value.state}} @{{value.zip}}
                <span ng-if="value.phone"><b>tel: </b>@{{value.phone}}</span>
                <span ng-if="value.brands"><b>Brands: </b>@{{value.brands}}</span>
                <span ng-if="value.esp_account_names.length > 0"><b>ESPs: </b> @{{ value.esp_account_names.join(', ') }}</span>
                <span ng-if="value.isp_names.length > 0"><b>ISPs: </b> @{{ value.isp_names.join(', ') }} </span>
                <div class="pull-right">
                    <a href="#" class="btn mt2-theme-btn-primary btn-xs" ng-click="dba.editPOBox(key)" role="button">Edit</a>
                    <a href="#" class="btn mt2-theme-btn-secondary btn-xs" ng-click="dba.removePOBox(key)"
                       role="button">Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : dba.formErrors.notes }">
    <label class="col-sm-2 control-label">Notes</label>
    <div class="col-sm-10">
        <textarea placeholder="Notes" value="" class="form-control" ng-model="dba.currentAccount.notes"
                  name="notes"></textarea>
    <div class="help-block" ng-show="dba.formErrors.notes">
        <div ng-repeat="error in dba.formErrors.notes">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
</div>