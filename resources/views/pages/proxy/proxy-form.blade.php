
    <input name="_token" type="hidden" value="{{ csrf_token() }}">
    <!-- Email field -->
    <md-input-container>
        <label>Name</label>
        <input type="text" name="name" ng-required="true" ng-model="proxy.currentAccount.name" ng-change="proxy.onFormFieldChange( $event , proxyForm , 'name' )"/>
        <div ng-messages="proxyForm.name.$error">
            <div ng-message="required">Name is required.</div>
            <div ng-repeat="error in proxy.formErrors.name">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Provider's Name</label>
        <input type="text" name="provider_name" ng-required="true" ng-model="proxy.currentAccount.provider_name" />
        <div ng-messages="proxyForm.provider_name.$error">
            <div ng-message="required">Provider name is required.</div>
        </div>
    </md-input-container>

    <md-chips name="ip_addresses" placeholder="IP Address*" secondary-placeholder="+ IP Address"
                ng-model="proxy.ip_addresses"
                md-removable="true"
                md-enable-chip-edit="true"
                md-separator-keys="proxy.mdChipSeparatorKeys"
                md-add-on-blur="true"
                ng-change="proxy.onFormFieldChange( $event , proxyForm, 'ip_addresses' )">

    </md-chips>
    <div ng-messages="proxyForm.ip_addresses.$error" >
        <div ng-message="required" class="mt2-error-message">At least 1 IP address is required.</div>
        <div ng-repeat="error in proxy.formErrors.ip_addresses">
            <div ng-bind="error" class="mt2-error-message"></div>
        </div>
    </div>

    <div layout="row" layout-align="center center">
        <md-input-container flex>
            <label>ESP</label>
            <md-select name="esp_account_name" ng-model="proxy.esp_account_name">
                @foreach ( $esps as $esp )
                    <md-option value="{{ $esp['name'] }}">{{ $esp['name'] }}</md-option>
                @endforeach
            </md-select>
            <div ng-messages="proxyForm.esp_account_name.$error">
                <div ng-repeat="error in proxy.formErrors.esp_account_names">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </md-input-container>
        <div ng-show="proxy.esp_account_names.length > 0">
            <md-button class="md-icon-button" flex="auto" ng-click="proxy.addEsp()">
                <md-icon md-svg-icon="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
                <md-tooltip md-direction>Add ESP</md-tooltip>
            </md-button>
        </div>
    </div>
    <div ng-show="proxy.esp_account_names.length > 0" layout-padding>
        <p class="no-margin" ng-repeat="(key, value) in proxy.esp_account_names track by $index"> @{{value}}
            <a ng-click="proxy.removeEsp(key)">Remove</a></p>
    </div>
    <div layout="row" layout-align="center center">
        <md-input-container flex>
            <label>ISP</label>
            <md-select name="isp_name" id="isp_name" ng-model="proxy.isp_name">
                <md-option ng-repeat="option in proxy.isps" ng-value="option">@{{ option }}</md-option>
            </md-select>
            <div ng-messages="proxyForm.isp_name.$error">
                <div ng-repeat="error in proxy.formErrors.isp_names">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </md-input-container>
        <div ng-show="proxy.isp_names.length > 0">
            <md-button class="md-icon-button" flex="auto" ng-click="proxy.addIsp()">
                <md-icon md-svg-icon="img/icons/ic_add_circle_outline_black_24px.svg"></md-icon>
                <md-tooltip md-direction>Add ISP</md-tooltip>
            </md-button>
        </div>
    </div>
    <div ng-show="proxy.isp_names.length > 0" layout-padding>
        <p class="no-margin" ng-repeat="(key, value) in proxy.isp_names track by $index"> @{{value}}
            <a ng-click="proxy.removeIsp(key)">Remove</a></p>
    </div>

    <md-input-container>
        <label>Notes</label>
        <textarea ng-model="proxy.currentAccount.notes" rows="5" id="notes"></textarea>
    </md-input-container>
