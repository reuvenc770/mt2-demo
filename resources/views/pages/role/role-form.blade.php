<md-fab-toolbar md-direction="right">
    <md-fab-trigger class="align-with-text">
        <md-button aria-label="menu" class="md-fab md-primary">
            <md-icon md-svg-src="img/icons/ic_menu_white_24px.svg"></md-icon>
        </md-button>
    </md-fab-trigger>
    <md-toolbar>
        <md-fab-actions class="md-toolbar-tools">
            <md-button aria-label="Select All" ng-click="role.selectPermissions( role.permissions.routes )">
                <md-icon md-svg-icon="img/icons/ic_select_all_white_24px.svg"></md-icon> Route
            </md-button>

            <md-button aria-label="Clear All" ng-click="role.unselectPermissions( role.permissions.routes )">
                <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon> Route
            </md-button>

            <md-button aria-label="Select All" ng-click="role.selectPermissions( role.permissions.api )">
                <md-icon md-svg-icon="img/icons/ic_select_all_white_24px.svg"></md-icon> API
            </md-button>

            <md-button aria-label="Clear All" ng-click="role.unselectPermissions( role.permissions.api )">
                <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon> API
            </md-button>
        </md-fab-actions>
    </md-toolbar>
</md-fab-toolbar> 

<div class="col-sm-12">
    <span class="help-block" ng-bind="role.formErrors.permissions" ng-show="role.formErrors.permissions"></span>
</div>

<div class="clearfix"></div>

<div layout-xs="column" layout="row" layout-wrap flex>
    <md-card ng-repeat="( groupName , permissionGroup ) in role.permissions.routes">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h4>Routes::@{{ groupName }}</h4>

                <span flex></span>

                <md-button class="md-icon-button" aria-label="Select All" ng-click="role.selectPermissions( permissionGroup )">
                    <md-icon md-svg-icon="img/icons/ic_select_all_white_24px.svg"></md-icon>
                </md-button>

                <md-button class="md-icon-button"  aria-label="Clear All" ng-click="role.unselectPermissions( permissionGroup )">
                    <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon>
                </md-button>
            </div>
        </md-toolbar>

        <md-content>
            <md-list class="md-dense">
                <md-list-item ng-repeat="permissionName in permissionGroup">
                    <div layout="row">
                        <div layout="column">
                            <md-checkbox ng-model="selectedPermissions[ permissionName ]" aria-label="@{{ permissionName }}"></md-checkbox>
                        </div>

                        <div layout="column">
                            @{{ permissionName }}
                        </div>
                    </div>

                    <md-divider ng-if="!$last"></md-divider>
                </md-list-item>
            </md-list>
        </md-content>
    </md-card>

    <md-card ng-repeat="( groupName , permissionGroup ) in role.permissions.api">
        <md-toolbar class="md-accent">
            <div class="md-toolbar-tools">
                <h4>API::@{{ groupName }}</h4>

                <span flex></span>

                <md-button class="md-icon-button"  aria-label="Select All" ng-click="role.selectPermissions( permissionGroup )">
                    <md-icon md-svg-icon="img/icons/ic_select_all_white_24px.svg"></md-icon>
                </md-button>

                <md-button class="md-icon-button"  aria-label="Clear All" ng-click="role.unselectPermissions( permissionGroup )">
                    <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon>
                </md-button>
            </div>
        </md-toolbar>

        <md-content>
            <md-list class="md-dense">
                <md-list-item ng-repeat="permissionName in permissionGroup">
                    <div layout="row">
                        <div layout="column">
                            <md-checkbox ng-model="selectedPermissions[ permissionName ]" aria-label="@{{ permissionName }}"></md-checkbox>
                        </div>

                        <div layout="column">
                            @{{ permissionName }}
                        </div>
                    </div>

                    <md-divider ng-if="!$last"></md-divider>
                </md-list-item>
            </md-list>
        </md-content>
    </md-card>
</div>
