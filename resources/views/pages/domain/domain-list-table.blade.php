
<md-table-container>
    <table md-table>
        <thead md-head class="mt2-theme-thead">
        <tr md-row>
            <th md-column class="mt2-table-btn-column"></th>
            <th md-column class="md-table-header-override-whitetext mt2-table-header-center" ng-hide="domain.rowBeingEdited != 0" >Status</th>
            <th md-column class="md-table-header-override-whitetext mt2-cell-left-padding" ng-hide="domain.rowBeingEdited != 0">Type</th>
            <th md-column class="md-table-header-override-whitetext" >Domain</th>
            <th md-column class="md-table-header-override-whitetext" ng-if="domain.type ==2" >Proxy</th>
            <th md-column class="md-table-header-override-whitetext" >Registrar</th>
            <th md-column class="md-table-header-override-whitetext" ng-if="domain.type == 1">Mainsite</th>
            <th md-column class="md-table-header-override-whitetext" ng-hide="domain.rowBeingEdited != 0">Created</th>
            <th md-column class="md-table-header-override-whitetext">Expires</th>
            <th md-column class="md-table-header-override-whitetext" ng-hide="domain.rowBeingEdited != 0">DBA</th>
            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Live A-Record</th>
        </tr>
        </thead>
        <tbody md-body>
        <tr md-row ng-repeat="record in domain.domains track by $index">
            <!--Normal View -->
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" class="mt2-table-btn-column">
                <div layout="row" layout-align="center center">
                    <md-icon ng-if="record.status == 1" ng-click="domain.editRow(record.dom_id)"
                             md-font-set="material-icons" class="mt2-icon-black" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">edit</md-icon>
                    <md-icon ng-if="record.status == 1" ng-click="domain.toggle( record.dom_id, 0 )"
                            aria-label="Pause" data-toggle="tooltip" data-placement="bottom" title="Pause" md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                    <md-icon ng-if="record.status == 0" ng-click="domain.toggle( record.dom_id, 1 )"
                                aria-label="Activate" data-toggle="tooltip" data-placement="bottom" title="Activate" md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                </div>
            </td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0" class="mt2-table-cell-center" ng-class="{ 'bg-success' : record.status == 1 , 'bg-danger' : record.status == 0 }">
                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
            </td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0" class="mt2-cell-left-padding">@{{ ( record.type ? ( record.type == 1 ? 'Mailing' : 'Content' )  : ( domain.type == 1 ? 'Mailing' : 'Content' )  ) }}</td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)">@{{ record.domain_name }}</td>
            <td md-cell ng-if="domain.type == 2" ng-hide="domain.beingEdited(record.dom_id)">@{{ record.proxy_name }}</td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" nowrap>@{{ record.registrar_name }} - @{{ record.registrar_username }}</td>
            <td md-cell ng-if="domain.type == 1" ng-hide="domain.beingEdited(record.dom_id)" >@{{ record.main_site }}</td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0" ng-bind="app.formatDate( record.created_at , 'MM-DD-YY' )" nowrap></td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" nowrap>
                <span ng-bind="::app.formatDate( record.expires_at , 'MM-DD-YY' )"></span>
                <md-icon ng-show="@{{ record.is_expired }}" md-font-set="material-icons" class="mt2-icon-warning" data-toggle="tooltip" data-placement="bottom" title="Expired or expiring soon.">warning</md-icon>
            </td>
            <td md-cell ng-hide="domain.rowBeingEdited != 0" nowrap>@{{ record.dba_name }}</td>
            <td md-cell ng-hide="domain.beingEdited(record.dom_id)" class="mt2-table-cell-center">
                <md-icon ng-show="record.live_a_record == 0" aria-label="No" md-font-set="material-icons" class="mt2-icon-black" data-toggle="tooltip" data-placement="bottom" title="Not Live">clear</md-icon>
                <md-icon ng-show="record.live_a_record == 1" aria-label="Yes" md-font-set="material-icons" class="mt2-icon-black" data-toggle="tooltip" data-placement="bottom" title="Live">check</md-icon>
            </td>
            <!--End Normal View -->

            <!--Edit View  -->
            <td md-cell class="mt2-table-cell-center" ng-show="domain.beingEdited(record.dom_id)">
                <div layout="row" layout-align="center center">
                    <md-icon ng-if="record.status == 1" ng-click="domain.editRow(0)"
                             md-font-set="material-icons" class="mt2-icon-black" aria-label="Reset" data-toggle="tooltip" data-placement="bottom" title="Reset">undo</md-icon>
                    <md-icon ng-if="record.status == 1" ng-click="domain.editDomain()" ng-disabled="domain.formSubmitted"
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
                        @foreach ( $proxies as $proxy )
                            <option value="{{ $proxy['id'] }}">{{ $proxy['name'] }} &nbsp;-&nbsp; {{ $proxy['ip_addresses'] }}</option>
                        @endforeach
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
                            <option value="{{ $reg['id'] }}">{{ $reg['name'] }} &nbsp;-&nbsp; {{ $reg['username'] }}</option>
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
                <select ng-required="true" name="live_a_record" class="form-control"  ng-model="domain.currentDomain.live_a_record">
                    <option value="">Is Domain in Use?</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
                <div class="help-block"  ng-show="domain.formErrors.live_a_record">
                    <div ng-repeat="error in domain.formErrors.live_a_record">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>

            <!--EndEdit View  -->
        </tr>
        </tbody>
    </table>
</md-table-container>
