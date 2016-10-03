
    <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <!-- Email field -->
    <md-input-container>
        <label>Registrar Name</label>
        <input type="text" name="name" ng-required="true" ng-model="registrar.currentAccount.name">
        <div ng-messages="registrarForm.name.$error">
            <div ng-message="required">Registrar name is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Username</label>
        <input type="text" name="username" ng-required="true" ng-model="registrar.currentAccount.username">
        <div ng-messages="registrarForm.username.$error">
            <div ng-message="required">Username is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Contact Name</label>
        <input type="text" name="contact_name" ng-required="true" ng-model="registrar.currentAccount.contact_name">
        <div ng-messages="registrarForm.contact_name.$error">
            <div ng-message="required">Contact name is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Contact Email</label>
        <input type="email" name="contact_email" ng-required="true" ng-model="registrar.currentAccount.contact_email">
        <div ng-messages="registrarForm.contact_email.$error">
            <div ng-message="required">Contact email is required.</div>
            <div ng-message="email">Contact email is not in a valid format.</div>
            <div ng-repeat="error in registrar.formErrors.contact_email">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Phone Number</label>
        <input type="text" name="phone_number" ng-required="true" ng-model="registrar.currentAccount.phone_number">
        <div ng-messages="registrarForm.phone_number.$error">
            <div ng-message="required">Phone number is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Last 4 CC Digits</label>
        <input type="text" name="last_cc" ng-required="true" ng-model="registrar.currentAccount.last_cc"
                ng-change="registrar.onFormFieldChange( $event , registrarForm , 'last_cc' )">
        <div ng-messages="registrarForm.last_cc.$error">
            <div ng-message="required">Last 4 CC digits are required.</div>
            <div ng-repeat="error in registrar.formErrors.last_cc">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Contact Credit Card</label>
        <input type="text" name="contact_credit_card" ng-required="true"
                ng-model="registrar.currentAccount.contact_credit_card">
        <div ng-messages="registrarForm.contact_credit_card.$error">
            <div ng-message="required">Contact for credit card is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Address</label>
        <input type="text" name="address" ng-required="true" ng-model="registrar.currentAccount.address">
        <div ng-messages="registrarForm.address.$error">
            <div ng-message="required">Address is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Address 2</label>
        <input type="text" name="address_2" ng-model="registrar.currentAccount.address_2">
        <div ng-messages="registrarForm.address_2.$error">
            <div ng-repeat="error in registrar.formErrors.address_2">
                <div ng-bind="error"></div>
            </div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>City</label>
        <input type="text" name="city" ng-required="true" ng-model="registrar.currentAccount.city">
        <div ng-messages="registrarForm.city.$error">
            <div ng-message="required">City is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>State</label>
        <md-select name="state" ng-required="true" convert-to-number ng-model="registrar.currentAccount.state">
            @foreach ( $states as $state )
                <md-option ng-selected="registrar.currentAccount.state == {{ $state->iso_3166_2 }}" value="{{ $state->iso_3166_2 }}">{{ $state->name }}</md-option>
            @endforeach
        </md-select>
        <div ng-messages="registrarForm.state.$error">
            <div ng-message="required">State is required.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Zip Code</label>
        <input type="text" name="zip" ng-required="true" ng-model="registrar.currentAccount.zip" ng-pattern="/^[0-9]{5}$/">
        <div ng-messages="registrarForm.zip.$error">
            <div ng-message="required">Zip code is required.</div>
            <div ng-message="pattern">Must be 5 digits.</div>
        </div>
    </md-input-container>
    <md-input-container>
        <label>Entity Name</label>
        <input type="text" name="entity_name" ng-required="true" ng-model="registrar.currentAccount.entity_name">
        <div ng-messages="registrarForm.entity_name.$error">
            <div ng-message="required">Entity name is required.</div>
        </div>
    </md-input-container>

    <md-button class="md-raised md-accent" ng-click="registrar.saveNewAccount( $event , registrarForm )" ng-show="registrar.pageType == 'add'">Create Registrar</md-button>
    <md-button class="md-raised md-accent" ng-click="registrar.editAccount( $event , registrarForm )" ng-show="registrar.pageType == 'edit'">Update Registrar</md-button>
