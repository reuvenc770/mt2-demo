@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Reports' )

@section( 'angular-controller' , 'ng-controller="ReportController as report"' )

@section( 'page-menu' )
    @if (Sentinel::hasAccess('api.report.store'))
        <li><a ng-click="report.showReportModal()" href="#">Add Report</a></li>
    @endif
@stop

@section( 'content' )
<div ng-init="report.loadReports()">
    <md-table-container>
        <table md-table md-progress="report.queryPromise">
            <thead md-head>
                <tr md-row>
                    <th md-column></th>
                    <th md-column class="md-table-header-override-whitetext">Name</th>
                    <th md-column class="md-table-header-override-whitetext">Created</th>
                    <th md-column class="md-table-header-override-whitetext">Updated</th>
                </tr>
            </thead>
            <tbody md-body>
                <tr md-row ng-repeat="current in report.reportList">
                    <td md-cell class="mt2-table-cell-center">
                        <a ng-href="@{{ '/report/view/' + current.id }}" target="_self" aria-label="View" data-toggle="tooltip" data-placement="bottom" title="View">
                            <md-icon md-font-set="material-icons" class="mt2-icon-black">launch</md-icon>
                        </a>

                        <a href="#" ng-click="report.changeReportModal( current.id , current.name , current.amp_report_id )">
                            <md-icon md-font-set="material-icons" class="mt2-icon-black" aria-label="Edit">edit</md-icon>
                        </a>
                    </td>
                    <td md-cell ng-bind="current.name"></td>
                    <td md-cell ng-bind="current.created_at"></td>
                    <td md-cell ng-bind="current.updated_at"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <md-content class="md-mt2-zeta-theme md-hue-2">
                            <md-table-pagination md-limit="report.paginationCount" md-limit-options="[10, 25, 50, 100]" md-page="report.currentPage" md-total="@{{report.reportTotal}}" md-on-paginate="report.loadReports" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>

<amp-report-create report-name="report.newReportName" report-id="report.newReportId" form-type="report.formType" report-saving="report.reportSaving" report-error="report.formErrors" create-report="report.createReport()"></amp-report-create>
@stop

<?php Assets::add(
    [
        'resources/assets/js/bootstrap/report/ReportController.js' ,
        'resources/assets/js/bootstrap/report/ReportApiService.js' ,
        'resources/assets/js/bootstrap/report/ReportCreateModalDirective.js' ,
    ] , 
    'js' ,
    'pageLevel' 
) ?>
