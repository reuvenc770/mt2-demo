@extends( 'layout.default' )
@section('title', 'Add Role')

@section('content')
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default" ng-controller="roleController as role" ng-init="role.initCreatePage()">
                    <div class="panel-heading">
                        <h1 class="panel-title">Add Security Role</h1>
                    </div>
                    <div class="panel-body">
                        <div class="form-group" ng-class="{ 'has-error' : role.formErrors.name }">
                            <input placeholder="Name" value="" class="form-control" ng-model="role.currentRole.name" required="required" name="name" type="text">
                            <span class="help-block" ng-bind="role.formErrors.name" ng-show="role.formErrors.name"></span>
                        </div>

                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="role.saveNewRole()" type="submit" value="Create Security Role">
                        </div>

                        @include( 'pages.role.role-form' )

                        <!-- Submit field -->
                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" ng-click="role.saveNewRole()" type="submit" value="Create Security Role">
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection


@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
