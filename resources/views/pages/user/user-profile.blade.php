@extends( 'layout.default' )
@section('title', 'My Profile')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="userController as user" ng-init="user.loadProfile({{$id}})">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit User</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <input name="_token" type="hidden" value="{{ csrf_token() }}">
                    <fieldset>
                        <!-- Email field -->
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.email }">
                            <input placeholder="Email" value="" class="form-control" ng-model="user.currentAccount.email" required="required" name="email" type="text">
                            <span class="help-block" ng-bind="user.formErrors.email" ng-show="user.formErrors.email"></span>
                        </div>
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.username }">
                            <input placeholder="Username" ng-model="user.currentAccount.username"  class="form-control" required="required" name="username" type="text">
                            <span class="help-block" ng-bind="user.formErrors.username" ng-show="user.formErrors.username"></span>
                        </div>
                        <!-- First name field -->
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.first_name }">
                            <input placeholder="First Name" value="" class="form-control" required="required" name="first_name" ng-model="user.currentAccount.first_name" type="text">
                            <span class="help-block" ng-bind="user.formErrors.first_name" ng-show="user.formErrors.first_name"></span>
                        </div>
                        <!-- Last name field -->
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.last_name }">
                            <input placeholder="Last Name" value="" class="form-control" required="required" name="last_name" ng-model="user.currentAccount.last_name" type="text">
                            <span class="help-block" ng-bind="user.formErrors.last_name" ng-show="user.formErrors.last_name"></span>
                        </div>
                        <!-- Password field -->
                        <h4>If you would like to update your password please fill out below (Optional)</h4>
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.password }">
                            <input placeholder="Password" class="form-control" required="required" name="password" type="password" ng-model="user.currentAccount.password" value="">
                            <span class="help-block" ng-bind="user.formErrors.password" ng-show="user.formErrors.password"></span>
                        </div>
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass }">
                            <input placeholder="New Password" class="form-control" required="required" name="newpass" type="password" ng-model="user.currentAccount.newpass" value="">
                            <span class="help-block" ng-bind="user.formErrors.newpass" ng-show="user.formErrors.newpass"></span>
                        </div>
                        <!-- Password Confirmation field -->
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass_confirmation }">
                            <input placeholder="Password Confirm" class="form-control" required="required" name="newpass_confirmation" ng-model="user.currentAccount.newpass_confirmation" type="password" value="">
                            <span class="help-block" ng-bind="user.formErrors.newpass_confirmation" ng-show="user.formErrors.newpass_confirmation"></span>
                        </div>
                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="user.updateProfile()" type="submit" value="Edit Profile">
                        </div>
                    </fieldset>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/user.js"></script>
@stop
