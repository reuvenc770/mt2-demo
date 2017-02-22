<div class="form-horizontal">
<input name="_token" type="hidden" value="{{ csrf_token() }}">
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.espName }">
    <label class="col-sm-2 control-label">ESP</label>
    <div class="col-sm-10">
    <select ng-model="domain.currentAccount.espName"   class="form-control" name="espName"
               ng-change="domain.updateEspAccounts()" ng-disabled="domain.updatingAccounts">
        <option value="">Please Select an ESP</option>
        @foreach ( $esps as $esp )
            <option value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
        @endforeach
    </select>
    <div class="help-block"  ng-show="domain.formErrors.espName">
        <div ng-repeat="error in domain.formErrors.espName">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.espAccountId }">
    <label class="col-sm-2 control-label">ESP Account</label>
    <div class="col-sm-10">
    <select ng-model="domain.currentAccount.espAccountId" class="form-control" name="espAccountId" id="esp_account"
               ng-change="domain.updateDomains()" ng-disabled="domain.espNotChosen">
        <option value="">Please Select an ESP Account</option>
        <option ng-repeat="option in domain.espAccounts" ng-value="option.id" ng-selected="option.id == domain.currentAccount.espAccountId">@{{ option.account_name }}</option>
    </select>
    <div class="help-block"  ng-show="domain.formErrors.espAccountId">
        <div ng-repeat="error in domain.formErrors.espAccountId">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.registrar }">
    <label class="col-sm-2 control-label">Registrar</label>
    <div class="col-sm-10">
    <select ng-model="domain.currentAccount.registrar" class="form-control" name="registrar" id="registrar">
        <option value="">Please Select a Registrar</option>
        @foreach ( $regs as $reg )
            <option value="{{ $reg['id'] }}">{{ $reg['name'] }}</option>
        @endforeach
    </select>
    <div class="help-block"  ng-show="domain.formErrors.registrar">
        <div ng-repeat="error in domain.formErrors.registrar">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.proxy_id }" ng-if="domain.type ==2">
    <label class="col-sm-2 control-label">Proxy</label>
    <div class="col-sm-10">
    <select name="proxy" id="proxy" class="form-control"
            ng-model="domain.proxy_id" ng-disabled="domain.updatingAccounts">
        <option value="">Please Select a Proxy</option>
        @foreach ( $proxies as $proxy )
            <option value="{{ $proxy['id'] }}">{{ $proxy['name'] }} &nbsp;-&nbsp; {{ $proxy['ip_addresses'] }}</option>
        @endforeach
    </select>
    <div class="help-block"  ng-show="domain.formErrors.proxy_id">
        <div ng-repeat="error in domain.formErrors.proxy_id">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.dba }">
    <label class="col-sm-2 control-label">DBA</label>
    <div class="col-sm-10">
    <select ng-required="true" name="dba" class="form-control"  ng-model="domain.currentAccount.dba">
        <option value="">Please Select a DBA</option>
        @foreach ( $dbas as $dba )
            <option value="{{ $dba['id'] }}">{{ $dba['dba_name'] }}</option>
        @endforeach
    </select>
    <div class="help-block"  ng-show="domain.formErrors.dba">
        <div ng-repeat="error in domain.formErrors.dba">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.live_a_record }">
    <label class="col-sm-2 control-label">A-Record</label>
    <div class="col-sm-10">
    <select ng-required="true" name="live_a_record" class="form-control"  ng-model="domain.currentAccount.live_a_record">
        <option value="">A-Record Live?</option>
        <option value="1">Yes</option>
        <option value="0">No</option>

    </select>
    <div class="help-block"  ng-show="domain.formErrors.live_a_record">
        <div ng-repeat="error in domain.formErrors.live_a_record">
            <span ng-bind="error"></span>
        </div>
    </div>
    </div>
</div>

</div>
<div class="form-group" ng-class="{ 'has-error' : domain.formErrors.domains }">
    <label>@{{ domain.currentInfo }} <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="All domain info is required to be in the exact order indicated and separated by commas. Date must also be in the correct format (YYYY-MM-DD).">help</md-icon></label>
    <textarea ng-model="domain.currentAccount.domains"  class="form-control" name="domains" rows="5" id="domains"></textarea>
    <div class="help-block"  ng-show="domain.formErrors.domains">
        <div ng-repeat="error in domain.formErrors.domains">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>