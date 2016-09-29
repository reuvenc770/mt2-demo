<md-card class="md-mt2-zeta-theme" flex layout="column">
    <md-toolbar class="md-table-toolbar md-mt2-zeta-theme md-hue-2">
        <div class="md-toolbar-tools">
            <span>Models</span>

            <span flex></span>

            <span ng-show="attr.showModelActions">
                @if (Sentinel::hasAccess('attributionProjection.show'))
                <md-button ng-class="{ 'md-icon-button' : app.isMobile() }" ng-hide="attr.disableProjection" ng-href="@{{ 'attribution/projection/' + attr.selectedModelId }}" target="_self"><md-icon md-svg-src="img/icons/ic_show_chart_white_18px.svg"></md-icon> <span hide-xs hide-sm>Projection</span></md-button>
                @endif

                @if (Sentinel::hasAccess('api.attribution.run'))
                <md-button ng-class="{ 'md-icon-button' : app.isMobile() }" ng-hide="attr.disableProjection" ng-click="attr.runAttribution( true )"><md-icon md-svg-src="img/icons/ic_cached_white_18px.svg"></md-icon> <span hide-xs hide-sm>Run Attribution</span></md-button>
                @endif

                @if (Sentinel::hasAccess('attributionModel.edit'))
                <md-button ng-class="{ 'md-icon-button' : app.isMobile() }" ng-href="@{{ 'attribution/edit/' + attr.selectedModelId }}" target="_self"><md-icon md-svg-src="img/icons/ic_mode_edit_white_18px.svg"></md-icon> <span hide-xs hide-sm>Edit</span></md-button>
                @endif

                @if (Sentinel::hasAccess('api.attribution.model.setlive'))
                <md-button ng-class="{ 'md-icon-button' : app.isMobile() }" ng-hide="attr.disableProjection" ng-click="attr.setModelLive()"><md-icon md-svg-src="img/icons/ic_send_white_18px.svg"></md-icon> <span hide-xs hide-sm>Set Live</span></md-button>   
                @endif
            </span>
        </div>
    </md-toolbar>

    <md-content>
        <md-table-container>
            <table md-table md-progress="attr.modelQueryPromise" md-row-select ng-model="attr.selectedModel">
                <thead md-head md-order="" md-on-reorder="">
                    <tr md-row>
                        <th md-column md-order-by="" class="md-table-header-override-whitetext">Name</th>
                        <th md-column md-order-by="" class="md-table-header-override-whitetext">Processing</th>
                        <th md-column md-order-by="" class="md-table-header-override-whitetext">Created</th>
                        <th md-column md-order-by="" class="md-table-header-override-whitetext">Updated</th>
                    </tr>
                </thead>

                <tbody md-body>
                    <tr
                        md-row
                        md-auto-select
                        md-select="model"
                        md-select-id="id"
                        md-on-select="attr.toggleModelActionButtons"
                        md-on-deselect="attr.toggleModelActionButtons"
                        multiple="false"
                        ng-class="{ 'mt2-live-row' : model.live == 1 }"
                        ng-repeat="model in attr.models track by $index">
                            <td md-cell ng-bind="model.name"></td>
                            <td md-cell ng-bind="model.processing ? 'Running' : 'Completed'"></td>
                            <td md-cell ng-bind="model.created_at"></td>
                            <td md-cell ng-bind="model.updated_at"></td>
                    </tr>
                </tbody>
            </table>
        </md-table-container>

        <md-table-pagination md-limit="" md-limit-options="[5,10,25,50]" md-page="" md-total="" md-on-paginate="" md-page-select></md-table-pagination>
    </md-content>
</md-card>
