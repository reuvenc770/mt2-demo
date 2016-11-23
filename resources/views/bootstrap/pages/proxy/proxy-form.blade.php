<input name="_token" type="hidden" value="{{ csrf_token() }}">

<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.name }">
    <input placeholder="Name" value="" class="form-control" ng-model="proxy.currentAccount.name" required="required"
           name="name" type="text">
    <div class="help-block" ng-show="proxy.formErrors.name">
        <div ng-repeat="error in proxy.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.provider_name }">
    <input placeholder="Provider's Name" value="" class="form-control" ng-model="proxy.currentAccount.provider_name"
           required="required" name="name" type="text">
    <div class="help-block" ng-show="proxy.formErrors.provider_name">
        <div ng-repeat="error in proxy.formErrors.provider_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.esp_account_name }">
    <div class="input-group">
        <select class="form-control" name="esp_account_name" ng-model="proxy.esp_account_name">
            <option value="">Select ESP Account/s</option>
            <option value="All ESP Accounts">All Esp Accounts</option>
            @foreach ( $esps as $esp )
                <option value="All {{ $esp['name'] }} Accounts">{{ $esp['name'] }} Accounts</option>
            @endforeach
            @foreach ( $espAccounts as $espAccount )
                <option value="{{ $espAccount['account_name'] }}">{{ $espAccount['account_name'] }}</option>
            @endforeach
        </select>
    <span class="input-group-btn">
        <button class="btn mt2-theme-btn-primary" ng-click="proxy.addEspAccount()" type="button">Add ESP</button>
      </span>
    </div>
    <div class="help-block" ng-show="proxy.formErrors.esp_account_name">
        <div ng-repeat="error in proxy.formErrors.esp_account_name">
            <span ng-bind="error"></span>
        </div>
    </div>

</div>
<ul class="list-group" ng-show="proxy.esp_account_names.length > 0">
    <li ng-repeat="(key, value) in proxy.esp_account_names track by $index" class="list-group-item mt2-list-group-item-grey">@{{value}} - <a
                ng-click="proxy.removeEspAccount(key)">Remove</a></li>
</ul>

<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.ip_addresses }">
    <div class="input-group">
        <input placeholder="IP Address" value="" class="form-control" ng-model="proxy.ip_address" required="required"
               name="name" type="text">
     <span class="input-group-btn">
        <button class="btn mt2-theme-btn-primary" ng-click="proxy.addIpAddress()" type="button">Add IP</button>
      </span>
    </div>
    <div class="help-block" ng-show="proxy.formErrors.ip_addresses">
        <div ng-repeat="error in proxy.formErrors.ip_addresses">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<ul class="list-group" ng-show="proxy.ip_addresses.length > 0">
    <li ng-repeat="(key, value) in proxy.ip_addresses track by $index" class="list-group-item mt2-list-group-item-grey">@{{value}} - <a
                ng-click="proxy.removeIpAddress(key)">Remove</a></li>
</ul>

<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.isp_name }">
    <div class="input-group">
        <select name="isp_name" id="isp_name" class="form-control" ng-model="proxy.isp_name">
            <option value="">Select ISP</option>
            @foreach ( $isps as $isp )
                <option value="{{ $isp['name'] }}">{{ $isp['name'] }}</option>
            @endforeach
        </select>
     <span class="input-group-btn">
        <button class="btn mt2-theme-btn-primary" ng-click="proxy.addIsp()"  type="button">Add ISP</button>
      </span>
    </div>
    <div class="help-block" ng-show="proxy.formErrors.isp_name">
        <div ng-repeat="error in proxy.formErrors.isp_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<ul class="list-group" ng-show="proxy.isp_names.length > 0">
    <li ng-repeat="(key, value) in proxy.isp_names track by $index" class="list-group-item mt2-list-group-item-grey">@{{value}} - <a
                ng-click="proxy.removeIsp(key)">Remove</a></li>
</ul>

<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.dba_name }">
    <input placeholder="DBA" value="" class="form-control" ng-model="proxy.currentAccount.dba_name"
           name="dba_name" type="text">
    <div class="help-block" ng-show="proxy.formErrors.dba_name">
        <div ng-repeat="error in proxy.formErrors.dba_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>

<div class="form-group" ng-class="{ 'has-error' : proxy.formErrors.notes }">
        <textarea rows="4" placeholder="Notes" value="" class="form-control" ng-model="proxy.currentAccount.notes"
                  name="notes"></textarea>
    <div class="help-block" ng-show="proxy.formErrors.notes">
        <div ng-repeat="error in proxy.formErrors.notes">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>