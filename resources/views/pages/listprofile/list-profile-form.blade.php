

@if ( Sentinel::inRole( 'admiral' ) )
<h3 class="bold-text">Admiral Settings</h3>
<br />
<md-checkbox ng-model="listProfile.enableAdmiral" aria-label="Turn Admiral Features On" ng-true-value="true" ng-false-value="false">Enable Admiral Features</md-checkbox>
<br />
<md-checkbox ng-model="listProfile.current.admiralsOnly" ng-show="listProfile.enableAdmiral" aria-label="Admirals Only" ng-true-value="true" ng-false-value="false">This list is for Admirals ONLY</md-checkbox>
@endif


<h3 class="bold-text">General</h3>
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
<div class="form-group" ng-class="{ 'has-error' : listProfile.formErrors.ftp_folder }">
    <label>FTP Folder</label>
    <input placeholder="ftp_folder" value="" class="form-control" ng-model="listProfile.current.ftp_folder"
           required="required" name="name" type="text">
    <div class="help-block" ng-show="listProfile.formErrors.ftp_folder">
        <div ng-repeat="error in listProfile.formErrors.ftp_folder">
            <span ng-bind="error"></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Party</label>

    <div layout="row" layout-align="start center">
        <label class="radio-inline">
            <input type="radio" name="party" ng-model="listProfile.current.party"  ng-click="listProfile.updateParty()" value="1">1st Party List Profile
        </label>

        <label class="radio-inline">
            <input type="radio" name="party" ng-model="listProfile.current.party" ng-click="listProfile.updateParty()" value="3">3rd Party List Profile
        </label>
    </div>
</div>

<div class="form-group">
    <label>Country</label>

    <div layout="row" layout-align="start center">
        <label class="radio-inline">
            <input type="radio" name="country" ng-model="listProfile.current.country_id"  ng-click="listProfile.updateCountry()" value="1">United States
        </label>

        <label class="radio-inline">
            <input type="radio" name="country" ng-model="listProfile.current.country_id" ng-click="listProfile.updateCountry()" value="2">United Kingdom
        </label>
    </div>
</div>

