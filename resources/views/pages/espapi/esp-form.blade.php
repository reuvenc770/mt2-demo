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

<div class="form-group" ng-class="{ 'has-error' : esp.formErrors.customId }">
    <label class="col-sm-2 control-label">Custom ID
        <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="To use a custom ID field for open pixel URL, enter a number (must be at least 6 digits) or generate one by clicking 'Randomize ID'. Once an ESP API acccount has a custom ID, it will always be required.">help</md-icon>
    </label>
    <div class="col-sm-10">
        <div class="input-group">
            <input placeholder="Custom ID" type="text" id="customId" name="customId" class="form-control" required="required" ng-model="esp.currentAccount.customId" value=""/>
            <span class="input-group-btn">
                <button class="btn mt2-theme-btn-primary" ng-click="esp.generateCustomId()" type="button">Randomize ID</button>
            </span>
        </div>
    <div class="help-block" ng-show="esp.formErrors.customId">
        <div ng-repeat="error in esp.formErrors.customId">
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