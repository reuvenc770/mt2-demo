<h3>General</h3>

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

<div class="form-group">
    <label>Country</label>

    <div layout="row" layout-align="start center">
        <md-checkbox name="countryUS" value="1" ng-checked="listProfile.current.countries[ 'United States' ]" ng-click="listProfile.toggleSelection( listProfile.current.countries , listProfile.countryNameMap ,'United States' , listProfile.generateName )">
            United States
        </md-checkbox>

        <span flex="5"></span>

        <md-checkbox name="countryGB" value="235" ng-checked="listProfile.current.countries[ 'United Kingdom' ]" ng-click="listProfile.toggleSelection( listProfile.current.countries , listProfile.countryNameMap , 'United Kingdom' , listProfile.generateName )">
            United Kingdom
        </md-checkbox>
    </div>
</div>

<div class="row" ng-init="listProfile.clientFeedMap = {{json_encode( $clientFeedMap )}}">
    <div class="col-sm-6">
        <label>Available Feeds</label>

        <div class="pull-right">
            <label ng-click="listProfile.addFeeds()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedFeeds" multiple style="width: 100%; height: 150px;">
            @foreach ( $feeds as $feed )
            <option value="{{$feed[ 'id' ]}}" ng-init="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ] = true;listProfile.feedNameMap[ {{$feed[ 'id' ]}} ] = '{{{ addslashes( $feed[ 'short_name' ] )}}}';" ng-show="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ]">{{ $feed[ 'short_name' ] . ' (' . $feed[ 'name' ] . ')' }}</option>
            @endforeach
        </select>

        <md-input-container flex>
            <label>Filter by Client</label>

            <md-select name="clients" id="clients" ng-model="listProfile.feedClientFilters" md-on-close="listProfile.updateFeedVisibility()" multiple>
                @foreach ( $clients as $client )
                <md-option ng-value="::'{{ $client[ 'value' ] }}'">{{ $client[ 'name' ] }}</md-option>
                @endforeach
            </md-select>
        </md-input-container>

        <md-button class="md-icon-button" data-toggle="tooltip" data-placement="bottom" title="Clear Client Filters" ng-click="listProfile.clearClientFeedFilter()">
            <md-icon md-font-set="material-icons" style="color: #000">cancel</md-icon>
        </md-button>
    </div>

    <div class="col-sm-6">
        <label>Selected Feeds</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeFeeds()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedFeedsForRemoval" multiple="" style="width: 100%; height: 150px;">
            <option ng-repeat="( feedId , feedName ) in listProfile.current.feeds" ng-value="::feedId">@{{::feedName}}</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label><h4>Deliverables Range</h4></label>

    <div class="row">
        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Min</span>

                <input type="number" name="deliverableMin" class="form-control" ng-model="listProfile.current.actionRanges.deliverable.min" ng-change="listProfile.generateName()" min="0" aria-label="Deliverable Min" />
            </div>
        </div>

        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Max</span>

                <input type="number" name="deliverableMax" class="form-control" ng-model="listProfile.current.actionRanges.deliverable.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.deliverable )" min="0" aria-label="Deliverable Max" />
            </div>
        </div>

        <div class="col-lg-2"></div>
    </div>
</div>