<div class="row" ng-init="listProfile.clientFeedMap = {{json_encode( $clientFeedMap )}}; listProfile.countryFeedMap = {{json_encode( $countryFeedMap )}}; listProfile.partyFeedMap = {{json_encode( $partyFeedMap )}}">
    <div class="col-sm-6">
        <label>Available Feeds</label>

        <div class="pull-right">
            <label ng-click="listProfile.addFeeds( $event )" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedFeeds" multiple style="width: 100%; height: 150px;">
            @foreach ( $feeds as $feed )
                @if (($feed == end($feed)))
                    <option value="{{$feed[ 'id' ]}}" ng-init="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ] = true;listProfile.feedNameMap[ {{$feed[ 'id' ]}} ] = '{{{ addslashes( $feed[ 'short_name' ] )}}}';" ng-show="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ]">{{ $feed[ 'short_name' ] }}</option>
                @else
                    <option value="{{$feed[ 'id' ]}}" ng-init="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ] = true;listProfile.feedNameMap[ {{$feed[ 'id' ]}} ] = '{{{ addslashes( $feed[ 'short_name' ] )}}}';listProfile.updateFeedVisibilityFromCountry();listProfile.updateFeedVisibilityFromParty()" ng-show="listProfile.feedVisibility[ {{$feed[ 'id' ]}} ]">{{ $feed[ 'short_name' ] }}</option>
                @endif
            @endforeach
        </select>
        @if ( Sentinel::inRole( 'admiral' ) )
        <md-input-container flex>
            <label>Filter by Client</label>

            <md-select name="clients" id="clients" ng-model="listProfile.feedClientFilters" md-on-close="listProfile.updateFeedVisibility()" multiple>
                @foreach ( $clients as $client )
                <md-option ng-value="::'{{ $client[ 'id' ] }}'">{{ $client[ 'name' ] }}</md-option>
                @endforeach
            </md-select>
        </md-input-container>

        <md-button class="md-icon-button" data-toggle="tooltip" data-placement="bottom" title="Clear Client Filters" ng-click="listProfile.clearClientFeedFilter()">
            <md-icon md-font-set="material-icons" style="color: #000">cancel</md-icon>
        </md-button>
        @endif
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
<div class="row form-group">
    <div class="col-sm-6">
        <label>Available Feed Groups</label>

        <div class="pull-right">
            <label ng-click="listProfile.addFeedGroups()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select id="feedGroupList" ng-model="listProfile.highlightedFeedGroups" multiple style="width: 100%; height: 150px;">
            @foreach ( $feedGroups as $feedGroup )
                <option value="{{$feedGroup[ 'id' ]}}" ng-init="listProfile.feedGroupVisibility[ {{$feedGroup[ 'id' ]}} ] = true;listProfile.feedGroupNameMap[ {{$feedGroup[ 'id' ]}} ] = '{{{$feedGroup[ 'name' ]}}}';" ng-show="listProfile.feedGroupVisibility[ {{$feedGroup[ 'id' ]}} ]">{{ $feedGroup[ 'name' ] }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Feed Groups</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeFeedGroups()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedFeedGroupsForRemoval" multiple="" style="width: 100%; height: 150px;">
            <option ng-repeat="( feedGroupId , feedGroupName ) in listProfile.current.feedGroups" ng-value="::feedGroupId">@{{::feedGroupName}}</option>
        </select>
    </div>
</div>
@if (Sentinel::inRole('admiral'))
<div class="row form-group" id="feedClientWidget">
    <div class="col-sm-6">
        <label>Available Clients</label>

        <div class="pull-right">
            <label ng-click="listProfile.addFeedClients()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedFeedClients" multiple style="width: 100%; height: 150px;" ng-click="listProfile.showAlert('Note: Selecting a client will include all feeds within that client.' , 'feedClientWidget' )">
            @foreach ( $clients as $client )
                <option value="{{$client[ 'id' ]}}" ng-init="listProfile.feedClientVisibility[ {{$client[ 'id' ]}} ] = true;listProfile.feedClientNameMap[ {{$client[ 'id' ]}} ] = '{{{$client[ 'name' ]}}}';" ng-show="listProfile.feedClientVisibility[ {{$client[ 'id' ]}} ]">{{ $client[ 'name' ] }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>Selected Clients</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeFeedClients()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedFeedClientsForRemoval" multiple="" style="width: 100%; height: 150px;">
            <option ng-repeat="( feedClientId , feedClientName ) in listProfile.current.feedClients" ng-value="::feedClientId">@{{::feedClientName}}</option>
        </select>
    </div>
</div>
@endif
<div class="form-group" id="actionRanges">
    <label>
        <h4>Deliverables Day Range</h4>
        <h5><i>All day ranges are inclusive</i></h5>
    </label>

    <div class="has-error">
        <div class="help-block" ng-show="listProfile.formErrors.actionRanges">
            <div ng-repeat="error in listProfile.formErrors.actionRanges">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-7 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="deliverableMin" class="form-control" ng-model="listProfile.current.actionRanges.deliverable.min" ng-change="listProfile.generateName()" min="0" aria-label="Deliverable Min" />

                <span class="range-delimiter">TO</span>

                <input type="number" name="deliverableMax" class="form-control" ng-model="listProfile.current.actionRanges.deliverable.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.deliverable )" min="0" aria-label="Deliverable Max" />

                <label>&nbsp;Days Back</label>
            </div>
        </div>

        <div class="col-xs-12 col-md-5"></div>
    </div>
</div>

<div class="form-group">
    <label>
        <h4>Openers Day Range</h4>
        <h5><i>All day ranges are inclusive</i></h5>
    </label>

    <div class="has-error">
        <div class="help-block" ng-show="listProfile.formErrors.actionRanges">
            <div ng-repeat="error in listProfile.formErrors.actionRanges">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-7 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="openerMin" class="form-control" ng-model="listProfile.current.actionRanges.opener.min" ng-change="listProfile.generateName()" min="0" aria-label="Opener Min" />

                <span class="range-delimiter">TO</span>

                <input type="number" name="openerMax" class="form-control" ng-model="listProfile.current.actionRanges.opener.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.opener )" min="0" aria-label="Opener Max" />

                <label>&nbsp;Days Back</label>
            </div>
        </div>

        <div class="col-xs-12 col-md-5 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="openerMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.opener.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.opener )" min="1" aria-label="Number of Times Opened" >

                <label>&nbsp;Multiaction</label>
                <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Minimum number of times a record opened email.">help</md-icon>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        <h4>Clickers Day Range</h4>
        <h5><i>All day ranges are inclusive</i></h5>
    </label>

    <div class="has-error">
        <div class="help-block" ng-show="listProfile.formErrors.actionRanges">
            <div ng-repeat="error in listProfile.formErrors.actionRanges">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-7 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="clickerMin" class="form-control" ng-model="listProfile.current.actionRanges.clicker.min" ng-change="listProfile.generateName()" min="0" aria-label="Clicker Min" />

                <span class="range-delimiter">TO</span>

                <input type="number" name="clickerMax" class="form-control" ng-model="listProfile.current.actionRanges.clicker.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.clicker )" min="0" aria-label="Clicker Max" />

                <label>&nbsp;Days Back</label>
            </div>
        </div>

        <div class="col-xs-12 col-md-5 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="clickerMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.clicker.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.clicker )" min="1" aria-label="Number of Times Clicked" >

                <label>&nbsp;Multiaction</label>
                <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Minimum number of times a record clicked on a call-to-action.">help</md-icon>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        <h4>Converters Day Range</h4>
        <h5><i>All day ranges are inclusive</i></h5>
    </label>

    <div class="has-error">
        <div class="help-block" ng-show="listProfile.formErrors.actionRanges">
            <div ng-repeat="error in listProfile.formErrors.actionRanges">
                <div ng-bind="error"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-7 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="converterMin" class="form-control" ng-model="listProfile.current.actionRanges.converter.min" ng-change="listProfile.generateName()" min="0" aria-label="Converter Min" />

                <span class="range-delimiter">TO</span>

                <input type="number" name="converterMax" class="form-control" ng-model="listProfile.current.actionRanges.converter.max" ng-change="listProfile.generateName()" ng-blur="listProfile.confirmMaxDateRange( $event , listProfile.current.actionRanges.converter )" min="0" aria-label="Converter Max" />

                <label>&nbsp;Days Back</label>
            </div>
        </div>

        <div class="col-xs-12 col-md-5 form-inline field-top-margin">
            <div class="form-group">
                <input type="number" name="converterMultiaction" class="form-control" ng-model="listProfile.current.actionRanges.converter.multiaction" ng-blur="listProfile.sanitizeMultiAction( listProfile.current.actionRanges.converter )" min="1" aria-label="Number of Times Converted" >

                <label>&nbsp;Multiaction</label>
                <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Minimum number of times a record converted an offer.">help</md-icon>
            </div>
        </div>
    </div>
