<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Data Cleanse Conditions</h3>
    </div>

    <div class="panel-body">
        <md-content ng-cloak>
            <md-input-container class="md-icon-float md-block">
                <label>Data Export Filename</label>
                <md-icon md-svg-src="img/icons/ic_publish_black_24px.svg"></md-icon>
                <md-select name="pname" ng-model="cleanse.current.pname" class="bold-text add-bottom-margin" required>
                    @foreach ( $dataExportFiles as $file )
                    <md-option value="{{ str_replace( 'Incoming/' , '' , $file ) }}">{{ str_replace( 'Incoming/' , '' , $file ) }}</md-option>
                    @endforeach
                </md-select>

                <div ng-messages="cleanseForm.pname.$error">
                    <div ng-message="required">Data Export Filename is required.</div>
                </div>
            </md-input-container>

            <md-input-container class="md-icon-float md-block">
                <label>Output Filename</label>
                <md-icon md-svg-src="img/icons/ic_insert_drive_file_black_24px.svg"></md-icon>
                <input ng-model="cleanse.current.outname" type="text">
            </md-input-container>

            <md-input-container class="md-icon-float md-block">
                <label>Suppression Filename</label>
                <md-icon md-svg-src="img/icons/ic_clear_black_24px.svg"></md-icon>
                <input ng-model="cleanse.current.suppname" type="text">
            </md-input-container>

            <md-input-container class="md-icon-float md-block">
                <md-icon md-svg-src="img/icons/ic_feedback_black_24px.svg"></md-icon>
                <md-radio-group name="ConfirmEmail" ng-model="cleanse.current.ConfirmEmail" ng-required="true">
                    <md-radio-button value="alphateam@zetainteractive.com">alphateam@zetainteractive.com</md-radio-button>
                    <md-radio-button value="betateam@zetainteractive.com">betateam@zetainteractive.com</md-radio-button>
                </md-radio-group>

                <div ng-messages="cleanseForm.ConfirmEmail.$error">
                    <div ng-message="required">Confirmation Email is required.</div>
                </div>
            </md-input-container>

            <md-input-container md-no-float class="md-block">
                <md-icon md-svg-src="img/icons/ic_border_top_black_24px.svg"></md-icon>
                <md-switch ng-model="cleanse.current.includeHeaders" ng-true-value="'Y'" ng-false-value="'N'" class="no-top-margin">Include Headers: @{{ cleanse.current.includeHeaders ? 'Yes' : 'No' }}</md-switch>
            </md-input-container>
        </md-content>

        <md-content id="suppressionOffers" layout-padding style="margin-bottom: 1em;" ng-cloak>
            <h4 layout flex layout-align="center center"><span>Suppression Offer Categories</span></h4>

            <md-divider></md-divider>

            <lite-membership-widget recordlist="cleanse.offerCategories" chosenrecordlist="cleanse.selectedOfferCategories" availablerecordtitle="cleanse.availableCategoryWidgetTitle" chosenrecordtitle="cleanse.chosenCategoryWidgetTitle" updatecallback="cleanse.offerCategoryMembershipCallback()" ng-init="cleanse.loadOfferCategories()"></lite-membership-widget>
        </md-content>

        <md-content id="suppressionCountries" layout-padding style="margin-bottom: 1em;" ng-cloak>
            <h4 layout flex layout-align="center center"><span>Suppression Countries</span></h4>

            <md-divider></md-divider>

            <lite-membership-widget recordlist="cleanse.countries" chosenrecordlist="cleanse.selectedCountries" availablerecordtitle="cleanse.availableCountryWidgetTitle" chosenrecordtitle="cleanse.chosenCountryWidgetTitle" updatecallback="cleanse.countryMembershipCallback()" ng-init="cleanse.loadCountries()"></lite-membership-widget>

        </md-content>

        <md-content id="suppressionAdvertisers" layout-padding style="margin-bottom: 1em;" ng-cloak>
            <h4 layout flex layout-align="center center"><span>Advertiser Suppression</span></h4>

            <md-divider></md-divider>

            <lite-membership-widget recordlist="cleanse.advertisers" chosenrecordlist="cleanse.selectedAdvertisers" availablerecordtitle="cleanse.availableAdvertiserWidgetTitle" chosenrecordtitle="cleanse.chosenAdvertiserWidgetTitle" updatecallback="cleanse.advertiserMembershipCallback()" ng-init="cleanse.loadAdvertisers()"></lite-membership-widget>

        </md-content>
    </div>
</div>
