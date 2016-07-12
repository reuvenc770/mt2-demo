<div class="md-whiteframe-4dp" style="background-color:#FFF;">
    <form name="attrModelForm" novalidate>
        <md-toolbar layout="row" class="md-hue-3">
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

        <md-toolbar layout="row" class="md-hue-3">
          <div class="md-toolbar-tools">
            <span>Client Attribution Levels</span>
          </div>
        </md-toolbar>

        <md-content>
            <ul dnd-list="attr.clients"
                dnd-horizontal-list="true"
                layout="column"
                flex
            >
                <li 
                    ng-repeat="client in attr.clients"
                    dnd-draggable="client"
                    dnd-moved="attr.clients.splice($index, 1)"
                    dnd-effect-allowed="move"
                    flex
                >
                    <div layout="row">
                        <span flex="10"></span>

                        <md-icon md-svg-src="img/icons/ic_drag_handle_black_18px.svg"></md-icon>

                        <span flex></span>

                        <p ng-bind="client.name"></p>

                        <span flex></span>

                        <p ng-bind="$index + 1"></p>

                        <span flex="10"></span>
                    </div>

                    <md-divider ng-if="!$last"></md-divider>
                </li>
            </ul>
        </md-content>
    </form>
</div>