</div>

<h3 class="bold-text">Actionable Filters</h3>
    <h5>To return records within specific ISP group(s), category(ies), and/or offer(s) select options below. Completed list profile will return records that meet selected option(s) and falls within the action day ranges listed above.</h5>

<div class="row">
    <div class="col-sm-6">
        <label>ISP Groups
            <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Return records that are within selected ISP groups and also meets selected action day ranges.">help</md-icon>
        </label>

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
        <label>Selected ISP Groups</label>

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
        <label>Category Actions
            <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Return records that are within selected category(ies) and also meets selected action day ranges.">help</md-icon>
        </label>

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
        <label>Offer Actions
            <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Return records that are within selected offer(s) and also meet selected action day ranges.">help</md-icon>
        </label>

        <div class="pull-right">
            <label ng-click="listProfile.addOffers()" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>
        <input type="text" style="margin-bottom:5px" 
            placeholder="To search, type at least 3 letters of offer name." 
            name="searchBy" id="searchBy" 
            class="form-control" 
            ng-change="listProfile.search.populateOffers()" 
            ng-model="listProfile.search.offer"  />
        <select ng-model="listProfile.highlightedOffers" multiple style="width: 100%; height: 150px;" ng-options="offer.name for offer in listProfile.search.offerResults" ></select>
    </div>

    <div class="col-sm-6">
        <label>Selected Offer Actions</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeOffers()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>

        <select ng-model="listProfile.highlightedOffersForRemoval" multiple ng-options="offer.name for offer in listProfile.current.offerActions" style="width: 100%; height: 150px; margin-top:40px">
        </select>
    </div>
