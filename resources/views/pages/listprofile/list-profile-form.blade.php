<form name="listProfileForm" layout="column" novalidate>

    <md-card>
        <md-toolbar>
            <div class="md-toolbar-tools"><span>List Profile</span></div>
        </md-toolbar>

      <md-card-content layout="column">
        <md-input-container>
            <label>Profile Name</label>

            <input type="text" name="name" id="name" ng-model="listProfile.current.name" />
        </md-input-container>

        <md-input-container>
            <label>Countries</label>

            <md-select name="countries" id="countries" ng-model="listProfile.current.countries" multiple>
                @foreach ( $countries as $country )
                <md-option ng-value="::'{{ $country[ 'id' ] }}'">{{ $country[ 'name' ] }}</md-option>
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
                        <input type="number" name="deliverableMin" model="listProfile.current.actionRanges.deliverable.min" min="0" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="deliverableMax" model="listProfile.current.actionRanges.deliverable.max" min="0" />
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
                        <input type="number" name="openerMin" model="listProfile.current.actionRanges.opener.min" min="0" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="openerMax" model="listProfile.current.actionRanges.opener.max" min="0" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-input-container>
                        <input type="number" name="openerMultiaction" model="listProfile.current.actionRanges.open.multiaction" min="1">
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
                        <input type="number" name="clickerMin" model="listProfile.current.actionRanges.clicker.min" min="0" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="clickerMax" model="listProfile.current.actionRanges.clicker.max" min="0" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-input-container>
                        <input type="number" name="clickerMultiaction" model="listProfile.current.actionRanges.clicker.multiaction" min="1" >
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
                        <input type="number" name="converterMin" model="listProfile.current.actionRanges.converter.min" min="0" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="converterMax" model="listProfile.current.actionRanges.converter.max" min="0" />
                        <div class="hint">Max</div>
                    </md-input-container>
                    <span flex="5"></span>
                    <md-input-container>
                        <input type="number" name="converterMultiaction" model="listProfile.current.actionRanges.converter.multiaction" min="1">
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
            <!-- ISP/category/offer fields -->
        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Attribute Filtering</span></div>
        </md-toolbar>

        <md-card-content>

            <div layout-xs="column" layout="row" layout-align="center start" layout-align-gt-xs="start center">
                <label flex-gt-xs="25" flex="100">Age:</label>
                <div layout="row" layout-align="start center">
                    <md-input-container>
                        <input type="number" name="filterAgeMin" model="listProfile.current.attributeFilters.age.min" min="0" />
                        <div class="hint">Min</div>
                    </md-input-container>
                    <sup><md-icon md-font-set="material-icons" class="mt2-icon-black">remove</md-icon></sup>
                    <md-input-container>
                        <input type="number" name="filterAgeMax" model="listProfile.current.attributeFilters.age.max" min="0" />
                        <div class="hint">Max</div>
                    </md-input-container>
                </div>
            </div>

        </md-card-content>

        <md-toolbar>
            <div class="md-toolbar-tools"><span>Suppression</span></div>
        </md-toolbar>
        <md-card-content layout="column">
            <md-input-container>
                <label>Global Suppression</label>
                <md-select name="globalSupp" ng-required="true" ng-model="listProfile.current.globalSupp" multiple>
                    <md-option value="Orange Global">Orange Global</md-option>
                    <md-option value="Red Global">Red Global</md-option>
                    <md-option value="Purple Global">Purple Global</md-option>
                    <md-option value="Blue Global">Blue Global</md-option>
                </md-select>
            </md-input-container>

            <md-input-container>
                <label>List Suppression</label>
                <md-select name="listSupp" ng-model="listProfile.current.listSupp" multiple>
                    <md-option value="List Option 1">List Option 1</md-option>
                    <md-option value="List Option 2">List Option 2</md-option>
                    <md-option value="List Option 3">List Option 3</md-option>
                </md-select>
            </md-input-container>

            <md-input-container>
                <label>Offer Suppression</label>
                <md-select name="offerSupp" ng-model="listProfile.current.offerSupp" multiple>
                    <md-option value="Offer 1">Offer 1</md-option>
                    <md-option value="Offer 2">Offer 2</md-option>
                    <md-option value="Offer 3">Offer 3</md-option>
                </md-select>
            </md-input-container>

            <label>Attribute Suppression</label>

            <md-chips name="city" placeholder="City/Cities" secondary-placeholder="+ City"
                   ng-model="listProfile.current.cities"
                   md-removable="true"
                   md-enable-chip-edit="true"
                   md-separator-keys="listProfile.mdChipSeparatorKeys"
                   md-add-on-blur="true">

            </md-chips>

            <md-input-container>
                <label>State(s)</label>
                <md-select name="state" convert-to-number ng-model="listProfile.current.states" multiple>
                </md-select>
            </md-input-container>

            <md-chips name="zip" placeholder="Zip Code(s)" secondary-placeholder="+ Zip Code"
                   ng-model="listProfile.current.zips"
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

    </md-card>

</form>
