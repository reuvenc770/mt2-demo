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
            <!--
            <md-content layout-padding style="margin-bottom: 1em;">
                <h4 layout flex layout-align="center center"><span>Client Group</span></h4>

                <md-divider></md-divider>

                <md-content>
                    <ui-select ng-model="listProfile.selectedClientGroup" theme="selectize" ng-init="listProfile.loadClientGroups()">
                        <ui-select-match placeholder="Choose a Client Group">
                          @{{$select.selected.name}}
                        </ui-select-match>
                        <ui-select-choices 
                          refresh="listGroup.fetchClientGroups($select)" 
                          refresh-delay="300" 
                          repeat="item in items | filter: $select.search"
                        >
                          @{{$index}} - @{{item.full_name}}
                          <div ng-if="$index == $select.items.length-1">
                            <button 
                              class="btn btn-xs btn-success" 
                              style="width: 100%; margin-top: 5px;" 
                              ng-click="listGroup.fetchClientGroups($select, $event);"
                              ng-disabled="listProfile.clientGroupLoading">Load more...</button>                 
                          </div>
                        </ui-select-choices>
                    </ui-select>
                </md-content>

            </md-content>
            -->

            <md-content layout-padding style="margin-bottom: 1em;" ng-if="listProfile.showVersionField" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Client Group</span></h4>

                <md-divider></md-divider>

                <md-content>
                    <md-autocomplete
                        md-search-text="listProfile.clientGroupSearchText"
                        md-items="item in listProfile.getClientGroups( listProfile.clientGroupSearchText )"
                        md-item-text="item.name"
                        md-min-length="0"
                        placeholder="Choose a Client Group"
                        md-selected-item="listProfile.current.cgroupid"
                        style="margin-bottom: 1em;">

                        <md-item-template>
                            <span md-highlight-text="listProfile.clientGroupSearchText" md-highlight-flags="^i">@{{ item.name }}</span>
                        </md-item-template>

                        <md-not-found></md-not-found>
                    </md-autocomplete>
                </md-content>
            </md-content>

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

                <md-content class="chipList">
                    <div layout="row">
                        <md-button flex ng-click="listProfile.selectAllIsps( true )">Select All</md-button>
                        <md-button flex ng-click="listProfile.selectAllIsps( false )">Clear All</md-button>
                    </div>

                    <md-chips ng-model="listProfile.ispChipList" md-on-remove="listProfile.removeIspChip( $chip )">
                        <md-autocomplete
                            md-search-text="listProfile.ispSearchText"
                            md-items="item in listProfile.getIsps( listProfile.ispSearchText )"
                            md-item-text="item.name"
                            md-min-length="0"
                            placeholder="Choose an ISP"
                            md-selected-item="listProfile.currentSelectedIsp"
                            md-selected-item-change="listProfile.updateIspCheckboxList( item )"
                            style="margin-bottom: 1em;">

                            <span md-highlight-text="listProfile.ispSearchText" md-highlight-flags="^i">@{{ item.name }}</span>

                            <md-not-found></md-not-found>
                        </md-autocomplete>

                        <md-chip-template>
                            <span>
                                <strong>@{{ $chip.name }}</strong>
                                <em>( @{{ $chip.id }} )</em>
                            </span>
                        </md-chip-template>
                    </md-chips>
                </md-content>
                
                <md-content class="chipBucket">
                    <md-list>
                        <md-list-item ng-repeat="isp in listProfile.ispList">
                            <div class="md-list-item">
                                <div layout="row">
                                    <div layout="column">
                                        <md-checkbox ng-model="selectedIsps[ isp.id ]" ng-true-value="'@{{ isp.name }}'" aria-label="@{{ isp.name }}"></md-checkbox>
                                    </div>

                                    <div layout="column">
                                        <div flex><strong>@{{ isp.name }}</strong></div>
                                        <div flex><span>@{{ isp.id }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <md-divider ng-if="!$last"></md-divider>
                        </md-list-item>
                    </md-list>
                </md-content>
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

            <!-- Change to TextArea
            <md-content layout-padding style="margin-bottom: 1em;" ng-cloak>
                <h4 layout flex layout-align="center center"><span>Zip Codes</span></h4>

                <md-divider></md-divider>

                    <md-chips ng-model="listProfile.zipList" placeholder="Enter a Zipcode" readonly="false" flex md-transform-chip="listProfile.preventDelimitedChips( 'zipList' , $chip )">
                        <md-chip-template>
                            <span>
                                <strong>@{{ $chip }}</strong>
                            </span>
                        </md-chip-template>
                    </md-chips>
            </md-content>
            -->
        </div>
    </div>
</div>
