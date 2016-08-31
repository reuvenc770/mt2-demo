<form id="feedForm" ng-init="feed.loadAutoComplete()">
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
            <h3 class="panel-title">Feed Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_main_name }">
                <input type="text" class="form-control" id="contact" value="" placeholder="Main Contact" required="required" ng-model="feed.current.client_main_name" />
                <span class="help-block" ng-bind="feed.formErrors.client_main_name" ng-show="feed.formErrors.client_main_name"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.email_addr }">
                <input type="email" class="form-control" id="email" value="" placeholder="Email" required="required" ng-model="feed.current.email_addr" />
                <span class="help-block" ng-bind="feed.formErrors.email_addr" ng-show="feed.formErrors.email_addr"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.username }">
                <input type="text" class="form-control" id="username" value="" placeholder="Feed Name" required="required" ng-model="feed.current.username" />
                <span class="help-block" ng-bind="feed.formErrors.username" ng-show="feed.formErrors.username"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.password }">
                <input type="password" class="form-control" id="password" value="" placeholder="Password" required="required" ng-model="feed.current.password" />
                <span class="help-block" ng-bind="feed.formErrors.password" ng-show="feed.formErrors.password"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.address }">
                <input type="text" class="form-control" id="address" value="" placeholder="Address" required="required" ng-model="feed.current.address" />
                <span class="help-block" ng-bind="feed.formErrors.address" ng-show="feed.formErrors.address"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.address2 }">
                <input type="text" class="form-control" id="address2" value="" placeholder="Apt/Suite" ng-model="feed.current.address2" />
                <span class="help-block" ng-bind="feed.formErrors.address2" ng-show="feed.formErrors.address2"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.city }">
                <input type="text" class="form-control" id="city" value="" placeholder="City" required="required" ng-model="feed.current.city" />
                <span class="help-block" ng-bind="feed.formErrors.city" ng-show="feed.formErrors.city"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.state }">
                <input type="text" class="form-control" id="state" value="" placeholder="State" maxlength="2" required="required" ng-model="feed.current.state" />
                <span class="help-block" ng-bind="feed.formErrors.state" ng-show="feed.formErrors.state"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.zip }">
                <input type="text" class="form-control" id="zip" value="" placeholder="Zip" required="required" ng-model="feed.current.zip" />
                <span class="help-block" ng-bind="feed.formErrors.zip" ng-show="feed.formErrors.zip"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.phone }">
                <input type="tel" class="form-control" id="phone" value="" placeholder="Phone" required="required" ng-model="feed.current.phone" />
                <span class="help-block" ng-bind="feed.formErrors.phone" ng-show="feed.formErrors.phone"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.network }">
                <input type="text" class="form-control" id="network" value="" placeholder="Network" required="required" ng-model="feed.current.network" />
                <span class="help-block" ng-bind="feed.formErrors.network" ng-show="feed.formErrors.network"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_type }">
                <div layout="column" ng-cloak>
                    <md-content class="autocompleteSelect">
                        <md-autocomplete
                            md-search-text="feed.typeSearchText"
                            md-items="item in feed.getClientType( feed.typeSearchText )"
                            md-item-text="item.name"

                            md-selected-item-change="feed.setClientType( item )"
                            md-min-length="0"
                            placeholder="Type to Choose a Feed Type"
                            md-selected-item="feed.current.client_type">

                            <md-item-template>
                                <span md-highlight-text="feed.typeSearchText" md-highlight-flags="^i">@{{item.name}}</span>
                            </md-item-template>

                            <md-not-found>
                                No Feed Types matching "@{{feed.typeSearchText}}" were found.
                            </md-not-found>
                        </md-autocomplete>
                    </md-content>
                </div>
                <span class="help-block" ng-bind="feed.formErrors.client_type" ng-show="feed.formErrors.client_type"></span>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">FTP Information</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.ftp_url }">
                <input type="text" readonly class="form-control" id="ftp_url" value="" placeholder="FTP URL" ng-model="feed.current.ftp_url" />
                <span class="help-block" ng-bind="feed.formErrors.ftp_url" ng-show="feed.formErrors.ftp_url"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.ftp_user }">
                <input type="text" readonly class="form-control" id="ftp_user" value="" placeholder="FTP User" ng-model="feed.current.ftp_user" />
                <span class="help-block" ng-bind="feed.formErrors.ftp_user" ng-show="feed.formErrors.ftp_user"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.ftp_pw }">
                <input type="text" readonly class="form-control" id="ftp_password" value="" placeholder="FTP Password" ng-model="feed.current.ftp_pw" />
                <span class="help-block" ng-bind="feed.formErrors.ftp_pw" ng-show="feed.formErrors.ftp_pw"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.rt_pw }">
                <input type="text" readonly class="form-control" id="ftp_realtime_password" value="" placeholder="FTP Realtime Password" ng-model="feed.current.rt_pw" />
                <span class="help-block" ng-bind="feed.formErrors.rt_pw" ng-show="feed.formErrors.rt_pw"></span>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Tracking</h3>
        </div>

        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.cake_sub_id }">
                <input type="text" class="form-control" id="subaffiliate" placeholder="Cake Sub Affiliate ID" ng-model="feed.current.cake_sub_id" />
                <span class="help-block" ng-bind="feed.formErrors.cake_sub_id" ng-show="feed.formErrors.cake_sub_id"></span>
            </div>
            <div class="clearfix"></div>
            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.list_owner }">
                <div layout="column" ng-cloak>
                    <md-content class="autocompleteSelect">
                        <md-autocomplete
                            md-search-text="feed.ownerSearchText"
                            md-items="item in feed.getListOwners( feed.ownerSearchText )"
                            md-item-text="item.name"
                            md-selected-item-change="feed.setListOwner( item )"
                            md-min-length="0"
                            placeholder="Type to Choose a List Owner"
                            md-selected-item="feed.current.list_owner">

                            <md-item-template>
                                <span md-highlight-text="feed.ownerSearchText" md-highlight-flags="^i">@{{item.name}}</span>
                            </md-item-template>

                            <md-not-found>
                                No Feed Types matching "@{{feed.ownerSearchText}}" were found.
                            </md-not-found>
                        </md-autocomplete>
                    </md-content>
                </div>
                <span class="help-block" ng-bind="feed.formErrors.list_owner" ng-show="feed.formErrors.list_owner"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.clientTypeId }">
                <select ng-model="feed.current.clientTypeId" class="form-control">
                    <option value="">Select a List Owner</option>
                        <option ng-selected="feed.current.clientTypeId == 1" value="1">Internal</option>
                    <option ng-selected="feed.current.clientTypeId == 2" value="2">Broker</option>
                        <option ng-selected="feed.current.clientTypeId == 3" value="3">Direct Owner</option>
                </select>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_record_source_url }">
                <input type="text" class="form-control" id="client_record_source_url" value="" placeholder="Source URL" ng-model="feed.current.client_record_source_url" />
                <span class="help-block" ng-bind="feed.formErrors.client_record_source_url" ng-show="feed.formErrors.client_record_source_url"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_record_ip }">
                <input type="text" class="form-control" id="source_ip" value="" placeholder="Source IP" ng-model="feed.current.client_record_ip" />
                <span class="help-block" ng-bind="feed.formErrors.client_record_ip" ng-show="feed.formErrors.client_record_ip"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.current.minimum_acceptable_record_date }">
                <input type="text" class="form-control" id="record_date" value="" placeholder="Minimum Record Date" ng-model="feed.current.minimum_acceptable_record_date" />
                <span class="help-block" ng-bind="feed.formErrors.current.minimum_acceptable_record_date" ng-show="feed.formErrors.current.minimum_acceptable_record_date"></span>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.country_id }">
                <select ng-model="feed.current.country_id" placeholder="Select Country"  convert-to-number class="form-control">
                    <option ng-selected="@{{ feed.current.country_id == '' || feed.current.country_id == null }}" value="">Select a Country</option>
                    @foreach ( $countries as $current )
                    <option ng-selected="feed.current.country_id == {{ $current->id }}" value="{{ $current->id }}">{{ $current->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>
