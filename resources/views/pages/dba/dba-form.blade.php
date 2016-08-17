
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.dba_name }">
            <input placeholder="DBA Name" value="" class="form-control" ng-model="dba.currentAccount.dba_name" required="required" name="name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.dba_name" ng-show="dba.formErrors.dba_name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.registrant_name }">
            <input placeholder="Registrant Name" value="" class="form-control" ng-model="dba.currentAccount.registrant_name" required="required" name="registrant_name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.registrant_name" ng-show="dba.formErrors.registrant_name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.address }">
            <input placeholder="Address" value="" class="form-control" ng-model="dba.currentAccount.address" required="required" name="address" type="text">
            <span class="help-block" ng-bind="dba.formErrors.address" ng-show="dba.formErrors.address"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.address_2 }">
            <input placeholder="Address Line 2" value="" class="form-control" ng-model="dba.currentAccount.address_2" required="required" name="address_2" type="text">
            <span class="help-block" ng-bind="dba.formErrors.address_2" ng-show="dba.formErrors.address_2"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.city }">
            <input placeholder="City" value="" class="form-control" ng-model="dba.currentAccount.city" required="required" name="city" type="text">
            <span class="help-block" ng-bind="dba.formErrors.city" ng-show="dba.formErrors.city"></span>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.state }">
            <select ng-model="dba.currentAccount.state" placeholder="Select State" name="state" convert-to-number class="form-control">
                <option  value="">Select a State</option>
                @foreach ( $states as $state )
                    <option ng-selected="dba.currentAccount.state == {{ $state->iso_3166_2 }}" value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.zip }">
            <input placeholder="Zip" value="" class="form-control" ng-model="dba.currentAccount.zip" required="required" name="zip" type="text">
            <span class="help-block" ng-bind="dba.formErrors.zip" ng-show="dba.formErrors.zip"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.email }">
            <input placeholder="Contact Email" value="" class="form-control" ng-model="dba.currentAccount.email" required="required" name="email" type="email">
            <span class="help-block" ng-bind="dba.formErrors.email" ng-show="dba.formErrors.email"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.entity_name }">
            <input placeholder="Entity Name" value="" class="form-control" ng-model="dba.currentAccount.entity_name" required="required" name="entity_name" type="text">
            <span class="help-block" ng-bind="dba.formErrors.entity_name" ng-show="dba.formErrors.entity_name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.phone }">
            <input placeholder="Phone" value="" class="form-control" ng-model="dba.currentAccount.phone" required="required" name="phone" type="text">
            <span class="help-block" ng-bind="dba.formErrors.phone" ng-show="dba.formErrors.phone"></span>
        </div>


        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">P.O. Boxes</h3>
            </div>
            <div class="panel-body">
                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.sub }">
                    <input placeholder="Sub #" value="" class="form-control" ng-model="dba.po_box.sub" required="required" name="sub" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.po_boxes.sub" ng-show="dba.formErrors.po_boxes.sub"></span>
                </div>
                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.address }">
                    <input placeholder="Address" value="" class="form-control" ng-model="dba.po_box.address" required="required" name="name" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.po_boxes.address" ng-show="dba.formErrors.po_boxes.address"></span>
                </div>
                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.address_2 }">
                    <input placeholder="Address Line 2" value="" class="form-control" ng-model="dba.po_box.address_2" required="required" name="name" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.po_boxes.address_2" ng-show="dba.formErrors.po_boxes.address_2"></span>
                </div>
                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.city }">
                    <input placeholder="City" value="" class="form-control" ng-model="dba.po_box.city" required="required" name="name" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.po_boxes.city" ng-show="dba.formErrors.po_boxes.city"></span>
                </div>
                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.state }">
                    <select ng-model="dba.po_box.state" placeholder="Select State" name="state" convert-to-number class="form-control">
                        <option value="">Select a State</option>
                        @foreach ( $states as $state )
                            <option ng-selected="dba.po_box.state == {{ $state->iso_3166_2 }}" value="{{ $state->iso_3166_2 }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.zip }">
                    <input placeholder="Zip" value="" class="form-control" ng-model="dba.po_box.zip" required="required" name="zip" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.po_boxes.zip" ng-show="dba.formErrors.po_boxes.zip"></span>
                </div>

                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.po_box.phone }">
                    <input placeholder="Phone" value="" class="form-control" ng-model="dba.po_box.phone" required="required" name="phone" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.po_boxes.phone" ng-show="dba.formErrors.po_boxes.phone"></span>
                </div>

                <div class="form-group" ng-class="{ 'has-error' : dba.formErrors.brand }">
                    <input placeholder="Brand" value="" class="form-control" ng-model="dba.brand" required="required" name="brand" type="text">
                    <span class="help-block" ng-bind="dba.formErrors.brand" ng-show="dba.formErrors.brand"></span>
                    <input class="btn btn-lg btn-primary btn-sm" ng-click="dba.addBrand()" type="submit" value="Add Brand">
                </div>

                <div ng-show="dba.po_box.brands.length > 0" >
                    <p ng-repeat="(key, value) in dba.po_box.brands track by $index"> @{{value}} <a ng-click="dba.editBrand(key)">Edit</a> <a ng-click="dba.removeBrand(key)">Remove</a> </p>
                </div>

                <div class="form-group col-md-6 col-md-offset-3">
                    <input class="btn btn-lg btn-primary btn-block" ng-click="dba.addPOBox()" type="submit" value="Create P.O. Box">
                </div>
            </div>
            <div ng-show="dba.currentAccount.po_boxes.length > 0" class="panel-footer">
                <p ng-repeat="(key, value) in dba.currentAccount.po_boxes track by $index"> @{{value.sub}} - @{{value.address}} @{{value.address_2}} @{{value.city}} @{{value.state}} @{{value.zip}} @{{value.phone}} @{{value.brands}} <a ng-click="dba.editPOBox(key)">Edit</a> <a ng-click="dba.removePOBox(key)">Remove</a> </p>
            </div>
        </div>