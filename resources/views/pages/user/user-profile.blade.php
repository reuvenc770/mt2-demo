@extends( 'layout.default' )
@section('title', 'My Profile')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="userController as user" ng-init="user.loadProfile({{$id}})">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Edit User</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form name="userForm" layout="column" novalidate>
                        <input name="_token" type="hidden" value="{{ csrf_token() }}">
                        <!-- Email field -->
                        <md-input-container>
                            <label>Email</label>
                            <input type="email" name="email" ng-required="true" ng-model="user.currentAccount.email"
                                    ng-change="user.onFormFieldChange( $event , userForm , 'email' )">
                            <div ng-messages="userForm.email.$error">
                                <div ng-message="required">Email is required.</div>
                                <div ng-message="email">Invalid email format.</div>
                                <div ng-repeat="error in user.formErrors.email">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <md-input-container>
                            <label>Username</label>
                            <input type="text" name="username" ng-required="true" ng-model="user.currentAccount.username"
                                    ng-change="user.onFormFieldChange( $event , userForm , 'username' )">
                            <div ng-messages="userForm.username.$error">
                                <div ng-message="required">Username is required.</div>
                                <div ng-repeat="error in user.formErrors.username">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <md-input-container>
                            <label>First Name</label>
                            <input type="text" name="first_name" ng-required="true" ng-model="user.currentAccount.first_name">
                            <div ng-messages="userForm.first_name.$error">
                                <div ng-message="required">First name is required.</div>
                                <div ng-repeat="error in user.formErrors.first_name">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <md-input-container>
                            <label>Last Name</label>
                            <input type="text" name="last_name" ng-required="true" ng-model="user.currentAccount.last_name">
                            <div ng-messages="userForm.last_name.$error">
                                <div ng-message="required">Last name is required.</div>
                                <div ng-repeat="error in user.formErrors.last_name">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <!-- Password field -->
                        <h4>If you would like to update your password please fill out below (optional)</h4>
                        <md-input-container>
                            <label>Password</label>
                            <input type="password" name="password" ng-model="user.currentAccount.password"
                                    ng-change="user.onFormFieldChange( $event , userForm , 'password' )">
                            <div ng-messages="userForm.password.$error">
                                <div ng-repeat="error in user.formErrors.password">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <md-input-container>
                            <label>New Password</label>
                            <input type="password" name="newpass" ng-model="user.currentAccount.newpass"
                                    ng-change="user.onFormFieldChange( $event , userForm , 'newpass' )">
                            <div ng-messages="userForm.newpass.$error">
                                <div ng-repeat="error in user.formErrors.newpass">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <!-- Password Confirmation field -->
                        <md-input-container>
                            <label>Password Confirm</label>
                            <input type="password" name="newpass_confirmation"
                                    ng-model="user.currentAccount.newpass_confirmation"
                                    ng-change="user.onFormFieldChange( $event , userForm , 'newpass' )">
                            <div ng-messages="userForm.newpass_confirmation.$error">
                                <div ng-repeat="error in user.formErrors.newpass_confirmation">
                                    <div ng-bind="error"></div>
                                </div>
                            </div>
                        </md-input-container>
                        <!-- Submit field -->
                        <md-button class="md-raised md-accent" ng-click="user.updateProfile( $event , userForm )">Update Profile</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/user.js"></script>
@stop
