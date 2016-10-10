@extends( 'layout.default' )
@section('title', 'Edit Role')

@section('content')
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="roleController as role"  ng-init="role.initEditPage()">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Edit Security Role</span>
                    </div>
                </md-toolbar>
                <md-card-content layout="column">
                    <form name="roleForm" layout="column" novalidate>
                        <md-input-container>
                            <label>Name</label>
                            <input type="text" name="name" ng-model="role.currentRole.name" ng-required="true">
                            <div ng-messages="roleForm.name.$error">
                                <div ng-message="required">Role name is required.</div>
                            </div>
                        </md-input-container>
                    </form>

                    <!-- Submit field -->
                    <md-button class="md-raised md-accent" ng-click="role.editRole()">Update Security Role</md-button>

                    @include( 'pages.role.role-form' )

                    <!-- Submit field -->
                    <md-button class="md-raised md-accent" ng-click="role.editRole()">Update Security Role</md-button>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@endsection

@section( 'pageIncludes' )
    <script src="js/role.js"></script>
@stop
