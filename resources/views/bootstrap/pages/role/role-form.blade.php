
<div ng-init="role.loadPermissionTree()">
    <div ivh-treeview="role.permissionTree" ivh-treeview-on-cb-change="role.updateSelectedPermissions()"></div>
    <div class="has-error">
        <div class="help-block" ng-show="role.formErrors.permissions">
            <div ng-repeat="error in role.formErrors.permissions">
                <span ng-bind="error"></span>
            </div>
        </div>
    </div>
</divt>
