@extends( 'layout.default' )
@section('title', 'Edit Role')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="roleController as role"  ng-init="role.initEditPage()">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit Security Role</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <div class="form-group" ng-class="{ 'has-error' : role.formErrors.name }">
                        <input placeholder="Name" value="" class="form-control" ng-model="role.currentRole.name" required="required" name="name" type="text">
                        <span class="help-block" ng-bind="role.formErrors.name" ng-show="role.formErrors.name"></span>
                    </div>

                    <!-- Submit field -->
                    <div class="form-group">
                        <input class="btn btn-lg btn-primary btn-block" ng-click="role.editRole()" type="submit" value="Update Security Role">
                    </div>

                    @include( 'pages.role.role-form' )

                    <!-- Submit field -->
                    <div class="form-group">
                        <input class="btn btn-lg btn-primary btn-block" ng-click="role.editRole()" type="submit" value="Update Security Role">
                    </div>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection

@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
