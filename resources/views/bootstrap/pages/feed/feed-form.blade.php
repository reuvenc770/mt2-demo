<br/>
<form id="feedForm" name="feedForm" ng-init="feed.loadAutoComplete()" novalidate>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">Feed Settings</div>
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
                <div class="help-block" ng-show="feed.formErrors.status">
                    <div ng-repeat="error in feed.formErrors.status">
                        <div ng-bind="error"></div>
                    </div>
                </div>
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
                <div class="help-block" ng-show="feed.formErrors.check_global_suppression">
                    <div ng-repeat="error in feed.formErrors.check_global_suppression">
                        <div ng-bind="error"></div>
                    </div>
                </div>
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
                <div class="help-block" ng-show="feed.formErrors.has_client_group_restriction">
                    <div ng-repeat="error in feed.formErrors.has_client_group_restriction">
                        <div ng-bind="error"></div>
                    </div>
                </div>
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
                <div class="help-block" ng-show="feed.formErrors.check_previous_oc">
                    <div ng-repeat="error in feed.formErrors.check_previous_oc">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <div class="panel-title">Feed Information</div>
        </div>
        <div class="panel-body">
            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.client_main_name }">
                <input type="text" class="form-control" id="contact" value="" placeholder="Main Contact" required="required" ng-model="feed.current.client_main_name" />
                <div class="help-block" ng-show="feed.formErrors.client_main_name">
                    <div ng-repeat="error in feed.formErrors.client_main_name">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.email_addr }">
                <input type="email" class="form-control" id="email" value="" placeholder="Email" required="required" ng-model="feed.current.email_addr" />
                <div class="help-block" ng-show="feed.formErrors.email_addr">
                    <div ng-repeat="error in feed.formErrors.email_addr">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.username }">
                <input type="text" class="form-control" id="username" value="" placeholder="Feed Name" required="required" ng-model="feed.current.username" />
                <div class="help-block" ng-show="feed.formErrors.username">
                    <div ng-repeat="error in feed.formErrors.username">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.password }">
                <input type="password" class="form-control" id="password" value="" placeholder="Password" required="required" ng-model="feed.current.password" />
                <div class="help-block" ng-show="feed.formErrors.password">
                    <div ng-repeat="error in feed.formErrors.password">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.address }">
                <input type="text" class="form-control" id="address" value="" placeholder="Address" required="required" ng-model="feed.current.address" />
                <div class="help-block" ng-show="feed.formErrors.address">
                    <div ng-repeat="error in feed.formErrors.address">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.address2 }">
                <input type="text" class="form-control" id="address2" value="" placeholder="Apt/Suite" ng-model="feed.current.address2" />
                <div class="help-block" ng-show="feed.formErrors.address2">
                    <div ng-repeat="error in feed.formErrors.address2">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.city }">
                <input type="text" class="form-control" id="city" value="" placeholder="City" required="required" ng-model="feed.current.city" />
                <div class="help-block" ng-show="feed.formErrors.city">
                    <div ng-repeat="error in feed.formErrors.city">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.state }">
                <input type="text" class="form-control" id="state" value="" placeholder="State" maxlength="2" required="required" ng-model="feed.current.state" />
                <div class="help-block" ng-show="feed.formErrors.state">
                    <div ng-repeat="error in feed.formErrors.state">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.zip }">
                <input type="text" class="form-control" id="zip" value="" placeholder="Zip" required="required" ng-model="feed.current.zip" />
                <div class="help-block" ng-show="feed.formErrors.zip">
                    <div ng-repeat="error in feed.formErrors.zip">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.phone }">
                <input type="tel" class="form-control" id="phone" value="" placeholder="Phone" required="required" ng-model="feed.current.phone" />
                <div class="help-block" ng-show="feed.formErrors.phone">
                    <div ng-repeat="error in feed.formErrors.phone">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.network }">
                <input type="text" class="form-control" id="network" value="" placeholder="Network" required="required" ng-model="feed.current.network" />
                <div class="help-block" ng-show="feed.formErrors.network">
                    <div ng-repeat="error in feed.formErrors.network">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-class="{ 'has-error' : feed.formErrors.feed_type }">
                <div angucomplete-alt ng-required="true"
                     id="feed_type"
                     name="feed_type"
                     placeholder="Feed Type"
                     selected-object="feed.getFeedType"
                     selected-object-data="feed.typeSearchText"
                     local-data="feed.feedTypes"
                     local-search="feed.getFeedType"
                     search-fields="name"
                     text-searching="Looking for feed types..."
                     minlength="3"
                     input-class="form-control">
                </div>

                <div class="help-block" ng-show="feed.formErrors.feed_type">
                    <div ng-repeat="error in feed.formErrors.feed_type">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<form id="feedForm" ng-init="feed.loadAutoComplete()" novalidate>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <span>Feed Information</span>
            </div>
        </md-toolbar>
        <md-card-content layout="column">

            <md-autocomplete
                ng-required="true"
                md-input-name="feed_type"
                md-floating-label="Feed Type"
                md-search-text="feed.typeSearchText"
                md-items="item in feed.getFeedType( feed.typeSearchText )"
                md-item-text="item.name"
                md-selected-item-change="feed.setClientType( item )"
                md-min-length="0"
                md-selected-item="feed.current.feed_type">

                <md-item-template>
                    <span md-highlight-text="feed.typeSearchText" md-highlight-flags="^i">@{{item.name}}</span>
                </md-item-template>

                <md-not-found>
                    No feed types matching "@{{feed.typeSearchText}}" were found.
                </md-not-found>
                <div ng-messages="feedForm.feed_type.$error">
                    <div ng-message="required">Feed type is required.</div>
                    <div ng-repeat="error in feed.formErrors.feed_type">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-autocomplete>

        </md-card-content>
    </md-card>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <span>FTP Information</span>
            </div>
        </md-toolbar>
        <md-card-content layout="column">
            <md-input-container>
                <label>FTP URL</label>
                <input type="text" id="ftp_url" readonly name="ftp_url" ng-model="feed.current.ftp_url" />
                <div ng-messages="feedForm.ftp_url.$error">
                    <div ng-repeat="error in feed.formErrors.ftp_url">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>FTP User</label>
                <input type="text" id="ftp_user" readonly name="ftp_user" ng-model="feed.current.ftp_user" />
                <div ng-messages="feedForm.ftp_user.$error">
                    <div ng-repeat="error in feed.formErrors.ftp_user">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>FTP Password</label>
                <input type="text" id="ftp_pw" readonly name="ftp_pw" ng-model="feed.current.ftp_pw" />
                <div ng-messages="feedForm.ftp_pw.$error">
                    <div ng-repeat="error in feed.formErrors.ftp_pw">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>FTP Realtime Password</label>
                <input type="text" id="ftp_realtime_password" readonly name="rt_pw" ng-model="feed.current.rt_pw" />
                <div ng-messages="feedForm.rt_pw.$error">
                    <div ng-repeat="error in feed.formErrors.rt_pw">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
        </md-card-content>
    </md-card>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <span>Tracking</span>
            </div>
        </md-toolbar>
        <md-card-content layout="column">
            <md-input-container>
                <label>Cake Sub Affiliate ID</label>
                <input type="text" id="subaffiliate" name="cake_sub_id" ng-model="feed.current.cake_sub_id" />
                <div ng-messages="feedForm.cake_sub_id.$error">
                    <div ng-repeat="error in feed.formErrors.cake_sub_id">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-autocomplete
                ng-required="true"
                md-input-name="list_owner"
                md-floating-label="List Owner"
                md-search-text="feed.ownerSearchText"
                md-items="item in feed.getListOwners( feed.ownerSearchText )"
                md-item-text="item.name"
                md-selected-item-change="feed.setListOwner( item )"
                md-min-length="0"
                md-selected-item="feed.current.list_owner">

                <md-item-template>
                    <span md-highlight-text="feed.ownerSearchText" md-highlight-flags="^i">@{{item.name}}</span>
                </md-item-template>

                <md-not-found>
                    No list owners matching "@{{feed.ownerSearchText}}" were found.
                </md-not-found>
                <div ng-messages="feedForm.list_owner.$error">
                    <div ng-message="required">List owner is required.</div>
                    <div ng-repeat="error in feed.formErrors.list_owner">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-autocomplete>

            <md-input-container>
                <label>List Owner Type</label>
                <md-select name="clientTypeId" ng-model="feed.current.clientTypeId">
                    <md-option ng-selected="feed.current.clientTypeId == 1" value="1">Internal</md-option>
                    <md-option ng-selected="feed.current.clientTypeId == 2" value="2">Broker</md-option>
                    <md-option ng-selected="feed.current.clientTypeId == 3" value="3">Direct Owner</md-option>
                </md-select>
                <div ng-messages="feedForm.clientTypeId.$error">
                    <div ng-repeat="error in feed.formErrors.clientTypeId">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Source URL</label>
                <input type="text" id="client_record_source_url" name="client_record_source_url" ng-model="feed.current.client_record_source_url" />
                <div ng-messages="feedForm.client_record_source_url.$error">
                    <div ng-repeat="error in feed.formErrors.client_record_source_url">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Source IP</label>
                <input type="text" id="source_ip" name="client_record_ip" ng-model="feed.current.client_record_ip" />
                <div ng-messages="feedForm.client_record_ip.$error">
                    <div ng-repeat="error in feed.formErrors.client_record_ip">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Minimum Record Date</label>
                <input type="text" id="record_date" name="minimum_acceptable_record_date" ng-model="feed.current.minimum_acceptable_record_date" />
                <div ng-messages="feedForm.minimum_acceptable_record_date.$error">
                    <div ng-repeat="error in feed.formErrors.minimum_acceptable_record_date">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Country</label>
                <md-select name="country_id" ng-model="feed.current.country_id" convert-to-number ng-required="true">
                    @foreach ( $countries as $current )
                    <md-option ng-selected="feed.current.country_id == {{ $current->id }}" value="{{ $current->id }}">{{ $current->name }}</md-option>
                    @endforeach
                </md-select>
                <div ng-messages="feedForm.country_id.$error">
                    <div ng-message="required">Country is required.</div>
                    <div ng-repeat="error in feed.formErrors.country_id">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

        </md-card-content>
    </md-card>

</form>