</div>

<h3 ng-click="listProfile.showAttrFilters = !listProfile.showAttrFilters" class="bold-text">Attribute Filtering
    <md-icon md-font-set="material-icons" ng-show="!listProfile.showAttrFilters">chevron_right</md-icon>
    <md-icon md-font-set="material-icons" ng-show="listProfile.showAttrFilters">expand_more</md-icon>
    <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Optional: Additional filtering to only return records that meet the selected attributes.">help</md-icon>
</h3>

<div ng-show="listProfile.showAttrFilters">
    <div class="form-group">
        <label>Age</label>

        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Min</span>

                    <input type="number" name="filterAgeMin" class="form-control" ng-model="listProfile.current.attributeFilters.age.min" min="0" aria-label="Minimum Age"/>
                </div>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Max</span>

                    <input type="number" name="filterAgeMax" class="form-control" ng-model="listProfile.current.attributeFilters.age.max" min="0" aria-label="Maximum Age" />
                </div>
                </div>
            </div>

            <div class="col-sm-2">
                <md-checkbox name="filterAgeUnknown" ng-model="listProfile.current.attributeFilters.age.unknown" ng-true-value="true" ng-false-value="false" style="margin-top: 7px">Unknown</md-checkbox>
                <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Some records may not have data for this attribute. Check 'Unknown' to include those records.">help</md-icon>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Gender</label>

        <div layout="row" layout-align="start center">
            <md-checkbox name="filterGenderMale" value="Male" ng-checked="listProfile.genderChecked.Male" ng-click="listProfile.setGender('Male')">
                Male
            </md-checkbox>

            <span flex="5"></span>

            <md-checkbox name="filterGenderFemale" value="Female" ng-checked="listProfile.genderChecked.Female" ng-click="listProfile.setGender('Female')">
                Female
            </md-checkbox>

            <span flex="5"></span>

            <md-checkbox name="filterGenderUnknown" value="Unknown" ng-checked="listProfile.genderChecked.Unknown" ng-click="listProfile.setGender('Unknown')">
                Unknown
            <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="bottom" data-content="Some records may not have data for this attribute. Check 'Unknown' to include those records.">help</md-icon>
            </md-checkbox>
        </div>
    </div>

    <div class="form-group">
        <label>Zip Codes(s)</label>

        <textarea name="zip" class="form-control" placeholder="Zip Code(s)" ng-model="listProfile.current.attributeFilters.zips.include" rows="5"></textarea>
    </div>

    <div class="form-group">
        <label>City/Cities</label>

        <textarea name="city" class="form-control" placeholder="City/Cities" ng-model="listProfile.current.attributeFilters.cities.include" rows="5"></textarea>
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
                <option ng-repeat="(stateId, stateName) in listProfile.selectedFilterStates" ng-value="stateId">@{{stateName}}</option>
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
                <option value="desktop" ng-show="listProfile.deviceTypeFilterVisibility[ 'desktop' ]">Desktop</option>
                <option value="unknown" ng-show="listProfile.deviceTypeFilterVisibility[ 'unknown' ]">Unknown</option>
            </select>
        </div>

        <div class="col-sm-6">
            <label>Selected Device Types</label>

            <div class="pull-right">
                <label ng-click="listProfile.removeDeviceTypeFilters()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
            </div>

            <select ng-model="listProfile.highlightedDeviceTypeFiltersForRemoval" multiple style="width: 100%; height: 75px;">
                <option ng-repeat="( typeId , typeName ) in listProfile.current.attributeFilters.deviceTypes.include" ng-value="typeName">@{{typeName}}</option>
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
                <option ng-repeat="( osId , osName ) in listProfile.current.attributeFilters.os.include" ng-value="osName">@{{osName}}</option>
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
                <option ng-repeat="( carrierId , carrierName ) in listProfile.current.attributeFilters.mobileCarriers.include" ng-value="carrierName">@{{carrierName}}</option>
            </select>
        </div>
    </div>
