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

        <div class="form-group">
            <input type="text" class="form-control" id="groupName" value="" ng-model="listProfile.current.profile_name" placeholder="List Profile Name" />
        </div>

        <div class="form-group" ng-if="listProfile.profileType !== 'v1'">
            <input type="text" class="form-control" id="volumeDesired" value="" ng-model="listProfile.current.volume_desired" placeholder="Volume Desired" />
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

            <md-content layout-padding style="margin-bottom: 1em;">
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

            <md-content layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>ISPs</span></h4>

                <md-divider></md-divider>

                <div flex layout-xs="column" layout-md="column" layout="row" layout-align="space-between">
                    <md-card flex>
                        <md-toolbar>
                            <div class="md-toolbar-tools">
                                <h3>Available ISPs</h3>
                                
                                <span flex></span>

                                <md-button class="md-icon-button md-primary" aria-label="Select All" ng-click="listProfile.selectAllAvailableIsps( listProfile.ispList )">
                                    <md-tooltip md-direction="bottom">Select All</md-tooltip>

                                    <md-icon md-svg-icon="img/icons/ic_select_all_white_36px.svg"></md-icon>
                                </md-button>

                                <md-button class="md-icon-button md-primary" aria-label="Clear" ng-click="listProfile.clearAllAvailableIsps( listProfile.ispList )">
                                    <md-tooltip md-direction="bottom">Clear Selected</md-tooltip>

                                    <md-icon md-svg-icon="img/icons/ic_clear_white_36px.svg"></md-icon>
                                </md-button>

                                <md-button class="md-icon-button md-primary" aria-label="Add Selected" ng-click="listProfile.addSelectedIsps()">
                                    <md-tooltip md-direction="bottom">Add Selected</md-tooltip>

                                    <md-icon md-svg-icon="img/icons/ic_add_circle_outline_white_36px.svg"></md-icon>
                                </md-button>
                            </div>
                        </md-toolbar>

                        <md-content class="membershipBox">
                            <md-list>
                                <md-list-item ng-repeat="isp in listProfile.ispList track by $index" ng-hide="isp.chosen">
                                    <div class="md-list-item" ng-click="listProfile.ispMultiSelect( isp , $index , listProfile.ispList , $event )" flex>
                                        <div layout="row">
                                            <md-checkbox aria-label="@{{ isp.name }}" ng-true-value="true" ng-model="isp.selected" ng-click="listProfile.ispMultiSelect( isp , $index , listProfile.ispList , $event )"></md-checkbox>
                                            <div class="md-list-item-text" flex>
                                                <h5>@{{ isp.name }}</h5>
                                                <span>@{{ isp.id }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <md-button class="md-icon-button md-primary" aria-label="Add @{{ isp.name }}" ng-click="listProfile.addSingleIsp( isp )">
                                        <md-tooltip md-direction="left">Add @{{ isp.name }}</md-tooltip>

                                        <md-icon md-svg-icon="img/icons/ic_add_circle_outline_black_36px.svg"></md-icon>
                                    </md-button>
                                </md-list-item>
                            </md-list>
                        </md-content>
                    </md-card>

                    <md-card flex>
                        <md-toolbar>
                            <div class="md-toolbar-tools">
                                <h3>Chosen ISPs</h3>
                                
                                <span flex></span>

                                <md-button class="md-icon-button md-primary" aria-label="Select All" ng-click="listProfile.selectAllAvailableIsps( listProfile.selectedIsps )">
                                    <md-tooltip md-direction="bottom">Select All</md-tooltip>

                                    <md-icon md-svg-icon="img/icons/ic_select_all_white_36px.svg"></md-icon>
                                </md-button>

                                <md-button class="md-icon-button md-primary" aria-label="Clear Selected" ng-click="listProfile.clearAllAvailableIsps( listProfile.selectedIsps )">
                                    <md-tooltip md-direction="bottom">Clear Selected</md-tooltip>

                                    <md-icon md-svg-icon="img/icons/ic_clear_white_36px.svg"></md-icon>
                                </md-button>

                                <md-button class="md-icon-button md-primary" aria-label="Remove Selected" ng-click="listProfile.removeAllSelectedChosenIsps()">
                                    <md-tooltip md-direction="bottom">Remove Selected</md-tooltip>

                                    <md-icon md-svg-icon="img/icons/ic_remove_circle_outline_white_36px.svg"></md-icon>
                                </md-button>
                            </div>
                        </md-toolbar>

                        <md-content class="membershipBox">
                            <md-list>
                                <md-list-item ng-repeat="isp in listProfile.selectedIsps track by $index">
                                    <div class="md-list-item" ng-click="listProfile.ispMultiSelect( isp , $index , listProfile.selectedIsps , $event )" flex>
                                        <div layout="row">
                                            <md-checkbox aria-label="@{{ isp.name }}" ng-true-value="true" ng-model="isp.selected" ng-click="listProfile.ispMultiSelect( isp , $index , listProfile.selectedIsps , $event )"></md-checkbox>
                                            <div class="md-list-item-text" flex>
                                                <h5>@{{ isp.name }}</h5>
                                                <span>@{{ isp.id }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <md-button class="md-icon-button md-primary" aria-label="Remove @{{ isp.name }}" ng-click="listProfile.removeSingleChosenIsp( isp )">
                                        <md-tooltip md-direction="left">Remove @{{ isp.name }}</md-tooltip>

                                        <md-icon md-svg-icon="img/icons/ic_remove_circle_outline_black_36px.svg"></md-icon>
                                    </md-button>
                                </md-list-item>
                            </md-list>
                        </md-content>
                    </md-card>
                </div>
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
