<form id="feedForm" name="feedForm" ng-init="feed.loadAutoComplete()" novalidate>
    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <span>Feed Settings</span>
            </div>
        </md-toolbar>
        <md-card-content>
            <md-input-container layout="column">
                <p class="bold-text">Status</p>
                <input type="hidden" name="status" ng-required="true" ng-model="feed.current.status" />
                <div layout="row" layout-align="center center">
                    <md-button class="md-raised mt2-button-option mt2-button-option-left" flex="50" ng-click="feed.current.status = 'A'" ng-disabled="feed.current.status == 'A'">Active</md-button>
                    <md-button class="md-raised mt2-button-option mt2-button-option-right" flex="50" ng-click="feed.current.status = 'D'" ng-disabled="feed.current.status != 'A'">Inactive</md-button>
                </div>
                <div ng-messages="feedForm.status.$error">
                    <div ng-repeat="error in feed.formErrors.status">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container layout="column">
                <p class="bold-text">Global Suppression</p>
                <input type="hidden" name="check_global_suppression" ng-required="true" ng-model="feed.current.check_global_suppression" />
                <div layout="row" layout-align="center center">
                    <md-button class="md-raised mt2-button-option mt2-button-option-left" flex="50" ng-click="feed.current.check_global_suppression = 'Y'" ng-disabled="feed.current.check_global_suppression == 'Y'">On</md-button>
                    <md-button class="md-raised mt2-button-option mt2-button-option-right" flex="50" ng-click="feed.current.check_global_suppression = 'N'" ng-disabled="feed.current.check_global_suppression != 'Y'">Off</md-button>
                </div>
                <div ng-messages="feedForm.check_global_suppression.$error">
                    <div ng-repeat="error in feed.formErrors.check_global_suppression">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container layout="column">
                <p class="bold-text">Group Restriction</p>
                <input type="hidden" ng-model="feed.current.has_client_group_restriction" />
                <input type="hidden" ng-model="feed.current.client_has_client_group_restrictions" />
                <div layout="row" layout-align="center center">
                    <md-button class="md-raised mt2-button-option mt2-button-option-left" flex="50"
                                ng-click="feed.current.has_client_group_restriction = 1; feed.current.client_has_client_group_restrictions = 1;"
                                ng-disabled="feed.current.has_client_group_restriction == 1">Yes</md-button>
                    <md-button class="md-raised mt2-button-option mt2-button-option-right" flex="50"
                                ng-click="feed.current.has_client_group_restriction = 0; feed.current.client_has_client_group_restrictions = 0;"
                                ng-disabled="feed.current.has_client_group_restriction != 1">No</md-button>
                </div>
                <div ng-messages="feedForm.client_has_client_group_restrictions.$error">
                    <div ng-repeat="error in feed.formErrors.client_has_client_group_restrictions">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container layout="column">
                <p class="bold-text">Filter By Historical OC</p>
                <input type="hidden" ng-model="feed.current.check_previous_oc" />
                <div layout="row" layout-align="center center">
                    <md-button class="md-raised mt2-button-option mt2-button-option-left" flex="50"
                                ng-click="feed.current.check_previous_oc = 1;"
                                ng-disabled="feed.current.check_previous_oc == 1">Yes</md-button>
                    <md-button class="md-raised mt2-button-option mt2-button-option-right" flex="50"
                                ng-click="feed.current.check_previous_oc = 0;"
                                ng-disabled="feed.current.check_previous_oc != 1">No</md-button>
                </div>
                <div ng-messages="feedForm.check_previous_oc.$error">
                    <div ng-repeat="error in feed.formErrors.check_previous_oc">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

        </md-card-content>
    </md-card>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <span>Feed Information</span>
            </div>
        </md-toolbar>
        <md-card-content layout="column">
            <md-input-container>
                <label>Main Contact</label>
                <input type="text" id="contact" name="contact" ng-required="true" ng-model="feed.current.client_main_name" />
                <div ng-messages="feedForm.contact.$error">
                    <div ng-message="required">Main contact name is required.</div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Email</label>
                <input type="email" id="email" name="email" ng-required="true" ng-model="feed.current.email_addr" />
                <div ng-messages="feedForm.email.$error">
                    <div ng-message="required">Contact email is required.</div>
                    <div ng-message="email">Contact email is not in a valid format.</div>
                    <div ng-repeat="error in feed.formErrors.email_addr">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Feed Name</label>
                <input type="text" id="username" name="username" ng-required="true" ng-model="feed.current.username" />
                <div ng-messages="feedForm.username.$error">
                    <div ng-message="required">Feed name is required.</div>
                    <div ng-repeat="error in feed.formErrors.username">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Password</label>
                <input type="password" id="password" name="password" ng-required="true" ng-model="feed.current.password" />
                <div ng-messages="feedForm.password.$error">
                    <div ng-message="required">Password is required.</div>
                    <div ng-repeat="error in feed.formErrors.password">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Address</label>
                <input type="text" id="address" name="address" ng-required="true" ng-model="feed.current.address" />
                <div ng-messages="feedForm.address.$error">
                    <div ng-message="required">Feed street address is required.</div>
                    <div ng-repeat="error in feed.formErrors.address">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Apt/Suite #</label>
                <input type="text" id="address2" name="address2" ng-model="feed.current.address2" />
                <div ng-messages="feedForm.address2.$error">
                    <div ng-repeat="error in feed.formErrors.address2">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>City</label>
                <input type="text" id="city" name="city" ng-required="true" ng-model="feed.current.city" />
                <div ng-messages="feedForm.city.$error">
                    <div ng-message="required">Feed city is required.</div>
                    <div ng-repeat="error in feed.formErrors.city">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>State</label>
                <input type="text" id="state" name="state" ng-required="true" ng-model="feed.current.state" ng-pattern="/^[a-zA-Z]{2}$/" />
                <div ng-messages="feedForm.state.$error">
                    <div ng-message="required">Feed state is required.</div>
                    <div ng-message="pattern">Feed state must be formatted correctly (e.g. "NY")</div>
                    <div ng-repeat="error in feed.formErrors.state">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Zip Code</label>
                <input type="text" id="zip" name="zip" ng-required="true" ng-model="feed.current.zip" ng-pattern="/^\d{5}$/" />
                <div ng-messages="feedForm.zip.$error">
                    <div ng-message="required">Feed zip code is required.</div>
                    <div ng-message="pattern">Feed zip code must be 5 digits.</div>
                    <div ng-repeat="error in feed.formErrors.zip">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Phone Number</label>
                <input type="text" id="phone" name="phone" ng-required="true" ng-model="feed.current.phone" />
                <div ng-messages="feedForm.phone.$error">
                    <div ng-message="required">Feed phone number is required.</div>
                    <div ng-repeat="error in feed.formErrors.phone">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container>
                <label>Network</label>
                <input type="text" id="network" name="network" ng-required="true" ng-model="feed.current.network" />
                <div ng-messages="feedForm.network.$error">
                    <div ng-message="required">Feed network is required.</div>
                    <div ng-repeat="error in feed.formErrors.network">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>

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
