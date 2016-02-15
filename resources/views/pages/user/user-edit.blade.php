@extends( 'layout.default' )
@section('title', 'Edit User')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="userController as user" ng-init="user.loadAccount()">
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
                            <div class="form-group" ng-class="{ 'has-error' : user.formErrors.roles }">
                                <h4 >Roles (check all that apply)</h4>
                                @foreach ($roles as $role)
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="items[]" value="{{ $role->id }}" ng-checked="user.currentAccount.roles.indexOf({{$role->id}})> -1" ng-click="user.toggleSelection({{$role->id}})"
                                        />{{ $role->name }}
                                    </label>
                                @endforeach
                                <span class="help-block" ng-bind="user.formErrors.roles" ng-show="user.formErrors.roles"></span>
                            </div>
                            <!-- Submit field -->
                            <div class="form-group">
                                <input class="btn btn-lg btn-primary btn-block" ng-click="user.editAccount()" type="submit" value="Edit User">
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
