<md-card>
    <md-toolbar>
        <div class="md-toolbar-tools">
            <span>Data Cleanse Conditions</span>
        </div>
    </md-toolbar>
    <md-card-content ng-cloak>
        <form name="cleanseForm" layout="column" novalidate>
            <md-input-container>
                <label>Data Export Filename</label>
                <md-select name="pname" ng-model="cleanse.current.pname" ng-required="true">
                    @foreach ( $dataExportFiles as $file )
                    <md-option value="{{ str_replace( 'Incoming/' , '' , $file ) }}">{{ str_replace( 'Incoming/' , '' , $file ) }}</md-option>
                    @endforeach
                </md-select>
                <div ng-messages="cleanseForm.pname.$error">
                    <div ng-message="required">Data export filename is required.</div>
                </div>
            </md-input-container>

            <md-input-container class="md-icon-float md-block">
                <label>Output Filename</label>
                <md-icon md-font-set="material-icons" class="mt2-icon-black">insert_drive_file</md-icon>
                <input name="outname" ng-model="cleanse.current.outname" type="text">
            </md-input-container>

            <md-input-container class="md-icon-float md-block">
                <label>Suppression Filename</label>
                <md-icon md-font-set="material-icons" class="mt2-icon-black">clear</md-icon>
                <input name="suppname" ng-model="cleanse.current.suppname" type="text">
            </md-input-container>

            <md-input-container>
                <div layout="row">
                    <div flex="5"><md-icon md-font-set="material-icons" class="mt2-icon-black no-margin">feedback</md-icon></div>
                    <div>
                        <md-radio-group name="ConfirmEmail" ng-model="cleanse.current.ConfirmEmail" ng-required="true">
                            <md-radio-button value="alphateam@zetainteractive.com">alphateam@zetainteractive.com</md-radio-button>
                            <md-radio-button value="betateam@zetainteractive.com">betateam@zetainteractive.com</md-radio-button>
                        </md-radio-group>

                        <div ng-messages="cleanseForm.ConfirmEmail.$error">
                            <div ng-message="required">Confirmation email is required.</div>
                        </div>
                    </div>
                </div>
            </md-input-container>

            <md-input-container layout="row">
                <div flex="5"><md-icon md-font-set="material-icons" class="mt2-icon-black no-margin">border_top</md-icon></div>
                <md-switch ng-model="cleanse.current.includeHeaders" ng-true-value="'Y'" ng-false-value="'N'" class="no-top-margin">Include Headers: @{{ cleanse.current.includeHeaders ? 'Yes' : 'No' }}</md-switch>
            </md-input-container>

            <md-card-content id="suppressionOffers" layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Suppression Offer Categories</span></h4>

                <md-divider></md-divider>

                <lite-membership-widget recordlist="cleanse.offerCategories" chosenrecordlist="cleanse.selectedOfferCategories" availablerecordtitle="cleanse.availableCategoryWidgetTitle" chosenrecordtitle="cleanse.chosenCategoryWidgetTitle" updatecallback="cleanse.offerCategoryMembershipCallback()" ng-init="cleanse.loadOfferCategories()"></lite-membership-widget>
            </md-card-content>

            <md-card-content id="suppressionCountries" layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Suppression Countries</span></h4>

                <md-divider></md-divider>

                <lite-membership-widget recordlist="cleanse.countries" chosenrecordlist="cleanse.selectedCountries" availablerecordtitle="cleanse.availableCountryWidgetTitle" chosenrecordtitle="cleanse.chosenCountryWidgetTitle" updatecallback="cleanse.countryMembershipCallback()" ng-init="cleanse.loadCountries()"></lite-membership-widget>

            </md-card-content>

            <md-card-content id="suppressionAdvertisers" layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Advertiser Suppression</span></h4>

                <md-divider></md-divider>

                <lite-membership-widget recordlist="cleanse.advertisers" chosenrecordlist="cleanse.selectedAdvertisers" availablerecordtitle="cleanse.availableAdvertiserWidgetTitle" chosenrecordtitle="cleanse.chosenAdvertiserWidgetTitle" updatecallback="cleanse.advertiserMembershipCallback()" ng-init="cleanse.loadAdvertisers()"></lite-membership-widget>

            </md-card-content>

        </form>
    </md-card-content>
</md-card>

