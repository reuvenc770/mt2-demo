<md-table-container>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th></th>
            <th ng-hide="domain.rowBeingEdited != 0" >Status</th>
            <th >Domain</th>
            <th ng-if="domain.type ==2" >Proxy</th>
            <th >Registrar</th>
            <th ng-if="domain.type == 1">Mainsite</th>
            <th ng-hide="domain.rowBeingEdited != 0">Created</th>
            <th>Expires</th>
            <th ng-hide="domain.rowBeingEdited != 0">DBA</th>
            <th>Live A-Record</th>
        </tr>
        </thead>
        <tbody>
        <tr  ng-repeat="record in domain.domains track by $index" ng-class="{ 'success' : record.status == 1 , 'danger' : record.status == 0 }">
            <!--Normal View -->
            <td ng-hide="domain.beingEdited(record.dom_id)" >
                <button ng-if="record.status == 1" class="btn btn-sm btn-primary" ng-click="domain.editRow(record.dom_id)">Edit</button>
                <button ng-if="record.status == 1" class="btn btn-sm btn-danger" ng-click="domain.toggle( record.dom_id, 0 )">Pause</button>
                <button ng-if="record.status == 0" class="btn btn-sm btn-success" ng-click="domain.toggle( record.dom_id, 1 )">Activate</button>
            </td>
            <td  ng-hide="domain.rowBeingEdited != 0"   >
                @{{ record.status == 1 ? 'Active' : 'Inactive' }}
            </td>
            <td ng-hide="domain.beingEdited(record.dom_id)"  >@{{ record.domain_name }}</td>
            <td ng-if="domain.type == 2" ng-hide="domain.beingEdited(record.dom_id)">@{{ record.proxy_name }}</td>
            <td ng-hide="domain.beingEdited(record.dom_id)">@{{ record.registrar_name }}</td>
            <td ng-if="domain.type == 1" ng-hide="domain.beingEdited(record.dom_id)" >@{{ record.main_site }}</td>
            <td ng-hide="domain.rowBeingEdited != 0" ng-bind="app.formatDate( record.created_at , 'MM-DD-YY' )"></td>
            <td ng-hide="domain.beingEdited(record.dom_id)" ng-bind="::app.formatDate( record.expires_at , 'MM-DD-YY' )"></td>
            <td ng-hide="domain.rowBeingEdited != 0">@{{ record.dba_name }}</td>
            <td ng-hide="domain.beingEdited(record.dom_id)" class="text-center" >
                <span ng-hide="record.live_a_record" class="glyphicon glyphicon-remove-sign"></span>
                <span ng-if="record.live_a_record" class="glyphicon glyphicon-ok-sign"></span>
            <!--End Normal View -->

            <!--Edit View  -->
            <td ng-show="domain.beingEdited(record.dom_id)">

                    <button ng-if="record.status == 1" class="btn btn-sm btn-warning" ng-click="domain.editRow(0)">Reset</button>
                    <button ng-if="record.status == 1" class="btn btn-sm btn-success" ng-click="domain.editDomain()">Save</button>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)">
                <input class="form-control" type="text" placeholder="Domain Name" name="address" ng-required="true" ng-model="domain.currentDomain.domain_name" >
                <div class="help-block"  ng-show="domain.formErrors.domain_name">
                    <div ng-repeat="error in domain.formErrors.domain_name">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" ng-if="domain.type == 2">
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
            <td ng-show="domain.beingEdited(record.dom_id)" >
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
            <td ng-if="domain.type == 1" ng-show="domain.beingEdited(record.dom_id)" >
                <input class="form-control" type="text" placeholder="Mainsite" name="mainsite"  ng-model="domain.currentDomain.main_site" >
                <div class="help-block"  ng-show="domain.formErrors.main_site">
                    <div ng-repeat="error in domain.formErrors.main_site">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>

            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" >
                <input class="form-control" type="text" placeholder="Expires At" name="address"  ng-model="domain.currentDomain.expires_at" >
                <div class="help-block"  ng-show="domain.formErrors.expires_at">
                    <div ng-repeat="error in domain.formErrors.expires_at">
                        <span ng-bind="error"></span>
                    </div>
                </div>
            </td>
            <td ng-show="domain.beingEdited(record.dom_id)" >
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
