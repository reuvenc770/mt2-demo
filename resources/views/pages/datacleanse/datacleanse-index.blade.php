@extends( 'layout.default' )

@section( 'title' , 'Data Cleanse' )

@section( 'angular-controller' , 'ng-controller="DataCleanseController as cleanse"')

@section( 'page-menu' )
    @if ( Sentinel::hasAccess( 'datacleanse.add' ) )
        <md-button ng-click="cleanse.viewAdd()" aria-label="Add Data Cleanse">
            <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="app.isMobile()">add_circle_outline</md-icon>
            <span ng-hide="app.isMobile()">Add Data Cleanse</span>
        </md-button>
    @endif
@stop

@section( 'content' )
<div ng-init="cleanse.load()">
    <md-content layout="row" layout-align="center" class="md-mt2-zeta-theme md-hue-1">
        <md-card flex-gt-md="70" flex="100">
            <md-table-container>
                <table md-table md-progress="cleanse.queryPromise">
                    <thead md-head >
                        <tr md-row>
                            <th md-column class="md-table-header-override-whitetext">File Name</th>
                            <th md-column class="md-table-header-override-whitetext">Updated</th>
                            <th md-column class="md-table-header-override-whitetext">Records</th>
                        </tr>
                    </thead>

                    <tbody md-body>
                        <tr md-row ng-repeat="record in cleanse.cleanses track by $index">
                            <td md-cell>@{{ record.name }}</td>
                            <td md-cell>@{{ record.lastUpdated }}</td>
                            <td md-cell>@{{ record.count }}</td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

            <md-content class="md-mt2-zeta-theme md-hue-2">
                <md-table-pagination md-limit="cleanse.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="cleanse.currentPage" md-total="@{{cleanse.cleanseTotal}}" md-on-paginate="cleanse.load" md-page-select></md-table-pagination>
            </md-content>
        </md-card>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/datacleanse.js"></script>
@stop
