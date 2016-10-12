<form name="listProfileForm" layout="column" novalidate>
    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools">
                <span>List Profile</span>

                <span flex></span>

                @if ( Sentinel::inRole( 'admiral' ) )
                <md-switch ng-model="listProfile.enableAdmiral" aria-label="Enable Admiral Features" ng-true-value="true" ng-false-value="false">Enable Admiral Features</md-switch>
                @endif
            </div>
        </md-toolbar>

        <md-card-content layout="column">
            <div class="form-group">
                <label for="name">Profile Name</label>

                <div class="input-group">
                    <input type="text" name="name" id="name" class="form-control" ng-model="listProfile.current.name" ng-disabled="listProfile.nameDisabled" />

                    <span class="input-group-btn">
                        <button class="btn btn-default" ng-show="!listProfile.customName" ng-click="listProfile.toggleEditName( $event )">Edit</button>
                        <button class="btn btn-danger" ng-show="listProfile.customName" ng-click="listProfile.toggleEditName( $event , true )">Reset To Default</button>
                    </span>
                </div>
            </div>

            @if ( Sentinel::inRole( 'admiral' ) )
            <md-checkbox ng-model="listProfile.current.admiralsOnly" ng-show="listProfile.enableAdmiral" aria-label="Admirals Only" ng-true-value="true" ng-false-value="false">Admirals Only</md-checkbox>
            @endif

            <div class="form-group">
                <label for="countries">Countries</label>

                <select name="countries" id="countries" class="form-control" ng-change="listProfile.generateName()" ng-model="listProfile.current.countries" multiple>
                    @foreach ( $countries as $country )
                    <option ng-value="::'{{ $country[ 'id' ] }}'" ng-init="listProfile.countryCodeMap[ {{$country[ 'id' ]}} ] = '{{$country[ 'code' ]}}'">{{ $country[ 'name' ] }}</option>
                    @endforeach
                </select>
            </div>

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
                            <option value="{{$feed[ 'id' ]}}" ng-init="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ] = true;listProfile.feedNameMap[ {{$feed[ 'id' ]}} ] = '{{$feed[ 'short_name' ]}}';" ng-show="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ]">{{ $feed[ 'short_name' ] . ' (' . $feed[ 'name' ] . ')' }}</option>
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

                    <md-card-footer layout="column"></md-card-footer>
                </md-card>
            </div>
        </md-card-content>

        <md-divider></md-divider>

        <md-card-content>
            <div class="form-group">
                <label><h4>Deliverables Range</h4></label>

                <div class="row">
                    <div class="col-lg-5">
                        <label>Min</label>

                        <input type="number" name="deliverableMin" class="form-control" ng-model="listProfile.current.actionRanges.deliverable.min" ng-change="listProfile.generateName()" min="0" aria-label="Deliverable Min" />
                    </div>

                    <div class="col-lg-5">
                        <label>Max</label>

                        <input type="number" name="deliverableMax" class="form-control" ng-model="listProfile.current.actionRanges.deliverable.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.deliverable )" min="0" aria-label="Deliverable Max" />
                    </div>

                    <div class="col-lg-2"></div>
                </div>
            </div>

            <div class="form-group">
                <label><h4>Openers Range</h4></label>

                <div class="row">
                    <div class="col-lg-5 form-group">
                        <label>Min</label>

                        <input type="number" name="openerMin" class="form-control" ng-model="listProfile.current.actionRanges.opener.min" ng-change="listProfile.generateName()" min="0" aria-label="Opener Min" />
                    </div>

                    <div class="col-lg-5 form-group">
                        <label>Max</label>

                        <input type="number" name="openerMax" class="form-control" ng-model="listProfile.current.actionRanges.opener.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.opener )" min="0" aria-label="Opener Max" />
                    </div>

                    <div class="col-lg-2 form-group">
                        <label>MultiAction</label>

                        <input type="number" name="openerMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.opener.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.opener )" min="1" aria-label="Number of Times Opened">

                        <i>The user opened # or more times</i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><h4>Clickers Range</h4></label>

                <div class="row">
                    <div class="col-lg-5 form-group">
                        <label>Min</label>

                        <input type="number" name="clickerMin" class="form-control" ng-model="listProfile.current.actionRanges.clicker.min" ng-change="listProfile.generateName()" min="0" aria-label="Clicker Min" />
                    </div>

                    <div class="col-lg-5 form-group">
                        <label>Max</label>

                        <input type="number" name="clickerMax" class="form-control" ng-model="listProfile.current.actionRanges.clicker.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.clicker )" min="0" aria-label="Clicker Max" />
                    </div>

                    <div class="col-lg-2 form-group">
                        <label>Multiaction</label>

                        <input type="number" name="clickerMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.clicker.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.clicker )" min="1" aria-label="Number of Times Clicked" >

                        <i>The user clicked # or more times.</i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><h4>Converters Range</h4></label>

                <div class="row">
                    <div class="col-lg-5 form-group">
                        <label>Min</label>

                        <input type="number" name="converterMin" class="form-control" ng-model="listProfile.current.actionRanges.converter.min" ng-change="listProfile.generateName()" min="0" aria-label="Converter Min" />
                    </div>

                    <div class="col-lg-5 form-group">
                        <label>Max</label>

                        <input type="number" name="converterMax" class="form-control" ng-model="listProfile.current.actionRanges.converter.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.converter )" min="0" aria-label="Converter Max" />
                    </div>

                    <div class="col-lg-2 form-group">
                        <label>Multiaction</label>

                        <input type="number" name="converterMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.converter.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.converter )" min="1" aria-label="Number of Times Converted">

                        <i>The user coverted # or more times.</i>
                    </div>
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
            <div class="md-toolbar-tools">
                <span>Attribute Filtering</span>

                <span flex></span>

                <md-button class="md-icon-button" aria-label="Show Attribute Filters" ng-show="!listProfile.showAttrFilters" ng-click="listProfile.showAttrFilters = true;">
                    <md-icon md-font-set="material-icons">chevron_right</md-icon>
                </md-button>

                <md-button class="md-icon-button" aria-label="Show Attribute Filters" ng-show="listProfile.showAttrFilters" ng-click="listProfile.showAttrFilters = false;">
                    <md-icon md-font-set="material-icons">expand_more</md-icon>
                </md-button>
            </div>
        </md-toolbar>

        <md-card-content ng-show="listProfile.showAttrFilters" layout="column">
            <div class="form-group">
                <label>Age</label>

                <div class="row">
                    <div class="form-group col-lg-5">
                        <label>Min</label>

                        <input type="number" name="filterAgeMin" class="form-control" ng-model="listProfile.current.attributeFilters.age.min" min="0" aria-label="Minimum Age"/>
                    </div>

                    <div class="form-group col-lg-5">
                        <label>Max</label>

                        <input type="number" name="filterAgeMax" class="form-control" ng-model="listProfile.current.attributeFilters.age.max" min="0" aria-label="Maximum Age" />
                    </div>

                    <div class="form-group col-lg-2">
                        <md-checkbox name="filterAgeUnknown" ng-model="listProfile.current.attributeFilters.age.unknown" ng-true-value="true" ng-false-value="false" style="margin-top: 25px">Unknown</md-checkbox>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Gender:</label>

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

            <div class="form-group">
                <label>Zip Codes(s)</label>

                <textarea name="zip" class="form-control" placeholder="Zip Code(s)" ng-model="listProfile.current.attributeFilters.zips" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label>City/Cities</label>

                <textarea name="city" class="form-control" placeholder="City/Cities" ng-model="listProfile.current.attributeFilters.cities" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="stateFilter">State(s)</label>

                <select name="state" id="stateFilter" class="form-control" ng-model="listProfile.current.attributeFilters.states" multiple>
                    @foreach ( $states as $state )
                        <option ng-value="::'{{ $state[ 'iso_3166_2' ] }}'">{{ $state[ 'name' ] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="deviceType">Device Type</label>

                <select name="deviceType" id="deviceType" class="form-control" ng-model="listProfile.current.attributeFilters.deviceTypes" multiple>
                    <option value="Galaxy Note 7">Galaxy Note 7</option>
                    <option value="iPhone 6S Plus">iPhone 6S Plus</option>
                    <option value="iPhone 6S">iPhone 6S</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mobileCarriers">Mobile Carriers</label>

                <select name="mobileCarriers" id="mobilecarriers" class="form-control" ng-model="listProfile.current.attributeFilters.mobileCarriers" multiple>
                    <option value="ATT">AT&amp;T</option>
                    <option value="Sprint">Sprint</option>
                    <option value="T-Mobile">T-Mobile</option>
                    <option value="Verizon">Verizon</option>
                </select>
            </div>

        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Suppression</span></div>
        </md-toolbar>

        <md-card-content layout="column">
            @if ( Sentinel::inRole( 'admiral' ) )
            <div class="form-group" ng-show="listProfile.enableAdmiral">
                <label for="globalSupp">Global Suppression</label>

                <select name="globalSupp" id="globalSupp" class="form-control" ng-required="true" ng-model="listProfile.current.suppression.global" ng-change="listProfile.sanitizeGlobalSupp()" multiple>
                    <option value="1">Orange Global</option>
                    <option value="2">Blue Global</option>
                    <option value="3">Green Global</option>
                    <option value="4">White Global</option>
                </select>
            </div>
            @endif

            @if ( Sentinel::inRole( 'admiral' ) )
            <div class="form-group" ng-show="listProfile.enableAdmiral">
                <label for="listSupp">List Suppression</label>

                <select name="listSupp" id="listSupp" class="form-control" ng-model="listProfile.current.suppression.list" ng-change="listProfile.confirmSuppressionConfig( $event , 'list' )" multiple>
                    <option value="List Option 1">List Option 1</option>
                    <option value="List Option 2">List Option 2</option>
                    <option value="List Option 3">List Option 3</option>
                </select>
            </div>
            @endif

            <div class="form-group">
                <label for="offerSupp">Offer Suppression</label>

                <select name="offerSupp" id="offerSupp" class="form-control" ng-model="listProfile.current.suppression.offer" ng-change="listProfile.confirmSuppressionConfig( $event , 'offer' )" multiple>
                    @foreach ( $offers as $offer )
                    <option value="{{$offer[ 'id' ]}}">{{$offer[ 'name' ]}}</option>
                    @endforeach
                </select>
            </div>
        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Attribute Suppression</span></div>
        </md-toolbar>

        <md-card-content>
            <div class="form-group">
                <label>City/Cities</label>

                <textarea name="city" class="form-control" placeholder="City/Cities" ng-model="listProfile.current.suppression.attribute.cities"></textarea>
            </div>

            <div class="form-group">
                <label for="state">State(s)</label>

                <select name="state" id="state" class="form-control" ng-model="listProfile.current.suppression.attribute.states" multiple>
                    @foreach ( $states as $state )
                        <option value="{{ $state[ 'iso_3166_2' ] }}">{{ $state[ 'name' ] }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <label>Zip Code(s)</label>

                <textarea name="zip" class="form-control" placeholder="Zip Code(s)" ng-model="listProfile.current.suppression.attribute.zips"></textarea>
            </div>
        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Hygiene</span></div>
        </md-toolbar>

        <md-card-content>
            <md-checkbox ng-model="listProfile.current.impressionwise" ng-true-value="true" ng-false-value="false">Impressionwise</md-checkbox>

            <br />

            <md-checkbox ng-model="listProfile.current.tower.run" ng-true-value="true" ng-false-value="false">Tower</md-checkbox>

            <div class="form-group" ng-show="listProfile.current.tower.run">
                <label>Cleansed After The Following Date</label>
                
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-addon">Year</span>

                            <select class="form-control" ng-model="listProfile.current.tower.cleanseYear" ng-init="listProfile.generateTowerDateOptions()">
                                <option value="">Year</option>
                                <option ng-value="::listProfile.towerDateOptions[ 0 ].value">@{{ ::listProfile.towerDateOptions[ 0 ].value }}</option>
                                <option ng-value="::listProfile.towerDateOptions[ 1 ].value">@{{ ::listProfile.towerDateOptions[ 1 ].value }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-addon">Month</span>

                            <select class="form-control" ng-model="listProfile.current.tower.cleanseMonth">
                                <option value="">Month</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
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
