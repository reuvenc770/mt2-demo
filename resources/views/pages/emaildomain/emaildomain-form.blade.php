    <div class="form-horizontal">
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : emailDomain.formErrors.domain_name }">
            <label class="col-sm-2 control-label">Domain Name</label>
            <div class="col-sm-10">
            <input placeholder="Domain Name" value="" class="form-control" ng-model="emailDomain.currentAccount.domain_name" required="required" name="name" type="text">
            <div class="help-block" ng-show="emailDomain.formErrors.domain_name">
                <div ng-repeat="error in emailDomain.formErrors.domain_name">
                    <span ng-bind="error"></span>
                </div>
            </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : emailDomain.formErrors.domain_group_id }">
            <label class="col-sm-2 control-label">Domain Group</label>
            <div class="col-sm-10">
            <select ng-model="emailDomain.currentAccount.domain_group_id" placeholder="" name="domain_group_id"  class="form-control">
                <option  value="">Select Domain Group</option>
                @foreach ($domainGroups as $domainGroup)
                <option ng-selected="emailDomain.currentAccount.domain_group_id == {{ $domainGroup->id }}" value="{{$domainGroup->id}}">{{$domainGroup->name}}</option>
                    @endforeach
            </select>
            <div class="help-block"  ng-show="emailDomain.formErrors.domain_group_id">
                <div ng-repeat="error in emailDomain.formErrors.domain_group_id">
                    <span ng-bind="error"></span>
                </div>
            </div>
            </div>
        </div>
    </div>