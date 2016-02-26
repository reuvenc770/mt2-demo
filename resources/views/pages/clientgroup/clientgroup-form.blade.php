<div class="panel panel-primary" ng-init="clientGroup.prepopPage()">
    <div class="panel-heading">
        <h3 class="panel-title">Client Group Details</h3>
    </div>

    <div class="panel-body">
        <div class="form-group" ng-class="{ 'has-error' : clientGroup.formErrors.groupName }">
            <input type="text" class="form-control" id="groupName" value="" placeholder="Client Group Name" ng-model="clientGroup.current.groupName" required="required" />

            <span class="help-block" ng-bind="clientGroup.formErrors.groupName" ng-show="clientGroup.formErrors.groupName"></span>
        </div>

        <md-card>
            <md-card-content>
                    <div id="clientChipList">
                        <md-chips
                            ng-model="clientGroup.clientChipList"
                            md-on-remove="clientGroup.removeClientChip( $chip )"
                            md-transform-chip="clientGroup.formatChip( $chip )">
                                <md-autocomplete
                                    md-items="item in clientGroup.searchClient( clientGroup.typeSearchText )"
                                    md-search-text="clientGroup.typeSearchText"
                                    md-item-text="item.name"
                                    md-selected-item-change="clientGroup.updateClientCheckboxList( item )"
                                    md-min-length="0"
                                    placeholder="Pick a Client"
                                    secondary-placeholder="+Client"
                                    md-selected-item="clientGroup.currentSelectedClient">
                                        <span md-highlight-text="clientGroup.typeSearchText" md-highlight-flags="^i">(@{{ item.client_id }}) - @{{ item.username }}</span> 
                                </md-autocomplete>

                                <md-chip-template>
                                    <span>
                                        <strong>@{{ $chip.name }}</strong>
                                        <em>( @{{ $chip.id }} )</em>
                                    <span>
                                </md-chip-template>
                        </md-chips>
                    </div>

                    <md-content flex="100" id="clientChipBucket" layout-padding>
                        <md-list>
                            <md-list-item ng-repeat="client in clientGroup.clientList" md-on-demand flex>
                                <div class="md-list-item">
                                    <div layout="row">
                                        <div layout="column">
                                            <md-checkbox
                                                ng-model="selectedClients[ client.client_id ]"
                                                ng-true-value="'@{{ client.username }}'"
                                                aria-label="@{{ client.username }}">
                                            </md-checkbox>
                                        </div>
                                        <div layout="column">
                                            <div flex>
                                                <strong>@{{ client.username }}</strong>
                                            </div>

                                            <div flex>
                                                <span>@{{ client.client_id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <md-divider ng-if="!$last"></md-divider>
                            </md-list-item>
                        </md-list>
                    </md-content>

            </md-card-content>
        </md-card>

        <div class="form-group">
            <md-switch ng-true-value="'Y'" ng-false-value="'N'" ng-model="clientGroup.current.excludeFromSuper" aria-label="Exclude From Super">Exclude From Super</md-switch>
        </div>
    </div>
</div>
