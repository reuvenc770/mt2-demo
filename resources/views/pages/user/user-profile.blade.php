@extends( 'layout.default' )
@section('title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" ng-controller="userController as user" ng-init="user.loadProfile({{$id}})">
                <div class="panel-heading">
                    <h1 class="panel-title">Edit User</h1>
                </div>
                <div class="panel-body">
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
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.password }">
                            <input placeholder="Password" class="form-control" required="required" name="password" type="password" ng-model="user.currentAccount.password" value="">
                            <span class="help-block" ng-bind="user.formErrors.password" ng-show="user.formErrors.password"></span>
                        </div>
                        <div class="form-group" ng-class="{ 'has-error' : user.formErrors.newpass }">
                            <input placeholder="New Passowrd" class="form-control" required="required" name="newpass" type="password" ng-model="user.currentAccount.newpass" value="">
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
                </div>
            </div>
        </div>
    </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/user.js"></script>
@stop
