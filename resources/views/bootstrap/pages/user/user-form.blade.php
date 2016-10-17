<input name="_token" type="hidden" value="{{ csrf_token() }}">
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.email }">
    <input placeholder="Email" value="{{old('email') }}" class="form-control" ng-model="user.currentAccount.email" required="required" name="email" type="text">
    <div class="help-block" ng-show="user.formErrors.email">
        <div ng-repeat="error in user.formErrors.email">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.username }">
    <input placeholder="Username" ng-model="user.currentAccount.username" class="form-control" required="required" name="username" type="text">
    <div class="help-block" ng-show="user.formErrors.username">
        <div ng-repeat="error in user.formErrors.username">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.password }" ng-show="{{ $formType == 'add' }}">
    <input placeholder="Password" class="form-control" required="required" name="password" type="password" ng-model="user.currentAccount.password" value="">
    <div class="help-block" ng-show="user.formErrors.password">
        <div ng-repeat="error in user.formErrors.password">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.password_confirmation }" ng-show="{{ $formType == 'add' }}">
    <input placeholder="Password Confirm" class="form-control" required="required" name="password_confirmation" ng-model="user.currentAccount.password_confirmation" type="password" value="">
    <div class="help-block" ng-show="user.formErrors.password_confirmation">
        <div ng-repeat="error in user.formErrors.password_confirmation">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.first_name }">
    <input placeholder="First Name" value="" class="form-control" required="required" name="first_name" ng-model="user.currentAccount.first_name" type="text">
    <div class="help-block" ng-show="user.formErrors.first_name">
        <div ng-repeat="error in user.formErrors.first_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.last_name }">
    <input placeholder="Last Name" value="" class="form-control" required="required" name="last_name" ng-model="user.currentAccount.last_name" type="text">
    <div class="help-block" ng-show="user.formErrors.last_name">
        <div ng-repeat="error in user.formErrors.last_name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : user.formErrors.roles }">
    <h4>Roles (check all that apply)</h4>
        @foreach ($roles as $role)
        <label class="checkbox-inline">
            <input type="checkbox" name="items[]" value="{{ $role->id }}" ng-checked="user.currentAccount.roles.indexOf({{$role->id}})> -1" ng-click="user.toggleSelection({{$role->id}})"
            />{{ $role->name }}
            </label>
        @endforeach
    <div class="help-block" ng-show="user.formErrors.roles">
        <div ng-repeat="error in user.formErrors.roles">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>