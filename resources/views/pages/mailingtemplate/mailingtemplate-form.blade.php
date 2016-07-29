
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <!-- Email field -->
        <div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.name }">
            <input placeholder="name" value="{{old('name') }}" class="form-control" ng-model="mailing.currentAccount.name" required="required" name="name" type="text">
            <span class="help-block" ng-bind="mailing.formErrors.name" ng-show="mailing.formErrors.name"></span>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.templateType }">
            <select ng-model="mailing.currentAccount.templateType" placeholder="Select Mailing Type" name="templateType"  class="form-control">
                <option  value="">Select Mailing Type</option>
                <option ng-selected="mailing.currentAccount.templateType == 1" value="1">Normal HTML</option>
                <option ng-selected="mailing.currentAccount.templateType == 2" value="2">HTML Lite (no images)</option>
                <option ng-selected="mailing.currentAccount.templateType == 3" value="3">Images Only</option>
                <option ng-selected="mailing.currentAccount.templateType == 4" value="4">Image Map</option>
                <option ng-selected="mailing.currentAccount.templateType == 5" value="5">Newsletter</option>
                <option ng-selected="mailing.currentAccount.templateType == 6" value="6">Clickable Button</option>
            </select>
            <span class="help-block" ng-bind="mailing.formErrors.templateType" ng-show="mailing.formErrors.templateType"></span>
        </div>

        <div  class="form-group" ng-class="{ 'has-error' : mailing.formErrors.selectedEsps }">
            <lite-membership-widget recordlist="mailing.espList" chosenrecordlist="mailing.selectedEsps" availablerecordtitle="mailing.availableWidgetTitle" chosenrecordtitle="mailing.chosenWidgetTitle" idfield="mailing.espIdField" namefield="mailing.espNameField" updatecallback="mailing.espMembershipCallback()" ></lite-membership-widget>
            <span class="help-block" ng-bind="mailing.formErrors.selectedEsps" ng-show="mailing.formErrors.selectedEsps"></span>
        </div>

        <div class="col-md-6"  class="form-group" ng-class="{ 'has-error' : mailing.formErrors.html }">
            <h4>HTML VERSION</h4>
            <div class="form-group" >
                <textarea ng-model="mailing.currentAccount.html" class="form-control" rows="15" id="html"></textarea>
            </div>
            <span class="help-block" ng-bind="mailing.formErrors.html" ng-show="mailing.formErrors.html"></span>
        </div>
        <div class="col-md-6">
            <h4>TEXT VERSION</h4>
            <div class="form-group">
                <textarea ng-model="mailing.currentAccount.text" class="form-control" rows="15" id="text"></textarea>
            </div>
        </div>
