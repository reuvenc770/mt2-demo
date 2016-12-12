<div class="navbar navbar-topper navbar-primary" role="navigation">
    <div class="container-fluid">

        <ul class="nav navbar-nav navbar-right" ng-show="attr.showModelActions">
                @if (Sentinel::hasAccess('attributionProjection.show'))
                    <li><a ng-hide="attr.disableProjection || attr.selectedModel[ 0 ].processing" ng-href="@{{ 'attribution/projection/' + attr.selectedModelId }}" target="_self">
                        <md-icon md-font-set="material-icons" class="mt2-icon-white">assessment</md-icon> Projection
                    </a></li>
                @endif

                @if (Sentinel::hasAccess('api.attribution.run'))
                    <li><a ng-hide="attr.disableProjection || attr.selectedModel[ 0 ].processing" ng-click="attr.runAttribution( true )" href="#">
                        <md-icon md-font-set="material-icons" class="mt2-icon-white">monetization_on</md-icon> Run Attribution
                    </a></li>
                @endif

                @if (Sentinel::hasAccess('api.attribution.model.setlive'))
                    <li><a ng-hide="attr.disableProjection || attr.selectedModel[ 0 ].processing" ng-click="attr.setModelLive()" href="#">
                        <md-icon md-font-set="material-icons" class="mt2-icon-white">play_circle_outline</md-icon> Set Live
                    </a></li>
                @endif

                      
                    <li><h2 class="md-toolbar-tools" ng-show="attr.selectedModel[ 0 ].processing"><a>This model is running. Please check back later for projections.</a></h2></li>
        </ul>
    </div>
</div>
    <md-table-container>
        <table md-table md-progress="attr.modelQueryPromise" md-row-select ng-model="attr.selectedModel">
            <thead md-head md-order="" md-on-reorder="" class="mt2-theme-thead">
            <tr md-row>
                <th md-column class="mt2-table-btn-column"></th>
                <th md-column md-order-by="" class="md-table-header-override-whitetext">Model Name</th>
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
                <td md-cell class="mt2-table-btn-column" nowrap>
                    <a ng-hide="model.processing" ng-href="@{{ 'attribution/edit/' + model.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon></a>
                </td>
                <td md-cell ng-bind="model.name"></td>
                <td md-cell ng-bind="model.processing ? 'Running' : 'Completed'"></td>
                <td md-cell ng-bind="::app.formatDate( model.created_at )" nowrap></td>
                <td md-cell ng-bind="::app.formatDate( model.updated_at )" nowrap></td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <md-table-pagination md-limit="attr.paginationCount" md-limit-options="attr.paginationOptions" md-page="attr.currentPage" md-total="@{{attr.modelTotal}}" md-on-paginate="attr.loadModels" md-page-select></md-table-pagination>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
