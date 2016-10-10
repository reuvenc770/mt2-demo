<form name="listProfileForm" layout="column" novalidate>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools"><span>List Profile</span></div>
        </md-toolbar>

      <md-card-content layout="column">
        <md-input-container>
            <label>Profile Name</label>

            <input type="text" name="name" id="name" ng-model="listProfile.current.name" ng-disabled="listProfile.nameDisabled" />
        </md-input-container>

        <md-input-container>
            <label>Countries</label>

            <md-select name="countries" id="countries" md-on-close="listProfile.generateName()" ng-model="listProfile.current.countries" multiple>
                @foreach ( $countries as $country )
                <md-option ng-value="::'{{ $country[ 'id' ] }}'" ng-init="listProfile.countryCodeMap[ {{$country[ 'id' ]}} ] = '{{$country[ 'code' ]}}'">{{ $country[ 'name' ] }}</md-option>
                @endforeach
            </md-select>
        </md-input-container>

        <div layout="row" layout-align="space-around stretch" ng-init="listProfile.clientFeedMap = {{json_encode( $clientFeedMap )}}">
            <md-card flex>
                <md-card-title>
                    <h4>Available Feeds</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.addFeeds()">
                        <md-icon md-font-set="material-icons" style="color: #000;">add_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedFeeds" multiple style="width: 100%; height: 150px;">
                        @foreach ( $feeds as $feed )
                        <option value="{{$feed[ 'id' ]}}" ng-init="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ] = true;listProfile.feedNameMap[ {{$feed[ 'id' ]}} ] = '{{$feed[ 'name' ]}}';" ng-show="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ]">{{$feed[ 'name' ]}}</option>
                        @endforeach
                    </select>
                </md-card-content>

                <md-card-footer layout="row">
                    <md-input-container flex>
                        <label>Filter by Client</label>

                        <md-select name="clients" id="clients" ng-model="listProfile.feedClientFilters" md-on-close="listProfile.updateFeedVisibility()" multiple>
                            @foreach ( $clients as $client )
                            <md-option ng-value="::'{{ $client[ 'value' ] }}'">{{ $client[ 'name' ] }}</md-option>
                            @endforeach
                        </md-select>
                    </md-input-container>

                    <md-button class="md-icon-button" ng-click="listProfile.clearClientFeedFilter()">
                        <md-icon md-font-set="material-icons" style="color: #000">cancel</md-icon>

                        <md-tooltip>Clear Client Filters</md-tooltip>
                    </md-button>
                </md-card-footer>
            </md-card>

            <md-card flex>
                <md-card-title flex="nogrow">
                    <h4>Selected Feeds</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.removeFeeds()">
                        <md-icon md-font-set="material-icons" style="color: #000;">remove_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedFeedsForRemoval" multiple="" style="width: 100%; height: 150px;">
                        <option ng-repeat="( feedId , feedName ) in listProfile.current.feeds" ng-value="::feedId">@{{::feedName}}</option>
                    </select>
                </md-card-content>

                <md-card-footer layout="column">

                </md-card-footer>
            </md-card>
        </div>
      </md-card-content>

        <md-divider></md-divider>

        <md-card-content>
            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Deliverables Range:</label>
                <div layout="row" layout-align="start center">
                    <md-input-container>
                        <input type="number" name="deliverableMin" ng-model="listProfile.current.actionRanges.deliverable.min" ng-change="listProfile.generateName()" min="0" aria-label="Deliverable Min" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="deliverableMax" ng-model="listProfile.current.actionRanges.deliverable.max" ng-change="listProfile.generateName()" min="0" aria-label="Deliverable Max" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span>&nbsp;days back </span>
                </div>
            </div>

            <div hide-gt-xs>&nbsp;</div>

            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Openers Range:</label>
                <div layout="row" layout-align="start center">
                    <md-input-container>
                        <input type="number" name="openerMin" ng-model="listProfile.current.actionRanges.opener.min" ng-change="listProfile.generateName()" min="0" aria-label="Opener Min" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="openerMax" ng-model="listProfile.current.actionRanges.opener.max" ng-change="listProfile.generateName()" min="0" aria-label="Opener Max" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-input-container>
                        <input type="number" name="openerMultiaction" ng-model="listProfile.current.actionRanges.opener.multiaction" min="1" aria-label="Number of Times Opened">
                        <div class="hint">Multiaction</div>
                        <md-tooltip md-direction="top">The user opened # or more times.</md-tooltip>
                        </input>
                    </md-input-container>
                    <span> X</span>
                </div>
            </div>

            <div hide-gt-xs>&nbsp;</div>

            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Clickers Range:</label>
                <div layout="row" layout-align="start center">
                    <md-input-container>
                        <input type="number" name="clickerMin" ng-model="listProfile.current.actionRanges.clicker.min" ng-change="listProfile.generateName()" min="0" aria-label="Clicker Min" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="clickerMax" ng-model="listProfile.current.actionRanges.clicker.max" ng-change="listProfile.generateName()" min="0" aria-label="Clicker Max" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-input-container>
                        <input type="number" name="clickerMultiaction" ng-model="listProfile.current.actionRanges.clicker.multiaction" min="1" aria-label="Number of Times Clicked" >
                        <div class="hint">Multiaction</div>
                        <md-tooltip md-direction="top">The user clicked # or more times.</md-tooltip>
                        </input>
                    </md-input-container>
                    <span> X</span>
                </div>
            </div>

            <div hide-gt-xs>&nbsp;</div>

            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Converters Range:</label>
                <div layout="row" layout-align="start center">
                    <md-input-container>
                        <input type="number" name="converterMin" ng-model="listProfile.current.actionRanges.converter.min" ng-change="listProfile.generateName()" min="0" aria-label="Converter Min" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="converterMax" ng-model="listProfile.current.actionRanges.converter.max" ng-change="listProfile.generateName()" min="0" aria-label="Converter Max" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-input-container>
                        <input type="number" name="converterMultiaction" ng-model="listProfile.current.actionRanges.converter.multiaction" min="1" aria-label="Number of Times Converted">
                        <div class="hint">Multiaction</div>
                        <md-tooltip md-direction="top">The user coverted # or more times.</md-tooltip>
                        </input>
                    </md-input-container>
                    <span> X</span>
                </div>
            </div>
        </md-card-content>

        <md-divider></md-divider>

        <md-card-content>
        <div layout="row" layout-align="space-around stretch">
            <md-card flex>
                <md-card-title>
                    <h4>Available ISP Groups</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.addIsps()">
                        <md-icon md-font-set="material-icons" style="color: #000;">add_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedIsps" multiple style="width: 100%; height: 150px;">
                        @foreach ( $isps as $isp )
                        <option value="{{$isp[ 'id' ]}}" ng-init="listProfile.ispVisibility[ {{$isp[ 'id' ]}} ] = true;listProfile.ispNameMap[ {{$isp[ 'id' ]}} ] = '{{$isp[ 'name' ]}}';" ng-show="listProfile.ispVisibility[ {{$isp[ 'id' ]}} ]">{{$isp[ 'name' ]}}</option>
                        @endforeach
                    </select>
                </md-card-content>
            </md-card>

            <md-card flex>
                <md-card-title flex="nogrow">
                    <h4>Selected ISP Groups</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.removeIsps()">
                        <md-icon md-font-set="material-icons" style="color: #000;">remove_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedIspsForRemoval" multiple="" style="width: 100%; height: 150px;">
                        <option ng-repeat="( ispId , ispName ) in listProfile.current.isps" ng-value="::ispId">@{{::ispName}}</option>
                    </select>
                </md-card-content>
            </md-card>
        </div>
      </md-card-content>

      <md-card-content>
        <div layout="row" layout-align="space-around stretch">
            <md-card flex>
                <md-card-title>
                    <h4>Available Category Actions</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.addCategories()">
                        <md-icon md-font-set="material-icons" style="color: #000;">add_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedCategories" multiple style="width: 100%; height: 150px;">
                        @foreach ( $categories as $category )
                        <option value="{{$category[ 'id' ]}}" ng-init="listProfile.categoryVisibility[ {{$category[ 'id' ]}} ] = true;listProfile.categoryNameMap[ {{$category[ 'id' ]}} ] = '{{$category[ 'name' ]}}';" ng-show="listProfile.categoryVisibility[ {{$category[ 'id' ]}} ]">{{$category[ 'name' ]}}</option>
                        @endforeach
                    </select>
                </md-card-content>
            </md-card>

            <md-card flex>
                <md-card-title flex="nogrow">
                    <h4>Selected Category Actions</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.removeCategories()">
                        <md-icon md-font-set="material-icons" style="color: #000;">remove_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedCategoriesForRemoval" multiple style="width: 100%; height: 150px;">
                        <option ng-repeat="( categoryId , categoryName ) in listProfile.current.categories" ng-value="::categoryId">@{{::categoryName}}</option>
                    </select>
                </md-card-content>
            </md-card>
        </div>
      </md-card-content>

      <md-card-content>
        <div layout="row" layout-align="space-around stretch">
            <md-card flex>
                <md-card-title>
                    <h4>Available Offers</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.addOffers()">
                        <md-icon md-font-set="material-icons" style="color: #000;">add_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedOffers" multiple style="width: 100%; height: 150px;">
                        @foreach ( $offers as $offer )
                        <option value="{{$offer[ 'id' ]}}" ng-init="listProfile.offerVisibility[ {{$offer[ 'id' ]}} ] = true;listProfile.offerNameMap[ {{$offer[ 'id' ]}} ] = '{{$offer[ 'name' ]}}';" ng-show="listProfile.offerVisibility[ {{$offer[ 'id' ]}} ]">{{$offer[ 'name' ]}}</option>
                        @endforeach
                    </select>
                </md-card-content>
            </md-card>

            <md-card flex>
                <md-card-title flex="nogrow">
                    <h4>Selected Offers</h4>

                    <span flex></span>

                    <md-button class="md-icon-button" ng-click="listProfile.removeOffers()">
                        <md-icon md-font-set="material-icons" style="color: #000;">remove_circle_outline</md-icon>
                    </md-button>
                </md-card-title>

                <md-card-content>
                    <select ng-model="listProfile.highlightedOffersForRemoval" multiple style="width: 100%; height: 150px;">
                        <option ng-repeat="( offerId , offerName ) in listProfile.current.offers" ng-value="::offerId">@{{::offerName}}</option>
                    </select>
                </md-card-content>
            </md-card>
        </div>
      </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Attribute Filtering</span></div>
        </md-toolbar>

        <md-card-content layout="column">

            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Age:</label>
                <div layout="row" layout-align="start center">
                    <md-input-container>
                        <input type="number" name="filterAgeMin" ng-model="listProfile.current.attributeFilters.age.min" min="0" aria-label="Minimum Age"/>
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="filterAgeMax" ng-model="listProfile.current.attributeFilters.age.max" min="0" aria-label="Maximum Age" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-checkbox name="filterAgeUnknown" ng-model="listProfile.current.attributeFilters.age.unknown" ng-true-value="true" ng-false-value="false">
                        Unknown
                    </md-checkbox>
                </div>
            </div>

            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Gender:</label>
                <div layout="row" layout-align="start center">
                    <md-checkbox name="filterGenderMale" value="Male" ng-checked="listProfile.current.attributeFilters.genders.indexOf('Male') > -1" ng-click="listProfile.toggleSelection('Male')">
                        Male
                    </md-checkbox>
                    <span flex="5"></span>
                    <md-checkbox name="filterGenderFemale" value="Female" ng-checked="listProfile.current.attributeFilters.genders.indexOf('Female') > -1" ng-click="listProfile.toggleSelection('Female')">
                        Female
                    </md-checkbox>
                    <span flex="5"></span>
                    <md-checkbox name="filterGenderUnknown" value="Unknown" ng-checked="listProfile.current.attributeFilters.genders.indexOf('Unknown') > -1" ng-click="listProfile.toggleSelection('Unknown')">
                        Unknown
                    </md-checkbox>
                </div>
            </div>

            <md-chips name="zip" placeholder="Zip Code(s)" secondary-placeholder="+ Zip Code"
                   ng-model="listProfile.current.attributeFilters.zips"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

            </md-chips>

            <md-chips name="city" placeholder="City/Cities" secondary-placeholder="+ City"
                   ng-model="listProfile.current.attributeFilters.cities"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

            </md-chips>

            <md-input-container>
                <label>State(s)</label>
                <md-select name="state" convert-to-number ng-model="listProfile.current.attributeFilters.states" multiple>
                    @foreach ( $states as $state )
                        <md-option ng-value="::'{{ $state[ 'iso_3166_2' ] }}'">{{ $state[ 'name' ] }}</md-option>
                    @endforeach
                </md-select>
            </md-input-container>

            <md-input-container>
                <label>Device Type</label>
                <md-select name="deviceType" ng-model="listProfile.current.attributeFilters.deviceTypes" multiple>
                    <md-option value="Galaxy Note 7">Galaxy Note 7</md-option>
                    <md-option value="iPhone 6S Plus">iPhone 6S Plus</md-option>
                    <md-option value="iPhone 6S">iPhone 6S</md-option>
                </md-select>
            </md-input-container>

            <md-input-container>
                <label>Mobile Carriers</label>
                <md-select name="mobileCarriers" ng-model="listProfile.current.attributeFilters.mobileCarriers" multiple>
                    <md-option value="ATT">AT&amp;T</md-option>
                    <md-option value="Sprint">Sprint</md-option>
                    <md-option value="T-Mobile">T-Mobile</md-option>
                    <md-option value="Verizon">Verizon</md-option>
                </md-select>
            </md-input-container>

        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Suppression</span></div>
        </md-toolbar>
        <md-card-content layout="column">
            <md-input-container>
                <label>Global Suppression</label>
                <md-select name="globalSupp" ng-required="true" ng-model="listProfile.current.suppression.global" multiple>
                    <md-option value="Orange Global">Orange Global</md-option>
                    <md-option value="Red Global">Red Global</md-option>
                    <md-option value="Purple Global">Purple Global</md-option>
                    <md-option value="Blue Global">Blue Global</md-option>
                </md-select>
            </md-input-container>

            <md-input-container>
                <label>List Suppression</label>
                <md-select name="listSupp" ng-model="listProfile.current.suppression.list" multiple>
                    <md-option value="List Option 1">List Option 1</md-option>
                    <md-option value="List Option 2">List Option 2</md-option>
                    <md-option value="List Option 3">List Option 3</md-option>
                </md-select>
            </md-input-container>

            <md-input-container>
                <label>Offer Suppression</label>
                <md-select name="offerSupp" ng-model="listProfile.current.suppression.offer" multiple>
                    @foreach ( $offers as $offer )
                    <md-option value="{{$offer[ 'id' ]}}">{{$offer[ 'name' ]}}</md-option>
                    @endforeach
                </md-select>
            </md-input-container>

            <h5><strong>Attribute Suppression</strong></h5>

            <md-chips name="city" placeholder="City/Cities" secondary-placeholder="+ City"
                   ng-model="listProfile.current.suppression.attribute.cities"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

            </md-chips>

            <md-input-container>
                <label>State(s)</label>
                <md-select name="state" convert-to-number ng-model="listProfile.current.suppression.attribute.states" multiple>
                    @foreach ( $states as $state )
                        <md-option ng-value="::'{{ $state[ 'iso_3166_2' ] }}'">{{ $state[ 'name' ] }}</md-option>
                    @endforeach
                </md-select>
            </md-input-container>

            <md-chips name="zip" placeholder="Zip Code(s)" secondary-placeholder="+ Zip Code"
                   ng-model="listProfile.current.suppression.attribute.zips"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

            </md-chips>

        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Hygiene</span></div>
        </md-toolbar>
        <md-card-content>

        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Select and Order Columns</span></div>
        </md-toolbar>

        <md-card-content layout="column">
            <div>
                <lite-membership-widget recordlist="listProfile.columnList" chosenrecordlist="listProfile.selectedColumns" availablerecordtitle="listProfile.availableWidgetTitle" chosenrecordtitle="listProfile.chosenWidgetTitle" namefield="listProfile.columnLabelField" updatecallback="listProfile.columnMembershipCallback()"></lite-membership-widget>
            </div>

            <md-input-container layout-padding>
                <md-checkbox ng-model="listProfile.current.includeCsvHeader" ng-true-value="true" ng-false-value="false">
                    Include header line
                </md-checkbox>
            </md-input-container>
        </md-card-content>

    </md-card>

</form>
