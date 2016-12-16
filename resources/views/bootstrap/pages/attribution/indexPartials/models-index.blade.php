<div class="navbar navbar-topper navbar-primary" role="navigation">
    <div class="container-fluid">
        <a class="navbar-brand pull-right">
            <md-icon md-font-set="material-icons" class="mt2-icon-white material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="left" data-content="After editing a model, attribution does not run again. You will need to wait until the next automated run or manually click 'Run Attribution'.">help</md-icon>
        </a>
        <a class="navbar-brand pull-left md-table-header-override-whitetext">Models</a>

        <ul class="nav navbar-nav navbar-right" ng-show="attr.showModelActions">
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
                    ng-class="{ 'mt2-live-row' : model.live == 1 }"
                    ng-repeat="model in attr.models track by $index">
                <td md-cell class="mt2-table-btn-column" nowrap>
                    <a ng-hide="model.processing" ng-href="@{{ 'attribution/edit/' + model.id }}" aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon></a>

                    @if (Sentinel::hasAccess('attributionProjection.show'))
                    <a ng-hide="model.live === 1 || model.processing" ng-href="@{{ 'attribution/projection/' + model.id }}" aria-label="Projection" target="_self" data-toggle="tooltip" data-placement="bottom" title="Projection">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">assessment</md-icon></a>
                    @endif

                    @if (Sentinel::hasAccess('api.attribution.run'))
                    <a ng-hide="model.processing" ng-click="attr.runAttribution( model.live === 1 ? '' : model.id )" aria-label="Run Attribution" data-toggle="tooltip" data-placement="bottom" title="Run Attribution">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">monetization_on</md-icon>
                    </a>
                    @endif

                    @if (Sentinel::hasAccess('api.attribution.model.setlive'))
                    <a ng-hide="model.live === 1 || model.processing" ng-click="attr.setModelLive( model.id )" aria-label="Set Live" data-toggle="tooltip" data-placement="bottom" title="Set Live">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">play_circle_outline</md-icon>
                    </a>
                    @endif
                </td>
                <td md-cell ng-bind="model.name" nowrap></td>
                <td md-cell ng-bind="model.processing ? 'Running' : 'Completed'"></td>
                <td md-cell ng-bind="::app.formatDate( model.created_at )" nowrap></td>
                <td md-cell ng-bind="::app.formatDate( model.updated_at )" nowrap></td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <md-table-pagination md-limit="attr.paginationCount" md-limit-options="attr.paginationOptions" md-page="attr.currentPage" md-total="@{{attr.modelTotal}}" md-on-paginate="attr.loadModels" md-page-select></md-table-pagination>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
