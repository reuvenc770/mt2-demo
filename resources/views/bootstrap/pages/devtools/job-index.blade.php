@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Background Jobs' )

@section( 'content' )
    <div ng-controller="jobController as job" ng-init="job.loadJobs()">
        <md-table-container>
            <table md-table>
                <thead md-head>
                <tr md-row>
                    <th md-column class="md-table-header-override-whitetext">Job</th>
                    <th md-column class="md-table-header-override-whitetext">Account</th>
                    <th md-column class="md-table-header-override-whitetext">Account Name</th>
                    <th md-column class="md-table-header-override-whitetext">Time Started</th>
                    <th md-column class="md-table-header-override-whitetext">Time Completed</th>
                    <th md-column class="md-table-header-override-whitetext">Attempts</th>
                    <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Status</th>
                </tr>
                </thead>
                <tbody md-body>
                <tr md-row ng-repeat="record in job.entries">
                    <td md-cell ng-bind="record.job_name"></td>
                    <td md-cell ng-bind="record.account_name"></td>
                    <td md-cell ng-bind="record.account_number"></td>
                    <td md-cell ng-bind="record.time_started" nowrap></td>
                    <td md-cell ng-bind="record.time_finished" nowrap></td>
                    <td md-cell ng-bind="record.attempts"></td>
                    <td md-cell class="mt2-table-cell-center" ng-class="[ job.rowStatusMap[ record.status ] ]" ng-bind="{{ json_encode($statusNames) }}[ record.status ]"></td>
                </tr>
                </tbody>
            </table>
        </md-table-container>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/job/jobController.js',
                'resources/assets/js/bootstrap/job/JobApiService.js'],'js','pageLevel') ?>