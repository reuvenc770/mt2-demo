<div class="panel-body">
    <input name="_token" type="hidden" value="{{ csrf_token() }}">
    <fieldset>
        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.espName }">
            <select ng-model="domain.currentAccount.espName" placeholder="Select ESP" required="required"
                    ng-change="domain.updateEspAccounts()" class="form-control" ng-disabled="domain.updatingAccounts">
                <option value="">Select Esp</option>
                @foreach ( $esps as $esp )
                    <option value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
                @endforeach
            </select>
            <span class="help-block" ng-bind="domain.formErrors.espName" ng-show="domain.formErrors.espName"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.espAccountId }">
            <select name="esp_account" id="esp_account"
                    ng-model="domain.currentAccount.espAccountId" ng-change="domain.updateDomains()" class="form-control"
                    ng-disabled="domain.espNotChosen">
                <option value="">- Please Choose an ESP Account -</option><option ng-repeat="option in domain.espAccounts" ng-value="option.id" ng-selected="option.id == domain.currentAccount.espAccountId">@{{ option.account_name }}</option>
            </select>
            <span class="help-block" ng-bind="domain.formErrors.espAccountId" ng-show="domain.formErrors.espAccountId"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.registrar }">
            <select ng-model="domain.currentAccount.registrar" placeholder="Select DBA" required="required"
                    class="form-control">
                <option value="">Select Registrar</option>
                @foreach ( $regs as $reg )
                    <option value="{{ $reg['id'] }}">{{ $reg['name'] }}</option>
                @endforeach
            </select>
            <span class="help-block" ng-bind="domain.formErrors.registrar" ng-show="domain.formErrors.registrar"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.proxy }" ng-if="domain.type ==2">
            <select name="proxy" id="proxy"
                    ng-options='(option.name + " - " + option.ip_addresses) for option in domain.proxies'
                    ng-model="domain.selectedProxy" class="form-control"
                    ng-disabled="domain.updatingAccounts">
                <option value="">- Please Choose a Proxy -</option>
            </select>
            <span class="help-block" ng-bind="domain.formErrors.proxy" ng-show="domain.formErrors.proxy"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.dba }">
            <select ng-model="domain.currentAccount.dba" placeholder="Select DBA" required="required"
                    class="form-control">
                <option value="">Select DBA</option>
                @foreach ( $dbas as $dba )
                    <option value="{{ $dba['id'] }}">{{ $dba['dba_name'] }}</option>
                @endforeach
                <span class="help-block" ng-bind="domain.formErrors.dba" ng-show="domain.formErrors.dba"></span>
            </select>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.domains }">
            <label for="domains">@{{ domain.currentInfo }}</label>
            <textarea ng-model="domain.currentAccount.domains" class="form-control" rows="5" id="domains"></textarea>
            <span class="help-block" ng-bind="domain.formErrors.domains" ng-show="domain.formErrors.domains"></span>
        </div>
