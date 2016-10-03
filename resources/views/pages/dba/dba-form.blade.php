
    <input name="_token" type="hidden" value="{{ csrf_token() }}">
    <md-input-container>
        <label>DBA Name</label>
        <input type="text" name="dba_name" ng-required="true" ng-model="dba.currentAccount.dba_name" ng-change="dba.onFormFieldChange( $event , dbaForm , 'dba_name' )"/>
        <div ng-messages="dbaForm.dba_name.$error">
            <div ng-message="required">DBA name is required.</div>
            <div ng-repeat="error in dba.formErrors.dba_name">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Registrant Name</label>
        <input type="text" name="registrant_name" ng-required="true" ng-model="dba.currentAccount.registrant_name" />
        <div ng-messages="dbaForm.registrant_name.$error">
            <div ng-message="required">Registrant name is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Address</label>
        <input type="text" name="address" ng-required="true" ng-model="dba.currentAccount.address" />
        <div ng-messages="dbaForm.address.$error">
            <div ng-message="required">Address is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Address Line 2</label>
        <input type="text" name="address_2" ng-model="dba.currentAccount.address_2" />
        <div ng-messages="dbaForm.address_2.$error">
            <div ng-repeat="error in dba.formErrors.address_2">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>City</label>
        <input type="text" name="city" ng-required="true" ng-model="dba.currentAccount.city" />
        <div ng-messages="dbaForm.city.$error">
            <div ng-message="required">City is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>State</label>
        <md-select name="state" ng-required="true" convert-to-number ng-model="dba.currentAccount.state">
            @foreach ( $states as $state )
                <md-option ng-selected="dba.currentAccount.state == {{ $state->iso_3166_2 }}" value="{{ $state->iso_3166_2 }}">{{ $state->name }}</md-option>
            @endforeach
        </md-select>
        <div ng-messages="dbaForm.state.$error">
            <div ng-message="required">State is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Zip Code</label>
        <input type="text" name="zip" ng-required="true" ng-model="dba.currentAccount.zip" ng-pattern="/^[0-9]{5}$/" />
        <div ng-messages="dbaForm.zip.$error">
            <div ng-message="required">Zip code is required.</div>
            <div ng-message="pattern">Must be 5 digits.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Contact Email</label>
        <input type="email" name="dba_email" ng-required="true" ng-model="dba.currentAccount.dba_email" />
        <div ng-messages="dbaForm.dba_email.$error">
            <div ng-message="required">Contact email is required.</div>
            <div ng-message="email">Email is not in a valid format.</div>
            <div ng-repeat="error in dba.formErrors.dba_email">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Entity Name</label>
        <input type="text" name="entity_name" ng-required="true" ng-model="dba.currentAccount.entity_name" />
        <div ng-messages="dbaForm.entity_name.$error">
            <div ng-message="required">Entity name is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Phone Number</label>
        <input type="text" name="phone" ng-required="true" ng-model="dba.currentAccount.phone" />
        <div ng-messages="dbaForm.phone.$error">
            <div ng-message="required">Phone number is required.</div>
        </div>
    </md-input-container>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools"><span>P.O. Boxes</span></div>
        </md-toolbar>
        <md-card-content layout="column">
            <md-input-container>
                <label>Sub #</label>
                <input type="text" name="po_box_sub" ng-model="dba.po_box.sub" />
                <div ng-messages="dbaForm.po_box_sub.$error">
                    <div ng-repeat="error in dba.formErrors.po_boxes.sub">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <md-input-container>
                <label>Address</label>
                <input type="text" name="po_box_address" ng-model="dba.po_box.address" />
                <div ng-messages="dbaForm.po_box_address.$error">
                    <div ng-repeat="error in dba.formErrors.po_boxes.address">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <md-input-container>
                <label>Address Line 2</label>
                <input type="text" name="po_box_address_2" ng-model="dba.po_box.address_2" />
                <div ng-messages="dbaForm.po_box_address_2.$error">
                    <div ng-repeat="error in dba.formErrors.po_boxes.address_2">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <md-input-container>
                <label>City</label>
                <input type="text" name="po_box_city" ng-model="dba.po_box.city" />
                <div ng-messages="dbaForm.po_box_city.$error">
                    <div ng-repeat="error in dba.formErrors.po_boxes.city">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <md-input-container>
                <label>State</label>
                <md-select name="po_box_state" convert-to-number ng-model="dba.po_box.state">
                    @foreach ( $states as $state )
                        <md-option ng-selected="dba.po_box.state == {{ $state->iso_3166_2 }}" value="{{ $state->iso_3166_2 }}">{{ $state->name }}</md-option>
                    @endforeach
                </md-select>
                <div ng-messages="dbaForm.po_box_state.$error">
                    <div ng-repeat="error in dba.formErrors.po_boxes.state">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <md-input-container>
                <label>Zip Code</label>
                <input type="text" name="po_box_zip" ng-model="dba.po_box.zip" ng-pattern="/^[0-9]{5}$/" />
                <div ng-messages="dbaForm.po_box_zip.$error">
                    <div ng-message="pattern">Must be 5 digits.</div>
                    <div ng-repeat="error in dba.formErrors.po_boxes.zip">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <md-input-container>
                <label>Phone Number</label>
                <input type="text" name="po_box_phone" ng-model="dba.po_box.phone" />
                <div ng-messages="dbaForm.po_box_phone.$error">
                    <div ng-repeat="error in dba.formErrors.po_boxes.phone">
                        <div ng-bind="error"></div>
                    </div>
                </div>
            </md-input-container>
            <div layout="row">
                <md-input-container flex>
                    <label>Brand</label>
                    <input type="text" name="po_box_brand" ng-model="dba.brand"/>
                    <div ng-messages="dbaForm.po_box_brand.$error">
                        <div ng-repeat="error in dba.formErrors.brand">
                            <div ng-bind="error"></div>
                        </div>
                    </div>
                </md-input-container>
                <div>
                    <md-button class="md-raised md-accent" flex="auto" ng-click="dba.addBrand()">Add Brand</md-button>
                </div>
            </div>
            <div ng-show="dba.po_box.brands.length > 0" >
                <p ng-repeat="(key, value) in dba.po_box.brands track by $index"> @{{value}} <a ng-click="dba.editBrand(key)">Edit</a> <a ng-click="dba.removeBrand(key)">Remove</a> </p>
            </div>

            <md-button class="md-raised md-accent" ng-click="dba.addPOBox( $event , dbaForm )">
                <span ng-show="!dba.editingPOBox">Create </span>
                <span ng-show="dba.editingPOBox">Update </span>P.O. Box
            </md-button>

        </md-card-content>

        <md-content ng-show="dba.currentAccount.po_boxes.length > 0" layout-padding>
            <md-list class="md-dense">
                <md-list-item class="md-3-line" ng-repeat="(key, value) in dba.poBoxHolder track by $index">
                    <div class="md-list-item-text">
                        <p><strong>PO Box:</strong> <span ng-if="value.sub">(Sub# @{{value.sub}})</span> @{{value.address}} @{{value.address_2}} @{{value.city}} @{{value.state}} @{{value.zip}}</p>
                        <p>@{{value.phone}}</p>
                        <p><span ng-if="value.brands.length > 0"><strong>Brands: </strong></span>@{{ value.brands.join(', ') }}</p>
                    </div>
                        <md-button class="md-secondary md-icon-button" aria-label="Edit" ng-click="dba.editPOBox(key)">
                            <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                            <md-tooltip md-direction="bottom">Edit</md-tooltip>
                        </md-button>
                        <md-button class="md-secondary md-icon-button" aria-label="Delete" ng-click="dba.removePOBox(key)">
                            <md-icon md-svg-icon="img/icons/ic_clear_black_24px.svg"></md-icon>
                            <md-tooltip md-direction="bottom">Delete</md-tooltip>
                        </md-button>
                </md-list-item>
            </md-list>
        </md-content>
    </md-card>

    <md-input-container>
        <label>Notes</label>
        <textarea ng-model="dba.currentAccount.notes" rows="5" id="notes"></textarea>
    </md-input-container>
