
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.name }">
            <input placeholder="Domain Name" value="" class="form-control" ng-model="dg.currentAccount.name" required="required" name="name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.dba_name" ng-show="dba.formErrors.dba_name"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dg.formErrors.domain_group_id }">
            <select ng-model="dg.currentAccount.domain_group_id" placeholder="" name="domain_group_id"  class="form-control">
                <option  value="">Select Domain Group</option>
                @foreach ($domainGroups as $domainGroup)
                <option ng-selected="dg.currentAccount.domain_group_id == {{$domainGroup->id}}" value="{$domainGroup->id}}">{{$domainGroup->name}}</option>
                    @endforeach
            </select>
            <span class="help-block" ng-bind="dg.formErrors.country" ng-show="dg.formErrors.country"></span>
        </div>

