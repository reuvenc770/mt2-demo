<div class="md-whiteframe-4dp" style="background-color:#FFF;">
    <form name="attrModelForm" novalidate>
        <md-toolbar layout="row" class="md-mt2-zeta-theme md-hue-2">
          <div class="md-toolbar-tools">
            <span>Attribution Model Details</span>
          </div>
        </md-toolbar>

        <md-content layout-padding>
            <div flex>
                <md-input-container class="md-block">
                    <label>Model Name</label>

                    <input type="text" name="modelName" ng-model="attr.current.name" ng-required="true" />

                    <div class="bg-danger" ng-show="attrModelForm.modelName.$dirty" ng-messages="attrModelForm.modelName.$error">
                    <div ng-message="required">Model Name is required.</div>
                    </div>
                </md-input-container>
            </div>
        </md-content>

        <md-toolbar layout="row" class="md-mt2-zeta-theme md-hue-2">
          <div class="md-toolbar-tools">
            <span flex="2"></span>

            <md-button class="md-icon-button md-primary" aria-label="Clear Checkboxes" ng-click="attr.resetLevelFields()" flex="4" flex-offset="6">
                <md-tooltip md-direction="bottom">Clear Selected</md-tooltip>

                <md-icon md-svg-icon="img/icons/ic_clear_white_36px.svg"></md-icon>
            </md-button>

            <span>Client Attribution Levels</span>

            <span flex></span>

            <span ng-bind="attr.feeds.length + ' Feeds'"></span>
          </div>
        </md-toolbar>

        <md-content>
            <md-list class="md-dense" flex ng-cloak>
                <md-list-item ng-repeat="feed in attr.feeds track by $index" class="md-no-proxy" ng-class="{ 'mt2-proj-increase-bg' : attr.clientLevels[ feed.id ] > ( $index + 1 ) , 'mt2-proj-decrease-bg' : attr.clientLevels[ feed.id ] < ( $index + 1 ) }">
                    <md-checkbox ng-model="feed.selected" aria-label="Feed Checkbox"></md-checkbox>

                    <div layout="column" layout-gt-lg="row" flex="noshrink" flex-gt-lg="100">
                        <div class="md-list-item-text" layout="column" flex="noshrink" flex-gt-lg="40">
                            <h4 ng-bind="feed.name"></h4>
                        </div>

                        <div layout="row" layout-align="start center" layout-align-gt-md="end center" flex="10">
                            <input ng-init="feed.newLevel = $index + 1" ng-model="feed.newLevel" style="width:50px;" />

                            <md-button ng-click="attr.changeLevel( feed , $index )">Change</md-button>
                        </div>
                    </div>

                    <md-icon class="md-secondary" ng-click="attr.onLevelRise( feed , $index )" aria-label="Move Feed Up" md-svg-icon="img/icons/ic_arrow_upward_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.onLevelDrop( feed , $index )" aria-label="Move Feed Down" md-svg-icon="img/icons/ic_arrow_downward_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.moveToTop( feed , $index )" aria-label="Move To Top" md-svg-icon="img/icons/ic_vertical_align_top_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.moveToBottom( feed , $index )" aria-label="Move to Bottom" md-svg-icon="img/icons/ic_vertical_align_bottom_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.confirmDeletion( feed.id )" aria-label="Move to Bottom" md-svg-icon="img/icons/ic_delete_black_18px.svg"></md-icon>
                </md-list-item>
            </md-list>
        </md-content>
    </form>
</div>
