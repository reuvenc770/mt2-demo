<form id="clientForm" ng-init="client.loadAutoComplete()">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Client Settings</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label>Status</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.status" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.status = 'A'" ng-class="{ active : client.current.status == 'A' }">Active</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.status = 'D'" ng-class="{ active : client.current.status != 'A' }">Inactive</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Global Suppression</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.check_global_suppression" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_global_suppression = 'Y'" ng-class="{ active : client.current.check_global_suppression == 'Y' }">On</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_global_suppression = 'N'" ng-class="{ active : client.current.check_global_suppression != 'Y' }">Off</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Group Restriction</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.has_client_group_restriction" />
                    <input type="hidden" ng-model="client.current.client_has_client_group_restrictions" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.has_client_group_restriction = 1; client.current.client_has_client_group_restrictions = 1;" ng-class="{ active : client.current.has_client_group_restriction == 1 }">Yes</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.has_client_group_restriction = 0; client.current.client_has_client_group_restrictions = 0;" ng-class="{ active : client.current.has_client_group_restriction != 1 }">No</button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Filter By Historical OC</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="client.current.check_previous_oc" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_previous_oc = 1" ng-class="{ active : client.current.check_previous_oc == 1 }">Yes</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="client.current.check_previous_oc = 0" ng-class="{ active : client.current.check_previous_oc != 1 }">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Client Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <input type="text" class="form-control" id="contact" value="" placeholder="Main Contact" required="required" ng-model="client.current.client_main_name" />
            </div>

            <div class="form-group">
                <input type="email" class="form-control" id="email" value="" placeholder="Email" required="required" ng-model="client.current.email_addr" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="username" value="" placeholder="Client Name" required="required" ng-model="client.current.username" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" value="" placeholder="Password" required="required" ng-model="client.current.password" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="address" value="" placeholder="Address" required="required" ng-model="client.current.address" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="address2" value="" placeholder="Apt/Suite" ng-model="client.current.address2" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="city" value="" placeholder="City" required="required" ng-model="client.current.city" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="state" value="" placeholder="State" maxlength="2" required="required" ng-model="client.current.state" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="zip" value="" placeholder="Zip" required="required" ng-model="client.current.zip" />
            </div>

            <div class="form-group">
                <input type="tel" class="form-control" id="phone" value="" placeholder="Phone" required="required" ng-model="client.current.phone" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="network" value="" placeholder="Network" required="required" ng-model="client.current.network" />
            </div>

            <div class="form-group">
                <div layout="column" ng-cloak>
                    <md-content>
                        <md-autocomplete
                            md-search-text="client.typeSearchText"
                            md-items="item in client.getClientType( client.typeSearchText )"
                            md-item-text="item.name"
                            md-selected-item-change="client.setClientType( item )"
                            md-min-length="0"
                            placeholder="Choose a Client Type"
                            md-selected-item="client.current.client_type">

                            <md-item-template>
                                <span md-highlight-text="client.typeSearchText" md-highlight-flags="^i">@{{item.name}}</span>
                            </md-item-template>

                            <md-not-found>
                                No Client Types matching "@{{client.typeSearchText}}" were found.
                            </md-not-found>
                        </md-autocomplete>
                    </md-content>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">FTP Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <input type="text" class="form-control" id="ftp_url" value="" placeholder="FTP URL" required="required" ng-model="client.current.ftp_url" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="ftp_user" value="" placeholder="FTP User" required="required" ng-model="client.current.ftp_user" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="ftp_password" value="" placeholder="FTP Password" ng-model="client.current.ftp_pw" />
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="ftp_realtime_password" value="" placeholder="FTP Realtime Password" ng-model="client.current.rt_pw" />
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Tracking</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <input type="text" class="form-control" id="subaffiliate" placeholder="Cake Sub Affiliate ID" ng-model="client.current.cake_sub_id" />
            </div>

            <div class="clearfix"></div>

            <div class="form-group">
                <div layout="column" ng-cloak>
                    <md-content>
                        <md-autocomplete
                            md-search-text="client.ownerSearchText"
                            md-items="item in client.getListOwners( client.ownerSearchText )"
                            md-item-text="item.name"
                            md-selected-item-change="client.setListOwner( item )"
                            md-min-length="0"
                            placeholder="Choose a List Owner"
                            md-selected-item="client.current.list_owner">

                            <md-item-template>
                                <span md-highlight-text="client.ownerSearchText" md-highlight-flags="^i">@{{item.name}}</span>
                            </md-item-template>

                            <md-not-found>
                                No Client Types matching "@{{client.ownerSearchText}}" were found.
                            </md-not-found>
                        </md-autocomplete>
                    </md-content>
                </div>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="client_record_source_url" value="" placeholder="Source URL" ng-model="client.current.client_record_source_url" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="source_ip" value="" placeholder="Source IP" ng-model="client.current.client_record_ip" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="record_date" value="" placeholder="Minimum Record Date" ng-model="client.current.minimum_acceptable_record_date" />
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="country_id" value="" placeholder="Country ID" ng-model="client.current.country_id" />
            </div>
        </div>
    </div>
</form>
