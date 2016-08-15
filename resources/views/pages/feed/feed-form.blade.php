<form id="clientForm" ng-init="feed.loadAutoComplete()">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Feed Settings</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.status }">
                <label>Status</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="feed.current.status" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.status = 'A'" ng-class="{ active : feed.current.status == 'A' }">Active</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.status = 'D'" ng-class="{ active : feed.current.status != 'A' }">Inactive</button>
                    </div>
                </div>
                <span class="help-block" ng-bind="feed.formErrors.country_id" ng-show="feed.formErrors.status"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.check_global_suppression}">
                <label>Global Suppression</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="feed.current.check_global_suppression" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.check_global_suppression = 'Y'" ng-class="{ active : feed.current.check_global_suppression == 'Y' }">On</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.check_global_suppression = 'N'" ng-class="{ active : feed.current.check_global_suppression != 'Y' }">Off</button>
                    </div>
                </div>
                <span class="help-block" ng-bind="feed.formErrors.country_id" ng-show="feed.formErrors.check_global_suppression"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_has_client_group_restrictions }">
                <label>Group Restriction</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="feed.current.has_client_group_restriction" />
                    <input type="hidden" ng-model="feed.current.client_has_client_group_restrictions" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.has_client_group_restriction = 1; feed.current.client_has_client_group_restrictions = 1;" ng-class="{ active : feed.current.has_client_group_restriction == 1 }">Yes</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.has_client_group_restriction = 0; feed.current.client_has_client_group_restrictions = 0;" ng-class="{ active : feed.current.has_client_group_restriction != 1 }">No</button>
                    </div>
                </div>
                <span class="help-block" ng-bind="feed.formErrors.country_id" ng-show="feed.formErrors.client_has_client_group_restrictions"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.check_previous_oc }">
                <label>Filter By Historical OC</label>
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <input type="hidden" ng-model="feed.current.check_previous_oc" />

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.check_previous_oc = 1" ng-class="{ active : feed.current.check_previous_oc == 1 }">Yes</button>
                    </div>

                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default" ng-click="feed.current.check_previous_oc = 0" ng-class="{ active : feed.current.check_previous_oc != 1 }">No</button>
                    </div>
                </div>
                <span class="help-block" ng-bind="feed.formErrors.country_id" ng-show="feed.formErrors.check_previous_oc"></span>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Client Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.client_main_name }">
                <input type="text" class="form-control" id="contact" value="" placeholder="Main Contact" required="required" ng-model="client.current.client_main_name" />
                <span class="help-block" ng-bind="client.formErrors.client_main_name" ng-show="client.formErrors.client_main_name"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.email_addr }">
                <input type="email" class="form-control" id="email" value="" placeholder="Email" required="required" ng-model="client.current.email_addr" />
                <span class="help-block" ng-bind="client.formErrors.email_addr" ng-show="client.formErrors.email_addr"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.username }">
                <input type="text" class="form-control" id="username" value="" placeholder="Client Name" required="required" ng-model="client.current.username" />
                <span class="help-block" ng-bind="client.formErrors.username" ng-show="client.formErrors.username"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.password }">
                <input type="password" class="form-control" id="password" value="" placeholder="Password" required="required" ng-model="client.current.password" />
                <span class="help-block" ng-bind="client.formErrors.password" ng-show="client.formErrors.password"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.address }">
                <input type="text" class="form-control" id="address" value="" placeholder="Address" required="required" ng-model="client.current.address" />
                <span class="help-block" ng-bind="client.formErrors.address" ng-show="client.formErrors.address"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.address2 }">
                <input type="text" class="form-control" id="address2" value="" placeholder="Apt/Suite" ng-model="client.current.address2" />
                <span class="help-block" ng-bind="client.formErrors.address2" ng-show="client.formErrors.address2"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.city }">
                <input type="text" class="form-control" id="city" value="" placeholder="City" required="required" ng-model="client.current.city" />
                <span class="help-block" ng-bind="client.formErrors.city" ng-show="client.formErrors.city"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.state }">
                <input type="text" class="form-control" id="state" value="" placeholder="State" maxlength="2" required="required" ng-model="client.current.state" />
                <span class="help-block" ng-bind="client.formErrors.state" ng-show="client.formErrors.state"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.zip }">
                <input type="text" class="form-control" id="zip" value="" placeholder="Zip" required="required" ng-model="client.current.zip" />
                <span class="help-block" ng-bind="client.formErrors.zip" ng-show="client.formErrors.zip"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.phone }">
                <input type="tel" class="form-control" id="phone" value="" placeholder="Phone" required="required" ng-model="client.current.phone" />
                <span class="help-block" ng-bind="client.formErrors.phone" ng-show="client.formErrors.phone"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.network }">
                <input type="text" class="form-control" id="network" value="" placeholder="Network" required="required" ng-model="client.current.network" />
                <span class="help-block" ng-bind="client.formErrors.network" ng-show="client.formErrors.network"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.client_type }">
                <div layout="column" ng-cloak>
                    <md-content class="autocompleteSelect">
                        <md-autocomplete
                            md-search-text="client.typeSearchText"
                            md-items="item in client.getClientType( client.typeSearchText )"
                            md-item-text="item.name"

                            md-selected-item-change="client.setClientType( item )"
                            md-min-length="0"
                            placeholder="Type to Choose a Client Type"
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
                <span class="help-block" ng-bind="client.formErrors.client_type" ng-show="client.formErrors.client_type"></span>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">FTP Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.ftp_url }">
                <input type="text" readonly class="form-control" id="ftp_url" value="" placeholder="FTP URL" ng-model="client.current.ftp_url" />
                <span class="help-block" ng-bind="client.formErrors.ftp_url" ng-show="client.formErrors.ftp_url"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.ftp_user }">
                <input type="text" readonly class="form-control" id="ftp_user" value="" placeholder="FTP User" ng-model="client.current.ftp_user" />
                <span class="help-block" ng-bind="client.formErrors.ftp_user" ng-show="client.formErrors.ftp_user"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.ftp_pw }">
                <input type="text" readonly class="form-control" id="ftp_password" value="" placeholder="FTP Password" ng-model="client.current.ftp_pw" />
                <span class="help-block" ng-bind="client.formErrors.ftp_pw" ng-show="client.formErrors.ftp_pw"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.rt_pw }">
                <input type="text" readonly class="form-control" id="ftp_realtime_password" value="" placeholder="FTP Realtime Password" ng-model="client.current.rt_pw" />
                <span class="help-block" ng-bind="client.formErrors.rt_pw" ng-show="client.formErrors.rt_pw"></span>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Tracking</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.cake_sub_id }">
                <input type="text" class="form-control" id="subaffiliate" placeholder="Cake Sub Affiliate ID" ng-model="client.current.cake_sub_id" />
                <span class="help-block" ng-bind="client.formErrors.cake_sub_id" ng-show="client.formErrors.cake_sub_id"></span>
            </div>
            <div class="clearfix"></div>
            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.list_owner }">
                <div layout="column" ng-cloak>
                    <md-content class="autocompleteSelect">
                        <md-autocomplete
                            md-search-text="client.ownerSearchText"
                            md-items="item in client.getListOwners( client.ownerSearchText )"
                            md-item-text="item.name"
                            md-selected-item-change="client.setListOwner( item )"
                            md-min-length="0"
                            placeholder="Type to Choose a List Owner"
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
                <span class="help-block" ng-bind="client.formErrors.list_owner" ng-show="client.formErrors.list_owner"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.clientTypeId }">
                <select ng-model="client.current.clientTypeId" class="form-control">
                    <option value="">Select a List Owner</option>
                        <option ng-selected="client.current.clientTypeId == 1" value="1">Internal</option>
                    <option ng-selected="client.current.clientTypeId == 2" value="2">Broker</option>
                        <option ng-selected="client.current.clientTypeId == 3" value="3">Direct Owner</option>
                </select>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.client_record_source_url }">
                <input type="text" class="form-control" id="client_record_source_url" value="" placeholder="Source URL" ng-model="client.current.client_record_source_url" />
                <span class="help-block" ng-bind="client.formErrors.client_record_source_url" ng-show="client.formErrors.client_record_source_url"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.client_record_ip }">
                <input type="text" class="form-control" id="source_ip" value="" placeholder="Source IP" ng-model="client.current.client_record_ip" />
                <span class="help-block" ng-bind="client.formErrors.client_record_ip" ng-show="client.formErrors.client_record_ip"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.current.minimum_acceptable_record_date }">
                <input type="text" class="form-control" id="record_date" value="" placeholder="Minimum Record Date" ng-model="client.current.minimum_acceptable_record_date" />
                <span class="help-block" ng-bind="client.formErrors.current.minimum_acceptable_record_date" ng-show="client.formErrors.current.minimum_acceptable_record_date"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.country_id }">
                <select ng-model="client.current.country_id" placeholder="Select Country"  convert-to-number class="form-control">
                    <option ng-selected="@{{ client.current.country_id == '' || client.current.country_id == null }}" value="">Select a Country</option>
                    @foreach ( $countries as $current )
                    <option ng-selected="client.current.country_id == {{ $current->id }}" value="{{ $current->id }}">{{ $current->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Client Payout Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.payout_type }">
                <select ng-model="client.current.payout_type" placeholder="Select Type" required="required" class="form-control">
                    <option ng-selected="@{{ client.current.payout_type == '' || client.current.payout_type == null }}" value="">Select a Payout Type</option>
                    @foreach ( $payoutTypes as $payout )
                    <option ng-selected="client.current.payout_type == {{$payout['id']}}" value="{{ $payout['id'] }}">{{ $payout['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : client.formErrors.payout_amount }">
                <input type="text" class="form-control" id="payout-amount" value="" placeholder="Payout Amount" required="required" ng-model="client.current.payout_amount" />
                <span class="help-block" ng-bind="client.formErrors.payout_amount" ng-show="client.formErrors.payout_amount"></span>
            </div>
        </div>
    </div>
</form>
