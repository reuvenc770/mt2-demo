
    <input name="_token" type="hidden" value="{{ csrf_token() }}">
    <fieldset>
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.name }">
            <input placeholder="Name" class="form-control" ng-model="proxy.currentAccount.name" required="required"
                   name="name" type="text">
            <span class="help-block" ng-bind="proxy.formErrors.name" ng-show="proxy.formErrors.name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.provider_name }">
            <input placeholder="Provider's Name"  class="form-control" ng-model="proxy.currentAccount.provider_name"
                   required="required" name="provider_name" type="text">
            <span class="help-block" ng-bind="proxy.formErrors.provider_name"
                  ng-show="proxy.formErrors.provider_name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.ip_addresses }">
            <div class="input-group">
                <input placeholder="IP Address" class="form-control" ng-model="proxy.ip_address" required="required"
                       name="ip_address" type="text">
                <span class="help-block" ng-bind="proxy.formErrors.ip_addresses"
                      ng-show="proxy.formErrors.ip_addresses"></span>
                 <span class="input-group-btn">
            <button class="btn btn-primary" ng-click="proxy.addIpAddress()" type="button">Add IP</button>
                     </span>
            </div>
            <div ng-show="proxy.ip_addresses.length > 0" class="panel-footer">
                <p ng-repeat="(key, value) in proxy.ip_addresses track by $index"> @{{value}}
                    <a ng-click="proxy.editIpAddress(key)">Edit</a>
                    <a ng-click="proxy.removeIpAddress(key)">Remove</a></p>
            </div>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.esp_account_names }">
            <div class="input-group">

                <span class="help-block" ng-bind="proxy.formErrors.esp_account_names"
                      ng-show="proxy.formErrors.esp_account_names"></span>
                <select ng-model="proxy.esp_account_name" placeholder="Select ESP Account" required="required"
                        class="form-control">
                    <option value="">Select EspAccount</option>
                    <option value="All ESP Accounts">All Esp Accounts</option>
                    @foreach ( $esps as $esp )
                        <option value="All {{ $esp['name'] }} Accounts">All {{ $esp['name'] }} Accounts</option>
                    @endforeach
                    @foreach ( $espAccounts as $espAccount )
                        <option value="{{ $espAccount['account_name'] }}">{{ $espAccount['account_name'] }}</option>
                    @endforeach
                </select>
                 <span class="input-group-btn">
            <button class="btn btn-primary" ng-click="proxy.addEspAccount()" type="button">Add Esp Account</button>
                     </span>
            </div>
            <div ng-show="proxy.esp_account_names.length > 0" class="panel-footer">
                <p ng-repeat="(key, value) in proxy.esp_account_names track by $index"> @{{value}}
                    <a ng-click="proxy.removeEspAccount(key)">Remove</a></p>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : domain.formErrors.isp_names }">
            <div class="input-group">
                <select name="isp_name" id="isp_name"
                        ng-model="proxy.isp_name" class="form-control">
                    <option value="">Select ISP</option>
                    <option ng-repeat="option in proxy.isps" ng-value="option">@{{ option }}</option>
                </select>
                    <span class="input-group-btn">
            <button class="btn btn-primary" ng-click="proxy.addIsp()" type="button">Add Isp</button>
                     </span>
            </div>
            <div ng-show="proxy.isp_names.length > 0" class="panel-footer">
                <p ng-repeat="(key, value) in proxy.isp_names track by $index"> @{{value}}
                    <a ng-click="proxy.removeIsp(key)">Remove</a></p>
            </div>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.notes }">
            <textarea placeholder="Notes" class="form-control" ng-model="proxy.currentAccount.notes" required="required"
                   name="name" rows="5" ></textarea>
            <span class="help-block" ng-bind="proxy.formErrors.notes" ng-show="proxy.formErrors.notes"></span>
        </div>

