<div class="form-horizontal">
<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.espId }">
    <label class="col-sm-2 control-label">ESP Name</label>
    <div class="col-sm-10">
    <select id="espId" name="espId" class="form-control" required="required" ng-model="esp.currentAccount.espId" ng-disabled="{{ $formType == 'edit' }}" ng-change="esp.updateKeyNames( esp.currentAccount.espId )">
        <option value="">ESP Name</option>
        @foreach( $espList as $espId => $esp )
            <option value="{{ $espId }}">{{ $esp }}</option>
        @endforeach
    </select>
    <div class="help-block" ng-show="esp.formErrors.espId">
        <div ng-repeat="error in esp.formErrors.espId">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.accountName }">
    <label class="col-sm-2 control-label">Account Name</label>
    <div class="col-sm-10">
    <input placeholder="Account Name" type="text" id="accountName" name="accountName" class="form-control" required="required" ng-model="esp.currentAccount.accountName" value=""/>
    <div class="help-block" ng-show="esp.formErrors.accountName">
        <div ng-repeat="error in esp.formErrors.accountName">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key1 }">
    <label class="col-sm-2 control-label" ng-bind="esp.key1Name"></label>
    <div class="col-sm-10">
    <input placeholder="Key 1" type="text" id="key1" name="key1" class="form-control" required="required" ng-model="esp.currentAccount.key1" value=""/>
    <div class="help-block" ng-show="esp.formErrors.key1">
        <div ng-repeat="error in esp.formErrors.key1">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key2 }">
    <label class="col-sm-2 control-label" ng-bind="esp.key2Name"></label>
    <div class="col-sm-10">
    <input placeholder="Key 2" type="text" id="key2" name="key2" class="form-control" ng-model="esp.currentAccount.key2" value="" ng-disabled="esp.currentAccount.id && esp.currentAccount.key2 === ''" />
    <div class="help-block" ng-show="esp.formErrors.key2">
        <div ng-repeat="error in esp.formErrors.key2">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
</div>