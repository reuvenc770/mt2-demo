<br/>
<div class="panel panel-primary">
  <div class="panel-heading">
    <div class="panel-title">Data Cleanse Conditions</div>
  </div>
  <div class="panel-body">
    <fieldset>
        <div class="form-group" ng-class="{ 'has-error' : cleanse.formErrors.pname }">
            <select name="pname" ng-model="cleanse.current.pname" class="form-control" required ng-required="true">
                <option value="">Choose a Data Export File</option>
                @foreach ( $dataExportFiles as $file )
                <option value="{{ str_replace( 'Incoming/' , '' , $file ) }}">{{ str_replace( 'Incoming/' , '' , $file ) }}</option>
                @endforeach
            </select>
            <div class="help-block" ng-show="cleanse.formErrors.pname">
                <div ng-repeat="error in cleanse.formErrors.pname">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : cleanse.formErrors.outname }">
            <div class="input-group">
                <div class="input-group-addon no-padding">
                    <md-icon md-font-set="material-icons" class="mt2-icon-black">insert_drive_file</md-icon>
                </div>
                <input placeholder="Output Filename" value="" class="form-control" name="outname" ng-model="cleanse.current.outname" type="text">
            </div>
            <div class="help-block" ng-show="cleanse.formErrors.outname">
                <div ng-repeat="error in cleanse.formErrors.outname">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : cleanse.formErrors.suppname }">
            <div class="input-group">
                <div class="input-group-addon no-padding">
                    <md-icon md-font-set="material-icons" class="mt2-icon-black">clear</md-icon>
                </div>
                <input placeholder="Suppression Filename" value="" class="form-control" name="suppname" ng-model="cleanse.current.suppname" type="text">
            </div>
            <div class="help-block" ng-show="cleanse.formErrors.outname">
                <div ng-repeat="error in cleanse.formErrors.outname">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : cleanse.formErrors.ConfirmEmail }">
            <label>Confirmation Email</label>
            <div class="radio">
                <label>
                    <input type="radio" value="alphateam@zetainteractive.com" ng-model="cleanse.current.ConfirmEmail">
                    alphateam@zetainteractive.com
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" value="betateam@zetainteractive.com" ng-model="cleanse.current.ConfirmEmail">
                    betateam@zetainteractive.com
                </label>
            </div>
            <div class="help-block" ng-show="cleanse.formErrors.ConfirmEmail">
                <div ng-repeat="error in cleanse.formErrors.ConfirmEmail">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : cleanse.formErrors.includeHeaders }">
            <label>Include Headers</label>
            <div class="radio">
            <label class="radio-inline">
                <input type="radio" value="Y" ng-model="cleanse.current.includeHeaders">
                Yes
            </label>
            <label class="radio-inline">
                <input type="radio" value="N" ng-model="cleanse.current.includeHeaders">
                No
            </label>
            </div>
            <div class="help-block" ng-show="cleanse.formErrors.includeHeaders">
                <div ng-repeat="error in cleanse.formErrors.includeHeaders">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>
        <div id="suppressionOffers" ng-cloak>
            <h4 class="text-center">Suppression Offer Categories</h4>

            <md-divider></md-divider>

            <lite-membership-widget recordlist="cleanse.offerCategories" chosenrecordlist="cleanse.selectedOfferCategories" availablerecordtitle="cleanse.availableCategoryWidgetTitle" chosenrecordtitle="cleanse.chosenCategoryWidgetTitle" updatecallback="cleanse.offerCategoryMembershipCallback()" ng-init="cleanse.loadOfferCategories()"></lite-membership-widget>
        </div>

        <br/>

        <div id="suppressionCountries" ng-cloak>
            <h4 class="text-center">Suppression Countries</h4>

            <md-divider></md-divider>

            <lite-membership-widget recordlist="cleanse.countries" chosenrecordlist="cleanse.selectedCountries" availablerecordtitle="cleanse.availableCountryWidgetTitle" chosenrecordtitle="cleanse.chosenCountryWidgetTitle" updatecallback="cleanse.countryMembershipCallback()" ng-init="cleanse.loadCountries()"></lite-membership-widget>
        </div>

        <br/>

        <div id="suppressionAdvertisers" ng-class="{ 'has-error' : cleanse.formErrors.aid }" ng-cloak>
            <h4 class="text-center">Advertiser Suppression</h4>

            <md-divider></md-divider>

            <lite-membership-widget recordlist="cleanse.advertisers" chosenrecordlist="cleanse.selectedAdvertisers" availablerecordtitle="cleanse.availableAdvertiserWidgetTitle" chosenrecordtitle="cleanse.chosenAdvertiserWidgetTitle" updatecallback="cleanse.advertiserMembershipCallback()" ng-init="cleanse.loadAdvertisers()"></lite-membership-widget>
            <div class="help-block" ng-show="cleanse.formErrors.aid">
                <div ng-repeat="error in cleanse.formErrors.aid">
                    <div ng-bind="error"></div>
                </div>
            </div>
        </div>

    </fieldset>
  </div>
</div>

