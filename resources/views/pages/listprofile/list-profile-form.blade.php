<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">List Profile Details</h3>
    </div>

    <div class="panel-body">
        <div flex layout="column" style="margin-bottom: 1em;" ng-if="listProfile.showVersionField">
            <md-content style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>List Profile Type</span></h4>

                <md-divider></md-divider>

                <md-radio-group layout layout-padding layout-align="space-around center" ng-model="listProfile.profileType">
                    <md-radio-button value="v1" aria-label="V1"><span>V1</span></md-radio-button>
                    <md-radio-button value="v2" aria-label="V2"><span>V2</span></md-radio-button>
                    <md-radio-button value="v3" aria-label="V3"><span>V3</span></md-radio-button>
                </md-radio-group>
            </md-content>
        </div>

        <div class="form-group" ng-class="{ 'has-error' : ( profileForm.profileName.$touched && profileForm.profileName.$error.required ) }">
            <input name="profileName" type="text" class="form-control" id="groupName" value="" ng-model="listProfile.current.profile_name" placeholder="List Profile Name" required />

            <div ng-show="profileForm.profileName.$touched">
                <span class="help-block" ng-show="profileForm.profileName.$error.required">Profile Name is Required</span>
            </div>
        </div>

        <div class="form-group" ng-if="listProfile.profileType !== 'v1'" ng-class="{ 'has-error' : ( profileForm.volumeDesired.$touched && profileForm.volumeDesired.$error.required ) }">
            <input name="volumeDesired" type="text" class="form-control" id="volumeDesired" value="" ng-model="listProfile.current.volume_desired" placeholder="Volume Desired" required />

            <div ng-show="profileForm.volumeDesired.$touched">
                <span class="help-block" ng-show="profileForm.volumeDesired.$error.required">Volume Desired is Required</span>
            </div>
        </div>

        <div flex layout="column" style="margin-bottom: 1em;">
            <div flex layout-padding style="margin-bottom: 1em;">
                <h4 layout flex layout-align="center center"><span>Client Group</span></h4>

                <md-divider></md-divider>

                <select ng-model="listProfile.selectedClientGroup" placeholder="Select Client Group" class="form-control">
                    @foreach ( $clientGroups as $current )
                    <option ng-value="{{ $current->id }}">{{ $current->name }}</option>
                    @endforeach
                </select>
            </div>

            <md-content id="range" layout-padding style="margin-bottom: 1em;">
                <h4 layout flex layout-align="center center"><span>Ranges</span></h4>

                <md-divider></md-divider>

                <md-chips ng-model="listProfile.rangeList" md-transform-chip="listProfile.filterRangeChips( $chip )" md-on-remove="listProfile.removeRangeChip( $chip )" placeholder="Choose a Type Below" secondary-placeholder="Choose Another Range">
                    <md-chip-template>
                        <span>@{{ ( $chip.subtype ? $chip.subtype + " " : "" ) + $chip.type }}: @{{ $chip.min }} - @{{ $chip.max }}</span>
                    </md-chip-template>
                </md-chips>

                <md-content layout="row" flex ng-cloak>
                    <md-tabs md-dynamic-height md-no-pagination md-stretch-tabs='always' flex>
                        <md-tab>
                            <md-tab-label>Count Range</md-tab-label>

                            <md-tab-body>
                                <md-list>
                                    <md-list-item>
                                        <md-button class="md-raised" flex ng-click="listProfile.addCountRange( $event , 'count' , 'age' )" ng-attr-disabled="@{{ listProfile.rangeData.count.age.filled === true || undefined }}"><span class="glyphicon glyphicon-plus"></span> Age</md-button>
                                    </md-list-item>

                                    <md-list-item ng-if="listProfile.profileType !== 'v3'">
                                        <md-button class="md-raised" flex ng-click="listProfile.addCountRange( $event , 'count' , 'deliverable' )" ng-attr-disabled="@{{ ( listProfile.rangeData.count.deliverable[ 0 ].filled === true && listProfile.rangeData.count.deliverable[ 1 ].filled === true && listProfile.rangeData.count.deliverable[ 2 ].filled === true ) || ( listProfile.profileType === 'v2' && listProfile.rangeData.count.deliverable[ 0 ].filled === true ) || undefined }}"><span class="glyphicon glyphicon-plus"></span> Deliverables</md-button>
                                    </md-list-item>

                                    <md-list-item ng-if="listProfile.profileType !== 'v3'">
                                        <md-button class="md-raised" flex ng-click="listProfile.addCountRange( $event , 'count' , 'openers' )" ng-attr-disabled="@{{ ( listProfile.rangeData.count.openers[ 0 ].filled === true && listProfile.rangeData.count.openers[ 1 ].filled === true && listProfile.rangeData.count.openers[ 2 ].filled === true ) || ( listProfile.profileType === 'v2' && listProfile.rangeData.count.openers[ 0 ].filled === true ) || undefined }}"><span class="glyphicon glyphicon-plus"></span> Openers</md-button>
                                    </md-list-item>

                                    <md-list-item ng-if="listProfile.profileType !== 'v3'">
                                        <md-button class="md-raised" flex ng-click="listProfile.addCountRange( $event , 'count' , 'clickers' )" ng-attr-disabled="@{{ ( listProfile.rangeData.count.clickers[ 0 ].filled === true && listProfile.rangeData.count.clickers[ 1 ].filled === true && listProfile.rangeData.count.clickers[ 2 ].filled === true ) || ( listProfile.profileType === 'v2' && listProfile.rangeData.count.clickers[ 0 ].filled === true ) || undefined }}"><span class="glyphicon glyphicon-plus"></span> Clickers</md-button>
                                    </md-list-item>

                                    <md-list-item ng-if="listProfile.profileType !== 'v3'">
                                        <md-button class="md-raised" flex ng-click="listProfile.addCountRange( $event , 'count' , 'converters' )" ng-attr-disabled="@{{ ( listProfile.rangeData.count.converters[ 0 ].filled === true && listProfile.rangeData.count.converters[ 1 ].filled === true && listProfile.rangeData.count.converters[ 2 ].filled === true ) || ( listProfile.profileType === 'v2' && listProfile.rangeData.count.converters[ 0 ].filled === true ) || undefined }}"><span class="glyphicon glyphicon-plus"></span> Converters</md-button>
                                    </md-list-item>
                                </md-list>
                            </md-tab-body>
                        </md-tab>

                        <md-tab ng-if="listProfile.profileType !== 'v3'">
                            <md-tab-label>Date Range</md-tab-label>

                            <md-tab-body>
                                <md-list>
                                    <md-list-item>
                                        <md-button class="md-raised" flex ng-click="listProfile.addDateRange( $event , 'deliverable' )" ng-attr-disabled="@{{ ( listProfile.rangeData.date.deliverable.filled === true || undefined ) }}"><span class="glyphicon glyphicon-plus"></span> Deliverables</md-button>
                                    </md-list-item>

                                    <md-list-item>
                                        <md-button class="md-raised" flex ng-click="listProfile.addDateRange( $event , 'openers' )" ng-attr-disabled="@{{ ( listProfile.rangeData.date.openers.filled === true || undefined ) }}"><span class="glyphicon glyphicon-plus"></span> Openers</md-button>
                                    </md-list-item>

                                    <md-list-item flex>
                                        <md-button class="md-raised" flex ng-click="listProfile.addDateRange( $event , 'clickers' )" ng-attr-disabled="@{{ ( listProfile.rangeData.date.clickers.filled === true || undefined ) }}"><span class="glyphicon glyphicon-plus"></span> Clickers</md-button>
                                    </md-list-item>

                                    <md-list-item flex>
                                        <md-button class="md-raised" flex ng-click="listProfile.addDateRange( $event , 'converters' )" ng-attr-disabled="@{{ ( listProfile.rangeData.date.converters.filled === true || undefined ) }}"><span class="glyphicon glyphicon-plus"></span> Converters</md-button>
                                    </md-list-item>
                                </md-list>
                            </md-tab-body>
                        </md-tab>
                    </md-tabs>
                </md-content>
            </md-content>

            <md-content id="isp" layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>ISPs</span></h4>

                <md-divider></md-divider>

                <membership-widget recordlist="listProfile.ispList" chosenrecordlist="listProfile.selectedIsps" availablecardtitle="listProfile.availableWidgetTitle" chosenrecordtitle="listProfile.chosenWidgetTitle" ng-init="listProfile.loadIsps()" widgetname="'isps'"></membership-widget>

            </md-content>

            <md-content layout-padding style="margin-bottom: 1em;" ng-if="listProfile.profileType === 'v1'" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Delivery Days</span></h4>

                <md-divider></md-divider>

                <md-content flex layout layout-align="space-around center">
                    <div layout flex>
                        <md-slider id="delivery-days-slider" flex ng-model="listProfile.current.deliveryDays" min="0" max="365" aria-label="Delivery Days"></md-slider>
                    </div>

                    <div layout flex="10">
                        <input flex type="number" ng-model="listProfile.current.deliveryDays" aria-label="Delivery Days" aria-controls="delivery-days-slider" ng-value="listProfile.current.deliveryDays"  />
                    </div>
                </md-content>
            </md-content>

            <md-content layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Gender</span></h4>

                <md-divider></md-divider>

                <div layout>
                    <md-radio-group ng-model="listProfile.genderType" layout layout-padding layout-align="space-around center" ng-model="listProfile.current.profileType">
                        <md-radio-button value="any" aria-label="Any Gender"><span>Any Gender</span></md-radio-button>

                        <md-radio-button value="empty" aria-label="Empty"><span>Empty</span></md-radio-button>

                        <md-radio-button value="specific" aria-label="Specific"><span>Specific</span></md-radio-button>

                        <md-switch ng-model="listProfile.current.gender" aria-label="Gender" ng-disabled="listProfile.genderType != 'specific'" ng-true-value="'F'" ng-false-value="'M'">
                            <span ng-if="listProfile.genderType == 'specific'">Target: @{{ listProfile.current.gender === 'F' ? 'Female' : 'Male' }}</span>
                        </md-switch>
                    </md-radio-group>
                </div>
            </md-content>

            <md-content layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Source URL</span></h4>

                <md-divider></md-divider>

                <md-chips ng-model="listProfile.sourceList" placeholder="Enter a Source URL" readonly="false" flex md-transform-chip="listProfile.preventDelimitedChips( 'sourceList' , $chip )">
                    <md-chip-template>
                        <span>
                            <strong>@{{ $chip }}</strong>
                        </span>
                    </md-chip-template>
                </md-chips>
            </md-content>

            <md-content layout-padding style="margin-bottom: 1em;" ng-if="listProfile.profileType === 'v1'" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Seeds</span></h4>

                <md-divider></md-divider>

                <md-chips ng-model="listProfile.seedList" placeholder="Enter a Seed" readonly="false" flex flex md-transform-chip="listProfile.preventDelimitedChips( 'seedList' , $chip )">
                    <md-chip-template>
                        <span>
                            <strong>@{{ $chip }}</strong>
                        </span>
                    </md-chip-template>
                </md-chips>
            </md-content>

            <md-content layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Zip Codes</span></h4>

                <md-divider></md-divider>

                <md-input-container class="md-block">
                    <textarea ng-model="listProfile.zipList" md-select-on-focus placeholder="Enter Zip Codes"></textarea>
                </md-input-container>
            </md-content>
        </div>
    </div>
</div>
