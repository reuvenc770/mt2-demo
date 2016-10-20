<md-card-content>
    <form name="domainForm{{$type}}" layout="column" novalidate>
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <md-input-container>
            <label>ESP</label>
            <md-select ng-model="domain.currentAccount.espName" ng-required="true" name="espName"
                        ng-change="domain.updateEspAccounts()" ng-disabled="domain.updatingAccounts">
                @foreach ( $esps as $esp )
                    <md-option value="{{ $esp['name'] }}">{{ $esp['name'] }}</md-option>
                @endforeach
            </md-select>
            <div ng-messages="domainForm{{$type}}.espName.$error">
                <div ng-message="required">ESP is required.</div>
            </div>
        </md-input-container>

        <md-input-container>
            <label>ESP Account</label>
            <md-select ng-model="domain.currentAccount.espAccountId" ng-required="true" name="espAccountId" id="esp_account"
                        ng-change="domain.updateDomains()" ng-disabled="domain.espNotChosen">
               <md-option ng-repeat="option in domain.espAccounts" ng-value="option.id" ng-selected="option.id == domain.currentAccount.espAccountId">@{{ option.account_name }}</md-option>
            </md-select>
            <div ng-messages="domainForm{{$type}}.espAccountId.$error">
                <div ng-message="required">ESP account is required.</div>
            </div>
        </md-input-container>

        <md-input-container>
            <label>Registrar</label>
            <md-select ng-required="true" name="registrar" ng-model="domain.currentAccount.registrar">
                @foreach ( $regs as $reg )
                    <md-option value="{{ $reg['id'] }}">{{ $reg['name'] }}</md-option>
                @endforeach
            </md-select>
            <div ng-messages="domainForm{{$type}}.registrar.$error">
                <div ng-message="required">Registrar is required.</div>
            </div>
        </md-input-container>

        <md-input-container ng-if="domain.type ==2">
            <label>Proxy</label>
            <md-select name="proxy" id="proxy"
                        ng-model="domain.selectedProxy" ng-disabled="domain.updatingAccounts">
                <md-option ng-repeat="option in domain.proxies" ng-value="option">@{{option.name }} - @{{option.ip_addresses}}</md-option>
            </md-select>
            <div ng-messages="domainForm{{$type}}.registrar.$error">
                <div ng-repeat="error in domain.formErrors.proxies">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </md-input-container>

        <md-input-container>
            <label>DBA</label>
            <md-select ng-required="true" name="dba" ng-model="domain.currentAccount.dba">
                @foreach ( $dbas as $dba )
                    <md-option value="{{ $dba['id'] }}">{{ $dba['dba_name'] }}</md-option>
                @endforeach
            </md-select>
            <div ng-messages="domainForm{{$type}}.dba.$error">
                <div ng-message="required">DBA is required.</div>
            </div>
        </md-input-container>

        <md-input-container>
            <label>@{{ domain.currentInfo }}</label>
            <textarea ng-model="domain.currentAccount.domains" ng-required="true" name="domains" rows="5" id="domains"></textarea>
            <div ng-messages="domainForm{{$type}}.domains.$error">
                <div ng-message="required">Domain info is required.</div>
            </div>
        </md-input-container>
    </form>
</md-card-content>