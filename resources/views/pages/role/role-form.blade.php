<md-card flex>
    <md-content ng-init="role.loadPermissionTree()">
        <div ivh-treeview="role.permissionTree" ivh-treeview-on-cb-change="role.updateSelectedPermissions()"></div>
    </md-content>
</md-card>
