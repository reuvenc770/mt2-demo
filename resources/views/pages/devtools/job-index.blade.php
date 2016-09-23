@extends( 'layout.default' )

@section( 'title' , 'Background Jobs' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <div flex="grow" layout-padding>
            <div ng-controller="jobController as job" ng-init="job.loadJobs()">
                <md-content class="md-mt2-zeta-theme">
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
                                <td md-cell>@{{ record.job_name }}</td>
                                <td md-cell>@{{ record.account_name }}</td>
                                <td md-cell>@{{ record.account_number }}</td>
                                <td md-cell>@{{ record.time_started }}</td>
                                <td md-cell>@{{ record.time_finished }}</td>
                                <td md-cell>@{{ record.attempts }}</td>
                                <td md-cell class="mt2-table-cell-center" ng-class="[ job.rowStatusMap[ record.status ] ]" ng-bind="{{ json_encode($statusNames) }}[ record.status ]"></td>
                            </tr>
                            </tbody>
                        </table>
                    </md-table-container>
                </md-content>
            </div>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
    <script src="js/job.js"></script>
@stop
