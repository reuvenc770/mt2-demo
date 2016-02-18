@extends( 'layout.default' )
@section('title', 'Add Role')

@section('content')
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default" ng-controller="roleController as role">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add Security Role</h1>
                    </div>
                    <div class="panel-body">
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <fieldset>
                                <!-- Name field -->
                                <div class="form-group" ng-class="{ 'has-error' : role.formErrors.name }">
                                    <input placeholder="Name" value="" class="form-control" ng-model="role.currentRole.name" required="required" name="name" type="text">
                                    <span class="help-block" ng-bind="role.formErrors.name" ng-show="role.formErrors.name"></span>
                                </div>
                                <div>
                                    <div class="checkbox-inline">
                                        <label>
                                            <input type="checkbox" ng-model="role.currentRole.apiUser" name="apiUser"/>Api User
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group permissions" ng-class="{ 'has-error' : role.formErrors.permissions }" ng-hide="role.currentRole.apiUser == true">
                                   <h4 >Roles (check all that apply)</h4>
                                        @foreach ($permissions as $permission)
                                        <div class="checkbox col-sm-3">
                                        <label>
                                            <input type="checkbox" name="items[]" value="{{ $permission->name }}" ng-checked="role.currentRole.permissions.indexOf('{{$permission->name}}')> -1" ng-click="role.toggleSelection('{{$permission->name}}')"
                                            />{{ trans($permission->name) }}</label>
                                        </div>
                                        @endforeach
                                </div>
                                <div class="form-group permissions" ng-class="{ 'has-error' : role.formErrors.permissions }" >
                                    <h4 >API Roles(check all that apply)</h4>
                                    @foreach ($permissionsAPI as $permission)
                                        <div class="checkbox col-sm-3">
                                            <label>
                                                <input type="checkbox" name="items[]" value="{{ $permission->name }}"  ng-checked="role.currentRole.permissions.indexOf('{{$permission->name}}')> -1" ng-click="role.toggleSelection('{{$permission->name}}')"
                                                />{{ trans($permission->name) }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                    <div class="col-sm-12">
                                    <span class="help-block" ng-bind="role.formErrors.permissions" ng-show="role.formErrors.permissions"></span>
                                        </div>

                                <!-- Submit field -->
                                <div class="form-group">
                                    <input class="btn btn-lg btn-primary btn-block" ng-click="role.saveNewRole()" type="submit" value="Create Security Role">
                                </div>
                            </fieldset>
                    </div>
                </div>
            </div>
        </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
