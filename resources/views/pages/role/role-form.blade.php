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

<md-content layout-wrap layout-padding layout-align="space-around" layout-xs="column" layout="row">
    <div layout-margin md-whiteframe="4" class="no-padding" ng-repeat="( groupName , permissionGroup ) in role.permissions.routes">
        <md-toolbar layout="row" layout-fill class="md-hue-2" layout-align="center center">
            <div class="md-toolbar-tools">
                <span>Routes::@{{ groupName }}</span>
            </div>

            <span flex></span>

            <md-button class="md-icon-button" aria-label="Select All" ng-click="role.selectPermissions( permissionGroup )">
                <md-icon md-svg-icon="img/icons/ic_select_all_white_24px.svg"></md-icon>
            </md-button>

            <md-button class="md-icon-button"  aria-label="Clear All" ng-click="role.unselectPermissions( permissionGroup )">
                <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon>
            </md-button>
        </md-toolbar>

        <div layout="row">
            <md-list class="md-dense" layout-fill>
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
        </div>
    </div>

    <div layout-margin md-whiteframe="4" class="no-padding" ng-repeat="( groupName , permissionGroup ) in role.permissions.api">
        <md-toolbar layout="row" layout-fill class="md-accent" layout-align="center center">
            <div class="md-toolbar-tools">
                <span>API::@{{ groupName }}</span>
            </div>

            <span flex></span>

            <md-button class="md-icon-button"  aria-label="Select All" ng-click="role.selectPermissions( permissionGroup )">
                <md-icon md-svg-icon="img/icons/ic_select_all_white_24px.svg"></md-icon>
            </md-button>

            <md-button class="md-icon-button"  aria-label="Clear All" ng-click="role.unselectPermissions( permissionGroup )">
                <md-icon md-svg-icon="img/icons/ic_clear_white_24px.svg"></md-icon>
            </md-button>
        </md-toolbar>

        <div layout="row">
            <md-list class="md-dense" layout-fill>
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
        </div>
    </div>
</md-content>
