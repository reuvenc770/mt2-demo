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
            <span>Client Attribution Levels</span>
          </div>
        </md-toolbar>

        <md-content>
            <md-list class="md-dense" flex>
                <md-list-item ng-repeat="feed in attr.feeds track by $index" ng-class="{ 'mt2-proj-increase-bg' : attr.clientLevels[ feed.id ] > ( $index + 1 ) , 'mt2-proj-decrease-bg' : attr.clientLevels[ feed.id ] < ( $index + 1 ) }">
                    <md-checkbox ng-model="feed.selected"></md-checkbox>

                    <div class="md-list-item-text" layout="column">
                        <h4 ng-bind="feed.name"></h4>

                        <p>Current Level: @{{ $index + 1 }} <span ng-show="attr.clientLevels[ feed.id ] !== ( $index + 1 )"> - Original Level: @{{ attr.clientLevels[ feed.id ] }}</span></p>
                    </div>

                    <span flex></span>

                    <md-icon class="md-secondary" ng-click="attr.moveToTop( feed , $index )" aria-label="Move To Top" md-svg-icon="img/icons/ic_vertical_align_top_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.moveToMiddle( feed , $index )" aria-label="Move To Middle" md-svg-icon="img/icons/ic_vertical_align_center_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.moveToBottom( feed , $index )" aria-label="Move to Bottom" md-svg-icon="img/icons/ic_vertical_align_bottom_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.onLevelRise( feed , $index )" aria-label="Move Feed Up" md-svg-icon="img/icons/ic_arrow_upward_black_18px.svg"></md-icon>

                    <md-icon class="md-secondary" ng-click="attr.onLevelDrop( feed , $index )" aria-label="Move Feed Down" md-svg-icon="img/icons/ic_arrow_downward_black_18px.svg"></md-icon>
                </md-list-item>
            </md-list>
        </md-content>
    </form>
</div>
