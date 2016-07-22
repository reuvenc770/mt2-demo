<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default" ng-controller="domainController as domain">
            <div class="panel-heading">
                <h1 class="panel-title">Add User</h1>
            </div>
            <div class="panel-body">
                <input name="_token" type="hidden" value="{{ csrf_token() }}">
                <fieldset>
                    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key2 }">
                    <select ng-model="domain.currentAccount.espName" placeholder="Select ESP" required="required" ng-change="domain.updateEspAccounts()" class="form-control">
                        <option value="">Select Esp</option>
                        @foreach ( $esps as $esp )
                            <option  value="{{ $esp['name'] }}">{{ $esp['name'] }}</option>
                        @endforeach
                    </select>
                        </div>
                    <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key2 }">
                        <select name="esp_account" id="esp_account"
                                ng-options="option.account_name for option in domain.espAccounts track by option.id"
                                ng-model="data.currentAccount.espAccountId" class="form-control" ng-disabled="domain.fetchingAccounts == false">
                            <option value="">- Please Choose an ESP Account -</option>
                        </select>

                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>