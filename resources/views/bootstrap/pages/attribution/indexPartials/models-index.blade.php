<div class="navbar navbar-topper navbar-primary" role="navigation">
    <div class="container-fluid">

        <ul class="nav navbar-nav navbar-right" ng-show="attr.showModelActions">
                @if (Sentinel::hasAccess('attributionProjection.show'))
                    <li> <a ng-hide="attr.disableProjection" ng-href="@{{ 'attribution/projection/' + attr.selectedModelId }}" target="_self"> Projection</a></li>
                @endif

                @if (Sentinel::hasAccess('api.attribution.run'))
                    <li><a ng-hide="attr.disableProjection || attr.selectedModel[ 0 ].processing" ng-click="attr.runAttribution( true )">Run Attribution</a></li>
                @endif

                @if (Sentinel::hasAccess('attributionModel.edit'))
                    <li><a ng-hide="attr.selectedModel[ 0 ].processing" ng-href="@{{ 'attribution/edit/' + attr.selectedModelId }}" target="_self">Edit</a></li>
                @endif

                @if (Sentinel::hasAccess('api.attribution.model.setlive'))
                    <li><a ng-hide="attr.disableProjection || attr.selectedModel[ 0 ].processing" ng-click="attr.setModelLive()">Set Live</a></li>
                @endif
        </ul>
    </div>
</div>
<md-card>
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
                <td md-cell ng-bind="::app.formatDate( model.created_at )"></td>
                <td md-cell ng-bind="::app.formatDate( model.updated_at )"></td>
            </tr>
            </tbody>
        </table>
    </md-table-container>

    <md-table-pagination md-limit="" md-limit-options="[5,10,25,50]" md-page="" md-total="" md-on-paginate=""
                         md-page-select></md-table-pagination>
</md-card>
