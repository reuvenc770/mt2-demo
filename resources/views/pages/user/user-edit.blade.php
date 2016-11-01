@extends( 'layout.default' )
@section('title', 'Edit User')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="userController as user" ng-init="user.loadAccount()">
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
                            <input type="email" name="email" ng-required="true" ng-model="user.currentAccount.email">
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
                            <input type="text" name="username" ng-required="true" ng-model="user.currentAccount.username">
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
                        <div>
                            <h4>Roles (check all that apply)</h4>
                            <div layout="row" layout-wrap>
                                @foreach ($roles as $role)
                                    <md-checkbox flex="40" flex-gt-md="30" name="roles" value="{{ $role->id }}" ng-checked="user.currentAccount.roles.indexOf({{$role->id}})> -1"
                                                ng-click="user.toggleSelection({{$role->id}})">
                                                {{ $role->name }}
                                    </md-checkbox>
                                @endforeach
                                <div ng-messages="userForm.roles.$error">
                                    <div ng-message="required">A role is required.</div>
                                    <div ng-repeat="error in user.formErrors.roles">
                                        <div ng-bind="error" class="mt2-error-message"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Submit field -->
                        <md-button class="md-raised md-accent" ng-click="user.editAccount( $event , userForm )">Update Account</md-button>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection


@section( 'pageIncludes' )
    <script src="js/user.js"></script>
@stop
