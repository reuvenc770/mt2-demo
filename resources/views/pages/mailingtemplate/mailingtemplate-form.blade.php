<form name="mailingForm" layout="column" novalidate>
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <!-- Email field -->
        <md-input-container>
            <label>Name</label>
            <input type="text" name="name" ng-required="true" ng-model="mailing.currentAccount.name" value="{{old('name') }}" ng-change="mailing.change( mailingForm , 'name' )" />
            <div ng-messages="mailingForm.name.$error">
                <div ng-message="required">Mailing template name is required.</div>
                <div ng-repeat="error in mailing.formErrors.name">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </md-input-container>

        <md-input-container>
            <label>Mailing Type</label>
            <md-select name="templateType" ng-required="true" ng-model="mailing.currentAccount.templateType">
                <md-option ng-selected="mailing.currentAccount.templateType == 1" value="1">Normal HTML</md-option>
                <md-option ng-selected="mailing.currentAccount.templateType == 2" value="2">HTML Lite (no images)</md-option>
                <md-option ng-selected="mailing.currentAccount.templateType == 3" value="3">Images Only</md-option>
                <md-option ng-selected="mailing.currentAccount.templateType == 4" value="4">Image Map</md-option>
                <md-option ng-selected="mailing.currentAccount.templateType == 5" value="5">Newsletter</md-option>
                <md-option ng-selected="mailing.currentAccount.templateType == 6" value="6">Clickable Button</md-option>
            </md-select>
            <div ng-messages="mailingForm.templateType.$error">
                <div ng-message="required">Template type is required.</div>
            </div>
        </md-input-container>

        <div>
            <lite-membership-widget recordlist="mailing.espList" chosenrecordlist="mailing.selectedEsps" availablerecordtitle="mailing.availableWidgetTitle" chosenrecordtitle="mailing.chosenWidgetTitle" idfield="mailing.espIdField" namefield="mailing.espNameField" updatecallback="mailing.espMembershipCallback()" ></lite-membership-widget>

            <span class="mt2-error-message" ng-bind="mailing.formErrors.selectedEsps" ng-show="mailing.formErrors.selectedEsps">
            </span>
        </div>

        <div layout="column" layout-gt-sm="row">
            <md-card flex="auto">
                <md-card-content layout="column">
                <md-input-container>
                    <label>HTML Version</label>
                    <textarea ng-model="mailing.currentAccount.html" ng-required="true" name="html" rows="15" id="html"></textarea>
                    <div ng-messages="mailingForm.html.$error">
                        <div ng-message="required">HTML version is required.</div>
                    </div>
                </md-input-container>
                </md-card-content>
            </md-card>
            <md-card flex="auto">
                <md-card-content layout="column">
                <md-input-container>
                    <label>Text Version</label>
                    <textarea ng-model="mailing.currentAccount.text" name="text" rows="15" id="text"></textarea>
                </md-input-container>
                </md-card-content>
            </md-card>
        </div>
</form>
