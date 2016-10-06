<md-table-container>
    <table md-table>
        <thead md-head>
        <tr md-row>
            <th md-column class="md-table-header-override-whitetext"></th>
            <th ng-hide="domain.rowBeingEdited != 0" md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
            <th md-column class="md-table-header-override-whitetext mt2-cell-left-padding">Domain</th>
            <th ng-if="domain.type ==2" md-column class="md-table-header-override-whitetext">Proxy</th>
            <th md-column class="md-table-header-override-whitetext">Registrar</th>
            <th ng-if="domain.type == 1" md-column class="md-table-header-override-whitetext">Mainsite</th>
            <th ng-hide="domain.rowBeingEdited != 0" md-column class="md-table-header-override-whitetext">Created</th>
            <th md-column class="md-table-header-override-whitetext">Expires</th>
            <th ng-hide="domain.rowBeingEdited != 0" md-column class="md-table-header-override-whitetext">DBA</th>
            <th ng-show="domain.rowBeingEdited != 0" md-column class=""></th>
            <th ng-show="domain.rowBeingEdited != 0" md-column class=""></th>
            <th ng-show="domain.rowBeingEdited != 0" md-column class=""></th>
        </tr>
        </thead>

        <tbody md-body>
        <tr md-row ng-repeat="record in domain.domains track by $index">
            <!--Normal View -->
            <td ng-hide="domain.beingEdited(record.dom_id)" md-cell>
                <div layout="row" layout-align="center center">
                    <md-button ng-if="record.status == 1" class="md-raised md-accent mt2-button-xs" ng-click="domain.editRow(record.dom_id)">Edit</md-button>
                    <md-button ng-if="record.status == 1" class="md-icon-button" ng-click="domain.toggle( record.dom_id, 0 )">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                        <md-tooltip md-direction="bottom">Deactivate</md-tooltip>
                    </md-button>
                    <md-button ng-if="record.status == 0" class="md-icon-button" ng-click="domain.toggle( record.dom_id, 1 )">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                        <md-tooltip md-direction="bottom">Activate</md-tooltip>
                    </md-button>
                </div>
            </td>
            <td ng-hide="domain.beingEdited(record.dom_id)" md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 1 , 'mt2-bg-danger' : record.status == 0 }">
                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
            </td>
            <td ng-hide="domain.beingEdited(record.dom_id)"  md-cell class="mt2-cell-left-padding">@{{ record.domain_name }}</td>
            <td ng-if="domain.type == 2" ng-hide="domain.beingEdited(record.dom_id)" md-cell>@{{ record.proxy_name }}</td>
            <td ng-hide="domain.beingEdited(record.dom_id)" md-cell>@{{ record.registrar_name }}</td>
            <td ng-if="domain.type == 1" ng-hide="domain.beingEdited(record.dom_id)" md-cell>@{{ record.main_site }}</td>
            <td ng-hide="domain.beingEdited(record.dom_id)" md-cell>@{{ record.created_at }}</td>
            <td ng-hide="domain.beingEdited(record.dom_id)" md-cell>@{{ record.expires_at }}</td>
            <td ng-hide="domain.beingEdited(record.dom_id)" md-cell>@{{ record.dba_name }}</td>
            <!--End Normal View -->

            <!--Edit View  -->
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
                <div layout="row" layout-align="center center">
                    <md-button ng-if="record.status == 1" class="md-raised md-accent mt2-button-xs" ng-click="domain.editRow(0)">Reset</md-button>
                    <md-button ng-if="record.status == 1" class="md-raised md-accent mt2-button-xs" ng-click="domain.editDomain()">Save</md-button>
            </div>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
                <md-input-container>
                    <label>Domain Name</label>
                    <input type="text" name="address" ng-required="true" ng-model="domain.currentDomain.domain_name" />
                    <div ng-messages="domain.domain_name.$error">
                        <div ng-message="required">Name is required.</div>
                    </div>
                </md-input-container>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell  ng-if="domain.type == 2">
                <md-input-container>
                    <label>Proxy</label>
                    <md-select name="proxy" id="proxy"
                               ng-model="domain.currentDomain.proxy_id">
                        <md-option ng-repeat="option in domain.proxies" ng-value="option.id">@{{option.name }} - @{{option.ip_addresses}}</md-option>
                    </md-select>
                    <div ng-messages="domainForm.proxy_id.$error">
                        <div ng-repeat="error in domain.formErrors.proxies">
                            <div ng-bind="error"></div>
                        </div>
                    </div>
                </md-input-container>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
                <md-input-container>
                    <label>Registrar</label>
                    <md-select ng-required="true" name="registrar_id" ng-model="domain.currentDomain.registrar_id">
                        @foreach ( $regs as $reg )
                            <md-option value="{{ $reg['id'] }}">{{ $reg['name'] }}</md-option>
                        @endforeach
                    </md-select>
                    <div ng-messages="domainForm.registrar_id.$error">
                        <div ng-message="required">Registrar is required.</div>
                    </div>
                </md-input-container>
            </td>
            <td ng-if="domain.type == 1" ng-show="domain.beingEdited(record.dom_id)" md-cell>
                <md-input-container>
                    <label>Mainsite</label>
                    <input type="text" name="mainsite" ng-required="true" ng-model="domain.currentDomain.main_site" />
                    <div ng-messages="domain.main_site.$error">
                        <div ng-message="required">Mainsite is required.</div>
                    </div>
                </md-input-container>
            </td>

            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
                <md-input-container>
                    <label>Expiration Date</label>
                    <input type="text" name="expires_at" ng-required="true" ng-model="domain.currentDomain.expires_at" />
                    <div ng-messages="domain.expires_at.$error">
                        <div ng-message="required">Expiration Date is required.</div>
                    </div>
                </md-input-container>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" md-cell>
            </td>
            <!--EndEdit View  -->
        </tr>
        </tbody>
    </table>
</md-table-container>
