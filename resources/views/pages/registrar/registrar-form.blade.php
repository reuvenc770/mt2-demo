
    <input name="_token" type="hidden" value="{{ csrf_token() }}">
    <fieldset>
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.name }">
            <input placeholder="Registrar Name"  class="form-control"
                   ng-model="registrar.currentAccount.name" required="required" name="name" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.name" ng-show="registrar.formErrors.name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.username }">
            <input placeholder="Username"  class="form-control"
                   ng-model="registrar.currentAccount.username" required="required" name="username" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.username"
                  ng-show="registrar.formErrors.username"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_name }">
            <input placeholder="Contact Name"  class="form-control"
                   ng-model="registrar.currentAccount.contact_name" required="required" name="registrar_name" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.contact_name"
                  ng-show="registrar.formErrors.contact_name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_email }">
            <input placeholder="Contact Email"  class="form-control"
                   ng-model="registrar.currentAccount.contact_email" required="required" name="contact_email" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.contact_email"
                  ng-show="registrar.formErrors.contact_email"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.phone_number }">
            <input placeholder="Phone Number"  class="form-control"
                   ng-model="registrar.currentAccount.phone_number" required="required" name="phone_number" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.phone_number"
                  ng-show="registrar.formErrors.phone_number"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.last_cc }">
            <input placeholder="Last 4 CC Digits"  class="form-control"
                   ng-model="registrar.currentAccount.last_cc" required="required" name="last_cc" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.last_cc"
                  ng-show="registrar.formErrors.last_cc"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.contact_credit_card }">
            <input placeholder="Contact Credit Card"  class="form-control"
                   ng-model="registrar.currentAccount.contact_credit_card" required="required" name="contact_credit_card" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.contact_credit_card"
                  ng-show="registrar.formErrors.contact_credit_card"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.address }">
            <input placeholder="Address" value="" class="form-control" ng-model="registrar.currentAccount.address" required="required" name="address" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.address" ng-show="registrar.formErrors.address"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.address_2 }">
            <input placeholder="Address Line 2" value="" class="form-control" ng-model="registrar.currentAccount.address_2" required="required" name="address_2" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.address_2" ng-show="registrar.formErrors.address_2"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.city }">
            <input placeholder="City" value="" class="form-control" ng-model="registrar.currentAccount.city" required="required" name="city" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.city" ng-show="registrar.formErrors.city"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.state }">
            <select ng-model="registrar.currentAccount.state" placeholder="Select State" name="state" convert-to-number class="form-control">
                <option  value="">Select a State</option>
                @foreach ( $states as $state )
                    <option ng-selected="registrar.currentAccount.state == {{ $state->iso_3166_2 }}" value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                @endforeach
            </select>
            <span class="help-block" ng-bind="registrar.formErrors.state" ng-show="registrar.formErrors.state"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.zip }">
            <input placeholder="Zip" value="" class="form-control" ng-model="registrar.currentAccount.zip" required="required" name="zip" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.zip" ng-show="registrar.formErrors.zip"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : registrar.formErrors.entity_name }">
            <input placeholder="Entity Email"  class="form-control"
                   ng-model="registrar.currentAccount.entity_name" required="required" name="entity_name" type="text">
            <span class="help-block" ng-bind="registrar.formErrors.entity_name"
                  ng-show="registrar.formErrors.entity_name"></span>
        </div>

        <div class="form-group">
            <input class="btn btn-lg btn-primary btn-block" ng-click="registrar.saveNewAccount()" type="submit" value="Create Registrar" ng-show="registrar.pageType == 'add'">
            <input class="btn btn-lg btn-primary btn-block" ng-click="registrar.editAccount()" type="submit" value="Edit Registrar" ng-show="registrar.pageType == 'edit'">
        </div>
    </fieldset>