</div>

<h3 class="bold-text">Suppression
    <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Global suppression should be run through the deploy page. Confirm with manager.">help</md-icon>
</h3>

@if ( Sentinel::inRole( 'admiral' ) )
<div class="row cmp-admiral-feature" ng-show="listProfile.enableAdmiral">
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
<br />
</div>
@endif

@if ( Sentinel::inRole( 'admiral' ) )
<div class="row cmp-admiral-feature" ng-show="listProfile.enableAdmiral">
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
<br />
</div>
@endif

<div class="row">
    <div class="col-sm-6">
        <label>Available Offer Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.addOfferSupp($event)" role="button" tabindex="0">Add Selected <span class="glyphicon glyphicon-plus"></span></label>
        </div>

        <input type="text" 
            style="margin-bottom:5px" 
            placeholder="To search, type at least 3 letters of offer name." 
            name="searchSuppBy" id="searchSuppBy" 
            class="form-control" 
            ng-change="listProfile.search.populateOfferSupp()" 
            ng-model="listProfile.search.offerSupp"  />
        <select ng-model="listProfile.highlightedOfferSupp" multiple style="width: 100%; height: 150px;" ng-options="offer.name for offer in listProfile.search.offerSuppResults" ></select>
    </div>

    <div class="col-sm-6">
        <label>Selected Offer Suppression</label>

        <div class="pull-right">
            <label ng-click="listProfile.removeOfferSupp()" role="button" tabindex="0">Remove Selected <span class="glyphicon glyphicon-minus"></span></label>
        </div>
        <select ng-model="listProfile.highlightedOfferSuppForRemoval" multiple ng-options="offer.name for offer in listProfile.current.suppression.offer" style="width: 100%; height: 150px; margin-top:40px"></select>
    </div>

</div>

<h3 class="bold-text">Attribute Suppression
    <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Optional: Additional suppression to exclude records that meet the selected attributes.">help</md-icon>
</h3>

<div class="form-group">
    <label>City/Cities</label>

    <textarea name="city" class="form-control" placeholder="City/Cities" ng-model="listProfile.current.attributeFilters.cities.exclude"></textarea>
</div>


<div class="form-group">
    <label>Zip Code(s)</label>

    <textarea name="zip" class="form-control" placeholder="Zip Code(s)" ng-model="listProfile.current.attributeFilters.zips.exclude"></textarea>
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
            <option ng-repeat="( stateId , stateName ) in listProfile.selectedSuppStates" ng-value="stateId">@{{stateName}}</option>
        </select>
    </div>
</div>

<h3 class="bold-text">Hygiene <span class="label label-default" style="font-size:12px; vertical-align:middle;"> Coming Soon </span></h3>

<md-checkbox ng-model="listProfile.current.impressionwise" ng-true-value="true" ng-false-value="false" ng-disabled="true">Impressionwise</md-checkbox>

<br />

<md-checkbox ng-model="listProfile.current.tower.run" ng-true-value="true" ng-false-value="false" ng-disabled="true">Tower</md-checkbox>

