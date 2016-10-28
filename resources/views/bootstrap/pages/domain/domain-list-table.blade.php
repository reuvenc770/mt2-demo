<md-table-container>
    <table md-table>
        <thead md-head>
        <tr md-row>
            <th md-column></th>
            <th md-column class="md-table-header-override-whitetext mt2-table-header-center" ng-hide="domain.rowBeingEdited != 0" >Status</th>
            <th md-column class="md-table-header-override-whitetext mt2-cell-left-padding" >Domain</th>
            <th md-column class="md-table-header-override-whitetext" ng-if="domain.type ==2" >Proxy</th>
            <th md-column class="md-table-header-override-whitetext" >Registrar</th>
            <th md-column class="md-table-header-override-whitetext" ng-if="domain.type == 1">Mainsite</th>
            <th md-column class="md-table-header-override-whitetext" ng-hide="domain.rowBeingEdited != 0">Created</th>
            <th md-column class="md-table-header-override-whitetext">Expires</th>
            <th md-column class="md-table-header-override-whitetext" ng-hide="domain.rowBeingEdited != 0">DBA</th>
            <th md-column class="md-table-header-override-whitetext">In Use</th>
        </tr>
        </thead>
        <tbody md-body>
        <tr md-row ng-repeat="record in domain.domains track by $index">
            <!--Normal View -->
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" >
                <div layout="row" layout-align="center center">
                    <md-icon ng-if="record.status == 1" ng-click="domain.editRow(record.dom_id)"
                             md-font-set="material-icons" class="mt2-icon-black" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">edit</md-icon>
                    <md-icon ng-if="record.status == 1" ng-click="domain.toggle( record.dom_id, 0 )"
                            aria-label="Pause" data-toggle="tooltip" data-placement="bottom" title="Pause" md-font-set="material-icons"class="mt2-icon-black">pause</md-icon>
                    <md-button ng-if="record.status == 0" ng-click="domain.toggle( record.dom_id, 1 )"
                                class="md-icon-button" aria-label="Activate" data-toggle="tooltip" data-placement="bottom" title="Activate">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                    </md-button>
                </div>
            </td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0" class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">
                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
            </td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" class="mt2-cell-left-padding">@{{ record.domain_name }}</td>
            <td md-cell ng-if="domain.type == 2" ng-hide="domain.beingEdited(record.dom_id)">@{{ record.proxy_name }}</td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)">@{{ record.registrar_name }}</td>
            <td md-cell ng-if="domain.type == 1" ng-hide="domain.beingEdited(record.dom_id)" >@{{ record.main_site }}</td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0" nowrap>@{{ record.created_at }}</td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" nowrap>@{{ record.expires_at }}</td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0">@{{ record.dba_name }}</td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" class="text-center" >
                <md-icon ng-hide="record.in_use" aria-label="No" md-font-set="material-icons" class="mt2-icon-black">cancel</md-icon>
                <md-icon ng-if="record.in_use" aria-label="Yes" md-font-set="material-icons" class="mt2-icon-black">check_circle</md-icon>
            <!--End Normal View -->

            <!--Edit View  -->
            <td md-cell class="mt2-table-cell-center" ng-show="domain.beingEdited(record.dom_id)">
                <div layout="row" layout-align="center center">
                    <md-icon ng-if="record.status == 1" ng-click="domain.editRow(0)"
                             md-font-set="material-icons" class="mt2-icon-black" aria-label="Reset" data-toggle="tooltip" data-placement="bottom" title="Reset">undo</md-icon>
                    <md-icon ng-if="record.status == 1" ng-click="domain.editDomain()"
                            aria-label="Save" data-toggle="tooltip" data-placement="bottom" title="Save" md-font-set="material-icons" class="mt2-icon-black">save</md-icon>
                </div>
            </td>
            <td md-cell ng-show="domain.beingEdited(record.dom_id)">
                <input class="form-control" type="text" placeholder="Domain Name" name="address" ng-required="true" ng-model="domain.currentDomain.domain_name" >
                <div class="help-block"  ng-show="domain.formErrors.domain_name">
                    <div ng-repeat="error in domain.formErrors.domain_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>
            <td md-cell ng-show="domain.beingEdited(record.dom_id)" ng-if="domain.type == 2">
                    <select name="proxy" id="proxy" class="form-control"
                               ng-model="domain.currentDomain.proxy_id">
                        <option ng-repeat="option in domain.proxies" ng-value="option.id">@{{option.name }} - @{{option.ip_addresses}}</option>
                    </select>
                <div class="help-block"  ng-show="domain.formErrors.proxy_id">
                    <div ng-repeat="error in domain.formErrors.proxy_id">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>
            <td md-cell ng-show="domain.beingEdited(record.dom_id)" >
                    <select ng-required="true" class="form-control" name="registrar_id" ng-model="domain.currentDomain.registrar_id">
                        @foreach ( $regs as $reg )
                            <option value="{{ $reg['id'] }}">{{ $reg['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="help-block"  ng-show="domain.formErrors.registrar_id">
                        <div ng-repeat="error in domain.formErrors.registrar_id">
                            <span ng-bind="error"></span>
                        </div>
                    </div>
            </td>
            <td md-cell ng-if="domain.type == 1" ng-show="domain.beingEdited(record.dom_id)" >
                <input class="form-control" type="text" placeholder="Mainsite" name="mainsite"  ng-model="domain.currentDomain.main_site" >
                <div class="help-block"  ng-show="domain.formErrors.main_site">
                    <div ng-repeat="error in domain.formErrors.main_site">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>

            </td>
            <td md-cell ng-show="domain.beingEdited(record.dom_id)" >
                <input class="form-control" type="text" placeholder="Expires At" name="address"  ng-model="domain.currentDomain.expires_at" >
                <div class="help-block"  ng-show="domain.formErrors.expires_at">
                    <div ng-repeat="error in domain.formErrors.expires_at">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>
            <td md-cell ng-show="domain.beingEdited(record.dom_id)" >
                <select ng-required="true" name="in_use" class="form-control"  ng-model="domain.currentDomain.in_use">
                    <option value="">Is Domain in Use?</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>

                </select>
                <div class="help-block"  ng-show="domain.formErrors.in_use">
                    <div ng-repeat="error in domain.formErrors.in_use">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>

            <!--EndEdit View  -->
        </tr>
        </tbody>
    </table>
</md-table-container>