<div class="form-group">
    <label><h4>Openers Range</h4></label>

    <div class="row">
        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Min</span>

                <input type="number" name="openerMin" class="form-control" ng-model="listProfile.current.actionRanges.opener.min" ng-change="listProfile.generateName()" min="0" aria-label="Opener Min" />
            </div>
        </div>

        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Max</span>

                <input type="number" name="openerMax" class="form-control" ng-model="listProfile.current.actionRanges.opener.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.opener )" min="0" aria-label="Opener Max" />
            </div>
        </div>

        <div class="col-lg-2">
            <div class="input-group" data-toggle="tooltip" data-placement="top" title="The user opened # or more times">
                <span class="input-group-addon">MultiAction</span>

                <input type="number" name="openerMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.opener.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.opener )" min="1" aria-label="Number of Times Opened" />
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label><h4>Clickers Range</h4></label>

    <div class="row">
        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Min</span>

                <input type="number" name="clickerMin" class="form-control" ng-model="listProfile.current.actionRanges.clicker.min" ng-change="listProfile.generateName()" min="0" aria-label="Clicker Min" />
            </div>
        </div>

        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Max</span>

                <input type="number" name="clickerMax" class="form-control" ng-model="listProfile.current.actionRanges.clicker.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.clicker )" min="0" aria-label="Clicker Max" />
            </div>
        </div>

        <div class="col-lg-2">
            <div class="input-group" data-toggle="tooltip" data-placement="top" title="The user clicked # or more times">
                <span class="input-group-addon">Multiaction</span>

                <input type="number" name="clickerMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.clicker.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.clicker )" min="1" aria-label="Number of Times Clicked" >
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label><h4>Converters Range</h4></label>

    <div class="row">
        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Min</span>

                <input type="number" name="converterMin" class="form-control" ng-model="listProfile.current.actionRanges.converter.min" ng-change="listProfile.generateName()" min="0" aria-label="Converter Min" />
            </div>
        </div>

        <div class="col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">Max</span>

                <input type="number" name="converterMax" class="form-control" ng-model="listProfile.current.actionRanges.converter.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.converter )" min="0" aria-label="Converter Max" />
            </div>
        </div>

        <div class="col-lg-2">
            <div class="input-group" data-toggle="tooltip" data-placement="top" title="The user converted # or more times">
                <span class="input-group-addon">Multiaction</span>

                <input type="number" name="converterMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.converter.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.converter )" min="1" aria-label="Number of Times Converted">
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6">
        <label>Available ISP Groups</label>

        <div class="pull-right">
            <label ng-click="listProfile.addIsps()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedIsps" multiple style="width: 100%; height: 150px;">
            @foreach ( $isps as $isp )
            <option value="{{$isp[ 'id' ]}}" ng-init="listProfile.ispVisibility[ {{$isp[ 'id' ]}} ] = true;listProfile.ispNameMap[ {{$isp[ 'id' ]}} ] = '{{{$isp[ 'name' ]}}}';" ng-show="listProfile.ispVisibility[ {{$isp[ 'id' ]}} ]">{{$isp[ 'name' ]}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Category Actions</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeIsps()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedIspsForRemoval" multiple="" style="width: 100%; height: 150px;">
            <option ng-repeat="( ispId , ispName ) in listProfile.current.isps" ng-value="::ispId">@{{::ispName}}</option>
        </select>
    </div>
</div>
<br />

<div class="row">
    <div class="col-sm-6">
        <label>Available Category Actions</label>

        <div class="pull-right">
            <label ng-click="listProfile.addCategories()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedCategories" multiple style="width: 100%; height: 150px;">
            @foreach ( $categories as $category )
            <option value="{{$category[ 'id' ]}}" ng-init="listProfile.categoryVisibility[ {{$category[ 'id' ]}} ] = true;listProfile.categoryNameMap[ {{$category[ 'id' ]}} ] = '{{{$category[ 'name' ]}}}';" ng-show="listProfile.categoryVisibility[ {{$category[ 'id' ]}} ]">{{$category[ 'name' ]}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Category Actions</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeCategories()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedCategoriesForRemoval" multiple style="width: 100%; height: 150px;">
            <option ng-repeat="( categoryId , categoryName ) in listProfile.current.categories" ng-value="::categoryId">@{{::categoryName}}</option>
        </select>
    </div>
</div>
<br />

<div class="row">
    <div class="col-sm-6">
        <label>Available Offers</label>

        <div class="pull-right">
            <label ng-click="listProfile.addOffers()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>
            <input type="text" style="margin-bottom:5px" placeholder="First 3 Letters of Offer Name" name="searchBy" id="searchBy" class="form-control" ng-change="listProfile.search.populateOffers()" ng-model="listProfile.search.offer"  />
        <select ng-model="listProfile.highlightedOffers" multiple style="width: 100%; height: 150px;" ng-options="offer.name for offer in listProfile.search.offerResults" >
            </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Offers</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeOffers()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select  ng-model="listProfile.highlightedOffersForRemoval" multiple ng-options="offer.name for offer in listProfile.current.offers" style="width: 100%; height: 150px; margin-top:40px">

        </select>
    </div>
</div>

<h3 ng-click="listProfile.showAttrFilters = !listProfile.showAttrFilters">Attribute Filtering
    <md-icon md-font-set="material-icons" ng-show="!listProfile.showAttrFilters">chevron_right</md-icon>
    <md-icon md-font-set="material-icons" ng-show="listProfile.showAttrFilters">expand_more</md-icon>
</h3>

<div ng-show="listProfile.showAttrFilters">
    <div class="form-group">
        <label>Age</label>

        <div class="row">
            <div class="col-lg-5">
                <div class="input-group">
                    <span class="input-group-addon">Min</span>

                    <input type="number" name="filterAgeMin" class="form-control" ng-model="listProfile.current.attributeFilters.age.min" min="0" aria-label="Minimum Age"/>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="input-group">
                    <span class="input-group-addon">Max</span>

                    <input type="number" name="filterAgeMax" class="form-control" ng-model="listProfile.current.attributeFilters.age.max" min="0" aria-label="Maximum Age" />
                </div>
            </div>

            <div class="col-lg-2">
                <md-checkbox name="filterAgeUnknown" ng-model="listProfile.current.attributeFilters.age.unknown" ng-true-value="true" ng-false-value="false" style="margin-top: 7px">Unknown</md-checkbox>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Gender</label>

        <div layout="row" layout-align="start center">
            <md-checkbox name="filterGenderMale" value="Male" ng-checked="listProfile.current.attributeFilters.genders.Male" ng-click="listProfile.toggleSelection( listProfile.current.attributeFilters.genders , listProfile.genderNameMap ,'Male' )">
                Male
            </md-checkbox>

            <span flex="5"></span>

            <md-checkbox name="filterGenderFemale" value="Female" ng-checked="listProfile.current.attributeFilters.genders.Female" ng-click="listProfile.toggleSelection( listProfile.current.attributeFilters.genders , listProfile.genderNameMap , 'Female' )">
                Female
            </md-checkbox>

            <span flex="5"></span>

            <md-checkbox name="filterGenderUnknown" value="Unknown" ng-checked="listProfile.current.attributeFilters.genders.Unknown" ng-click="listProfile.toggleSelection( listProfile.current.attributeFilters.genders , listProfile.genderNameMap , 'Unknown' )">
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

    <div class="row">
        <div class="col-sm-6">
            <label>Available States</label>

            <div class="pull-right">
                <label ng-click="listProfile.addStateFilters()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedStateFilters" multiple style="width: 100%; height: 150px;">
                @foreach ( $states as $state )
                    <option value="{{ $state[ 'iso_3166_2' ] }}" ng-init="listProfile.stateFilterVisibility[ '{{$state[ 'iso_3166_2' ]}}' ] = true;listProfile.stateFilterNameMap[ '{{$state[ 'iso_3166_2' ]}}' ] = '{{$state[ 'name' ]}}';" ng-show="listProfile.stateFilterVisibility[ '{{$state[ 'iso_3166_2' ]}}' ]">{{ $state[ 'name' ] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-sm-6">
            <label>Selected States</label>

            <div class="pull-right">
                <label ng-click="listProfile.removeStateFilters()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedStateFiltersForRemoval" multiple style="width: 100%; height: 150px;">
                <option ng-repeat="( stateId , stateName ) in listProfile.current.attributeFilters.states" ng-value="stateId">@{{stateName}}</option>
            </select>
        </div>
    </div>
    <br />

    <div class="row">
        <div class="col-sm-6">
            <label>Available Device Types</label>

            <div class="pull-right">
                <label ng-click="listProfile.addDeviceTypeFilters()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedDeviceTypeFilters" multiple style="width: 100%; height: 75px;">
                <option value="mobile" ng-show="listProfile.deviceTypeFilterVisibility[ 'mobile' ]">Mobile</option>
                <option value="desktop" ng-show="listProfile.deviceTypeFilterVisibility[ 'desktop' ]">Dekstop</option>
                <option value="unknown" ng-show="listProfile.deviceTypeFilterVisibility[ 'unknown' ]">Unknown</option>
            </select>
        </div>

        <div class="col-sm-6">
            <label>Selected Device Types</label>

            <div class="pull-right">
                <label ng-click="listProfile.removeDeviceTypeFilters()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedDeviceTypeFiltersForRemoval" multiple style="width: 100%; height: 75px;">
                <option ng-repeat="( typeId , typeName ) in listProfile.current.attributeFilters.deviceTypes" ng-value="typeId">@{{typeName}}</option>
            </select>
        </div>
    </div>
    <br />

    <div class="row">
        <div class="col-sm-6">
            <label>Available Device OS</label>

            <div class="pull-right">
                <label ng-click="listProfile.addOsFilters()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedOsFilters" multiple style="width: 100%; height: 75px;">
                <option value="android" ng-show="listProfile.osFilterVisibility[ 'android' ]">Android</option>
                <option value="ios" ng-show="listProfile.osFilterVisibility[ 'ios' ]">iOS</option>
                <option value="macosx" ng-show="listProfile.osFilterVisibility[ 'macosx' ]">Mac OS X</option>
                <option value="rim" ng-show="listProfile.osFilterVisibility[ 'rim' ]">Rim</option>
                <option value="windows" ng-show="listProfile.osFilterVisibility[ 'windows' ]">Windows</option>
                <option value="linux" ng-show="listProfile.osFilterVisibility[ 'linux' ]">Linux</option>
                <option value="other" ng-show="listProfile.osFilterVisibility[ 'other' ]">Other</option>
            </select>
        </div>

        <div class="col-sm-6">
            <label>Selected Device OS</label>

            <div class="pull-right">
                <label ng-click="listProfile.removeOsFilters()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedOsFiltersForRemoval" multiple style="width: 100%; height: 75px;">
                <option ng-repeat="( osId , osName ) in listProfile.current.attributeFilters.os" ng-value="osId">@{{osName}}</option>
            </select>
        </div>
    </div>
    <br />

    <div class="row">
        <div class="col-sm-6">
            <label>Available Mobile Carriers</label>

            <div class="pull-right">
                <label ng-click="listProfile.addCarrierFilters()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedCarrierFilters" multiple style="width: 100%; height: 75px;">
                <option value="att" ng-show="listProfile.carrierFilterVisibility[ 'att' ]">AT&amp;T</option>
                <option value="sprint" ng-show="listProfile.carrierFilterVisibility[ 'sprint' ]">Sprint</option>
                <option value="tmobile" ng-show="listProfile.carrierFilterVisibility[ 'tmobile' ]">T-Mobile</option>
                <option value="verizon" ng-show="listProfile.carrierFilterVisibility[ 'verizon' ]">Verizon</option>
            </select>
        </div>

        <div class="col-sm-6">
            <label>Selected Mobile Carriers</label>

            <div class="pull-right">
                <label ng-click="listProfile.removeCarrierFilters()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedCarrierFiltersForRemoval" multiple style="width: 100%; height: 75px;">
                <option ng-repeat="( carrierId , carrierName ) in listProfile.current.attributeFilters.mobileCarriers" ng-value="carrierId">@{{carrierName}}</option>
            </select>
        </div>
    </div>
</div>

<h3>Suppression</h3>

@if ( Sentinel::inRole( 'admiral' ) )
<div class="row" ng-show="listProfile.enableAdmiral">
    <div class="col-sm-6">
        <label>Available Global Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.addGlobalSupp()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedGlobalSupp" multiple style="width: 100%; height: 75px;">
            <option value="1" ng-show="listProfile.globalSuppVisibility[ 1 ]">Orange Global</option>
            <option value="2" ng-show="listProfile.globalSuppVisibility[ 2 ]">Blue Global</option>
            <option value="3" ng-show="listProfile.globalSuppVisibility[ 3 ]">Green Global</option>
            <option value="4" ng-show="listProfile.globalSuppVisibility[ 4 ]">Gold Global</option>
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected List Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeGlobalSupp()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedGlobalSuppForRemoval" multiple style="width: 100%; height: 75px;">
            <option ng-repeat="( globalSuppId , globalSuppName ) in listProfile.current.suppression.global" ng-value="globalSuppId">@{{globalSuppName}}</option>
        </select>
    </div>
</div>
<br />
@endif

@if ( Sentinel::inRole( 'admiral' ) )
<div class="row" ng-show="listProfile.enableAdmiral">
    <div class="col-sm-6">
        <label>Available List Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.addListSupp( $event )" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedListSupp" multiple style="width: 100%; height: 75px;">
            <option value="1" ng-show="listProfile.listSuppVisibility[ 1 ]">Sprint Yahoo</option>
            <option value="2" ng-show="listProfile.listSuppVisibility[ 2 ]">Verizon Gmail</option>
            <option value="3" ng-show="listProfile.listSuppVisibility[ 3 ]">Trendr Hotmail</option>
            <option value="4" ng-show="listProfile.listSuppVisibility[ 4 ]">RMP Hotmail</option>
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected List Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeListSupp()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedListSuppForRemoval" multiple style="width: 100%; height: 75px;">
            <option ng-repeat="( listSuppId , listSuppName ) in listProfile.current.suppression.list" ng-value="listSuppId">@{{listSuppName}}</option>
        </select>
    </div>
</div>
<br />
@endif

<div class="row">
    <div class="col-sm-6">
        <label>Available Offer Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.addOfferSupp( $event )" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedOfferSupp" multiple style="width: 100%; height: 75px;">
            <option value="">NEED LIST</option>
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Offer Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeOfferSupp()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedOfferSuppForRemoval" multiple style="width: 100%; height: 75px;">
            <option ng-repeat="( offerSuppId , offerSuppName ) in listProfile.current.suppression.offer" ng-value="offerSuppId">@{{offerSuppName}}</option>
        </select>
    </div>
</div>

<h3>Attribute Suppression</h3>

<div class="form-group">
    <label>City/Cities</label>

    <textarea name="city" class="form-control" placeholder="City/Cities" ng-model="listProfile.current.suppression.attribute.cities"></textarea>
</div>


<div class="form-group">
    <label>Zip Code(s)</label>

    <textarea name="zip" class="form-control" placeholder="Zip Code(s)" ng-model="listProfile.current.suppression.attribute.zips"></textarea>
</div>

<div class="row">
    <div class="col-sm-6">
        <label>Available States</label>

        <div class="pull-right">
            <label ng-click="listProfile.addStateSupp()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedStateSupp" multiple style="width: 100%; height: 150px;">
            @foreach ( $states as $state )
                <option value="{{ $state[ 'iso_3166_2' ] }}" ng-init="listProfile.stateSuppVisibility[ '{{$state[ 'iso_3166_2' ]}}' ] = true;listProfile.stateSuppNameMap[ '{{$state[ 'iso_3166_2' ]}}' ] = '{{$state[ 'name' ]}}';" ng-show="listProfile.stateSuppVisibility[ '{{$state[ 'iso_3166_2' ]}}' ]">{{ $state[ 'name' ] }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected States</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeStateSupp()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedStateSuppForRemoval" multiple style="width: 100%; height: 150px;">
            <option ng-repeat="( stateId , stateName ) in listProfile.current.suppression.attribute.states" ng-value="stateId">@{{stateName}}</option>
        </select>
    </div>
</div>

<h3>Hygiene</h3>

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

<h3>Select and Order Columns</h3>

<div>
    <lite-membership-widget recordlist="listProfile.columnList" chosenrecordlist="listProfile.selectedColumns" availablerecordtitle="listProfile.availableWidgetTitle" chosenrecordtitle="listProfile.chosenWidgetTitle" namefield="listProfile.columnLabelField" updatecallback="listProfile.columnMembershipCallback()"></lite-membership-widget>
</div>

<br />

<md-checkbox ng-model="listProfile.current.includeCsvHeader" ng-true-value="true" ng-false-value="false">
    Include header line
</md-checkbox>

@if ( Sentinel::inRole( 'admiral' ) )
<br />
<md-checkbox ng-model="listProfile.enableAdmiral" aria-label="Turn Admiral Features On" ng-true-value="true" ng-false-value="false">Enable Admiral Features</md-checkbox>
<br />
<md-checkbox ng-model="listProfile.current.admiralsOnly" ng-show="listProfile.enableAdmiral" aria-label="Admirals Only" ng-true-value="true" ng-false-value="false">This list is for Admirals ONLY</md-checkbox>
@endif
