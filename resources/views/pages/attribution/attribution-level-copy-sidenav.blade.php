<md-sidenav md-component-id="levelCopy" class="md-sidenav-right">
        <md-toolbar>
            <div class="md-toolbar-tools" layout-align="center center">
                <h2><span>Copy Model Levels</span></h2>
            </div>
        </md-toolbar>

        <md-toolbar class="md-accent">
            <div class="md-toolbar-tools" layout-align="center center">
                <h2><span>Selected Model</span></h2>
            </div>
        </md-toolbar>

        <md-content layout-padding>
            <md-input-container>
                <md-select ng-init="attr.initLevelCopyPanel()" ng-model="attr.levelCopyModelId" placeholder="Please Choose a Model">
                    <md-option ng-repeat="model in attr.models" ng-value="model.id">@{{ model.name }}</md-option>
                </md-select>
            </md-input-container>

            <md-button class="md-primary md-raised md-hue-2" ng-click="attr.loadLevelPreview()">Load Levels</md-button>
        </md-content>

        <md-toolbar class="md-accent">
            <div class="md-toolbar-tools" layout-align="center center">
                <h2><span>Model Level Preview</span></h2>
            </div>
        </md-toolbar>

        <md-content layout-padding>
            <md-button class="md-primary md-raised md-hue-2" ng-disabled="attr.disableCopyButton" ng-click="attr.copyLevels()">Copy Levels</md-button>

            <md-list class="md-dense">
                <md-list-item ng-repeat="client in attr.levelCopyClients">
                    <div layout="row" flex>
                        <div>
                           @{{ attr.clientLevels[ client.id ] }}
                                <md-icon ng-show="attr.clientLevels[ client.id ] == ( $index + 1 )" md-svg-src="img/icons/ic_chevron_right_black_18px.svg"></md-icon>
                                <md-icon ng-show="attr.clientLevels[ client.id ] < ( $index + 1 )" md-svg-src="img/icons/ic_chevron_right_red_18px.svg"></md-icon>
                                <md-icon ng-show="attr.clientLevels[ client.id ] > ( $index + 1 )" md-svg-src="img/icons/ic_chevron_right_green_18px.svg"></md-icon> @{{ $index + 1 }}
                        </div>

                        <div layout="row" layout-align="center center" flex>
                            <div ng-bind="client.name"></div>
                        </div>
                    </div>
                </md-list-item>
            </md-list>
        </md-content>
</md-sidenav>