<div class="form-group" ng-show="listProfile.current.tower.run">
    <label>Cleansed Since</label>

    <div class="row">
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
    </div>
</div>

<h3 class="bold-text">Select and Order Fields
    <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Select the data fields to be included for each record in the exported CSV file.">help</md-icon>
</h3>

<div class="has-error">
    <div class="help-block" ng-show="listProfile.formErrors.selectedColumns">
        <div ng-repeat="error in listProfile.formErrors.selectedColumns">
            <div ng-bind="error"></div>
        </div>
    </div>
</div>

<div class="row draggable-membership-widget">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Available Fields</div>

            <div class="panel-body">
                <ul dnd-list="listProfile.columnList">
                    <li
                        ng-repeat="listItem in listProfile.columnList"
                        dnd-draggable="listItem"
                        dnd-moved="listProfile.columnList.splice( $index , 1 )"
                        dnd-effect-allowed="move"
                        ng-bind="listItem.label"></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Selected Fields</div>

            <div class="panel-body">
                <ul dnd-list="listProfile.current.selectedColumns">
                    <li
                        ng-repeat="listItem in listProfile.current.selectedColumns"
                        dnd-draggable="listItem"
                        dnd-moved="listProfile.current.selectedColumns.splice( $index , 1 )"
                        dnd-effect-allowed="move"
                        ng-bind="listItem.label"></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<br />

<md-checkbox ng-model="listProfile.current.includeCsvHeader" ng-true-value="true" ng-false-value="false">
    Include header line
</md-checkbox>

<h3 class="bold-text">Export Options
    <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Schedule how often a data pull should run for this list profile. The CSV file in FTP will update accordingly. If nothing is selected, schedule will default to 'Never'.">help</md-icon>
</h3>

<div class="row">
    <div class="col-md-2">
        <md-checkbox ng-click="listProfile.toggleExportOption( 'Immediately' )" ng-checked="listProfile.isSelectedExportOption( 'Immediately' )">Immediately
            <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="Selecting this will pull data and drop the CSV file in FTP immediately. This is a one-time event. To manually pull again, edit a list profile and save again.">help</md-icon>
        </md-checkbox>
    </div>

    <div class="col-md-2">
        <md-checkbox ng-click="listProfile.toggleExportOption( 'Daily' )" ng-checked="listProfile.isSelectedExportOption( 'Daily' )">Daily</md-checkbox>
    </div>

    <div class="col-md-2">
        <md-checkbox ng-click="listProfile.toggleExportOption( 'Weekly' )" ng-checked="listProfile.isSelectedExportOption( 'Weekly' )">Weekly</md-checkbox>
    </div>

    <div class="col-md-2">
        <md-checkbox ng-click="listProfile.toggleExportOption( 'Monthly' )" ng-checked="listProfile.isSelectedExportOption( 'Monthly' )">Monthly</md-checkbox>
    </div>

    <div class="col-md-2">
        <md-checkbox ng-click="listProfile.toggleExportOption( 'Never' )" ng-checked="listProfile.isSelectedExportOption( 'Never' )">Never</md-checkbox>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-xs-12" ng-show="listProfile.isSelectedExportOption( 'Weekly' )">
            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Sunday"> Sunday
            </label>

            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Monday"> Monday
            </label>

            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Tuesday"> Tuesday
            </label>

            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Wednesday"> Wednesday
            </label>

            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Thursday"> Thursday
            </label>

            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Friday"> Friday
            </label>

            <label class="radio-inline">
                <input type="radio" name="dailyExportRadio" ng-model="listProfile.current.exportOptions.dayOfWeek" value="Saturday"> Saturday
            </label>
        </div>

        <div class="col-xs-12 form-inline field-top-margin" ng-show="listProfile.isSelectedExportOption( 'Monthly' )">
            <div class="form-group">
                <label>Day of Month</label>

                <input type="number" class="form-control" ng-model="listProfile.current.exportOptions.dayOfMonth" min="1" max="31" step="1" />
            </div>
        </div>
    </div>
</div>
