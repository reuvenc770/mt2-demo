<input name="_token" type="hidden" value="{{ csrf_token() }}">
<!-- Email field -->
<div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.name }">
    <input placeholder="Domain Name" value="" class="form-control" ng-model="mailing.currentAccount.name" required="required" name="name" type="text">
    <div class="help-block" ng-show="mailing.formErrors.name">
        <div ng-repeat="error in mailing.formErrors.name">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>
<div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.templateType }">
    <select ng-model="mailing.currentAccount.templateType"  name="templateType"  class="form-control">
        <option value="">Select Domain Group</option>
        <option value="1">Normal HTML</option>
        <option value="2">HTML Lite (no images)</option>
        <option value="3">Images Only</option>
        <option value="4">Image Map</option>
        <option value="5">Newsletter</option>
        <option value="6">Clickable Button</option>
    </select>
    <div class="help-block"  ng-show="mailing.formErrors.templateType">
        <div ng-repeat="error in mailing.formErrors.templateType">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>

<div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.selectedEsps }">
    <lite-membership-widget recordlist="mailing.espList" chosenrecordlist="mailing.selectedEsps"
                            availablerecordtitle="mailing.availableWidgetTitle"
                            chosenrecordtitle="mailing.chosenWidgetTitle" idfield="mailing.espIdField"
                            namefield="mailing.espNameField"
                            height="200"
                            updatecallback="mailing.espMembershipCallback()"></lite-membership-widget>

            <span class="mt2-error-message" ng-bind="mailing.formErrors.selectedEsps"
                  ng-show="mailing.formErrors.selectedEsps">
            </span>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.html }">
        <textarea rows="20"  placeholder="HTML VERSION" value="" class="form-control" ng-model="mailing.currentAccount.html"
                  name="notes"></textarea>
            <div class="help-block" ng-show="mailing.formErrors.html">
                <div ng-repeat="error in mailing.formErrors.html">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group" ng-class="{ 'has-error' : mailing.formErrors.text }">
        <textarea rows="20" placeholder="TEXT VERSION" value="" class="form-control" ng-model="mailing.currentAccount.text"
                  name="notes"></textarea>
            <div class="help-block" ng-show="mailing.formErrors.text">
                <div ng-repeat="error in mailing.formErrors.text">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
    </div>
</div>