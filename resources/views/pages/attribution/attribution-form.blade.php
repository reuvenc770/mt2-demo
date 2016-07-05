<div class="md-whiteframe-4dp" style="background-color:#FFF;">
    <md-toolbar layout="row" class="md-hue-3">
      <div class="md-toolbar-tools">
        <span>Attribution Model Details</span>
      </div>
    </md-toolbar>

    <md-content layout-padding>
        <div flex>
            <md-input-container class="md-block">
                <label>Model Name</label>

                <input type="text" name="modelName" ng-model="attr.current.name" required />
            </md-intput-container>
        </div>
    </md-content>

    <md-toolbar layout="row" class="md-hue-3">
      <div class="md-toolbar-tools">
        <span>Client Attribution Levels</span>
      </div>
    </md-toolbar>

    <md-content layout-padding>
        <md-list class="md-dense">
            <md-list-item class="largeListItem layout-padding" ng-repeat="client in attr.clients track by $index" layout-align="center center">
                <p>@{{ client.username }}</p>

                <span flex></span>

                <md-input-container class="md-block">
                    <label>Attribution Level</label>
                    <input required type="number" step="any" name="attribution_level" ng-model="client.attribution_level" required />
                </md-input-container>

                <md-divider ng-if="!$last"></md-divider>
            </md-list-item>
        </md-list>
    </md-content>
</div>
