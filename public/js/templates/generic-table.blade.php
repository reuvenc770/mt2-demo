<table class="table table-striped table-bordered table-hover text-center">
    <thead>
        <tr>
            <th class="text-center" ng-repeat="header in ctrl.headers track by $index">{{ header }}</th>
        </tr>
    </thead>

    <tbody>
        <tr ng-repeat="record in ctrl.records track by $index">
            <td>
                <?php if(Sentinel::hasAccess('role.add')){

                ?>
                <edit-button recordid="record[0]" editurl="ctrl.editurl"></edit-button>

                   <?php }
                ?>
            </td>
            <td ng-repeat="field in record track by $index">{{ field }}</td>
        </tr>
    </tbody>
</table>
