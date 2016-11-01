@extends('layout.default')

@section('title', 'Data Exports')

@section( 'angular-controller' , 'ng-controller="DataExportController as dataExport"')

@section( 'page-menu' )
    <div ng-hide="app.isMobile()">
      <md-button ng-click="dataExport.switchDisplayedStatus()">
          <span>@{{dataExport.displayedStatusButtonText}}</span>
      </md-button>
      @if (Sentinel::hasAccess('dataexport.add'))
        <md-button ng-click="dataExport.viewAdd()" aria-label="Add Data Export">
            <span>Add Data Export</span>
        </md-button>
      @endif
    </div>

    <md-menu ng-show="app.isMobile()" md-position-mode="target-right target">
      <md-button aria-label="Open menu" class="md-icon-button" ng-click="$mdOpenMenu($event)">
        <md-icon md-svg-src="img/icons/ic_more_horiz_black_24px.svg"></md-icon>
      </md-button>
      <md-menu-content width="3">
        <md-menu-item>
          <md-button ng-click="dataExport.switchDisplayedStatus()">
              <span>@{{dataExport.displayedStatusButtonText}}</span>
          </md-button>
        </md-menu-item>
        @if (Sentinel::hasAccess('dataexport.add'))
          <md-menu-item>
            <md-button ng-click="dataExport.viewAdd()" aria-label="Add Data Export">
                <span>Add Data Export</span>
            </md-button>
          </md-menu-item>
        @endif
      </md-menu-content>
    </md-menu>
@stop

@section('content')
<div ng-init="dataExport.loadActiveDataExports()">
  <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
    <md-card>
      <md-table-container>
        <table md-table class="mt2-table-large" md-row-select multiple="true" md-progress="dataExport.queryPromise" ng-model="dataExport.mdSelectedExports">
            <thead md-head>
                <tr md-row>
                <th md-column></th>
                <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                <th md-column class="md-table-header-override-whitetext mt2-cell-left-padding">Filename</th>
                <th md-column class="md-table-header-override-whitetext">Client</th>
                <th md-column class="md-table-header-override-whitetext">Profile</th>
                <th md-column class="md-table-header-override-whitetext">FTP username</th>
                <th md-column class="md-table-header-override-whitetext">Frequency</th>
                <th md-column class="md-table-header-override-whitetext">Last Pulled</th>
                <th md-column class="md-table-header-override-whitetext" md-numeric>Records</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row
                    md-auto-select="false"
                    md-select="record"
                    md-select-id="exportID"
                    md-on-select="dataExport.mdToggleInclusion"
                    md-on-deselect="dataExport.mdToggleInclusion"
                    ng-repeat="record in dataExport.dataExports track by $index">
                    <td md-cell>
                      <div layout="row" layout-align="center center">
                        <md-button class="md-icon-button" ng-href="@{{'/dataexport/edit/' + record.exportID}}" target="_self" aria-label="Edit">
                            <md-icon md-svg-icon="img/icons/ic_mode_edit_black_18px.svg"></md-icon>
                            <md-tooltip md-direction="bottom">Edit</md-tooltip>
                        </md-button>

                        <md-button class="md-icon-button" ng-click="dataExport.changeDataExportStatus(record.exportID)" aria-label="Pause">
                            <md-icon md-svg-icon="img/icons/ic_pause_black_18px.svg"></md-icon>
                            <md-tooltip md-direction="bottom">Pause</md-tooltip>
                        </md-button>

                        <md-button class="md-icon-button" ng-click="dataExport.copyDataExport(record.exportID)" aria-label="Copy">
                            <md-icon md-svg-icon="img/icons/ic_content_copy_black_18px.svg"></md-icon>
                            <md-tooltip md-direction="bottom">Copy</md-tooltip>
                        </md-button>

                        <md-button class="md-icon-button" ng-click="dataExport.deleteDataExport(record.exportID)" aria-label="Delete">
                            <md-icon md-svg-icon="img/icons/ic_clear_black_24px.svg"></md-icon>
                            <md-tooltip md-direction="bottom">Delete</md-tooltip>
                        </md-button>
                      </div>
                    </td>
                    <td md-cell class="mt2-table-cell-center" ng-class="{ 'mt2-bg-success' : record.status == 'Active' , 'mt2-bg-warn' : record.status == 'Paused' , 'mt2-bg-danger' : record.status == 'Deleted' }">@{{ record.status == 'Active' ? 'Active' : record.status == 'Paused' ? 'Paused' : 'Deleted'  }}</td>
                    <td md-cell class="mt2-cell-left-padding">@{{ record.fileName }}</td>
                    <td md-cell>@{{ record.group_name }}</td>
                    <td md-cell>@{{ record.profile_name }}</td>
                    <td md-cell>@{{ record.ftpUser }}</td>
                    <td md-cell>@{{ record.frequency }}</td>
                    <td md-cell nowrap>@{{ record.lastUpdated }}</td>
                    <td md-cell>@{{ record.recordCount }}</td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

<md-content class="md-mt2-zeta-theme md-hue-2">
    <md-table-pagination md-limit="dataExport.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="dataExport.currentPage" md-total="@{{dataExport.dataExportTotal}}" md-on-paginate="dataExport.mdLoadActiveDataExports" md-page-select></md-table-pagination>
</md-content>
    </md-card>
  </md-content>
  <div layout="row" layout-align="end center">
    <md-button class="md-raised md-warn" ng-click="dataExport.pauseSelected()">
      <md-icon md-svg-icon="img/icons/ic_pause_white_18px.svg"></md-icon> @{{dataExport.massActionButtonText}} Exports
    </md-button>
    <md-button class="md-raised md-accent" ng-click="dataExport.rePullSelected()">
      <md-icon md-svg-icon="img/icons/ic_refresh_white_18px.svg"></md-icon> Re-pull Exports
    </md-button>
  </div>
</div>
@stop

@section('pageIncludes')
<script src="js/dataexport.js"></script>
@stop