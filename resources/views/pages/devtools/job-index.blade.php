@extends( 'layout.default' )

@section( 'title' , 'Background Jobs' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <div class="row">
        <div class="page-header col-xs-12"><h1 class="text-center">Last 50 Job Runs</h1></div>
    </div>

    <div ng-controller="jobController as job" ng-init="job.loadJobs()">
        <div class="row">
            <div class="col-xs-12">
                <md-content class="md-mt2-zeta-theme">
                    <md-table-container>
                        <table md-table>
                            <thead md-head>
                            <tr md-row>
                                <th md-column class="md-table-header-override-whitetext" md-numeric>Job</th>
                                <th md-column class="md-table-header-override-whitetext">Account</th>
                                <th md-column class="md-table-header-override-whitetext">Account Name</th>
                                <th md-column class="md-table-header-override-whitetext">Time Started</th>
                                <th md-column class="md-table-header-override-whitetext">Time Completed</th>
                                <th md-column class="md-table-header-override-whitetext">Attempts</th>
                                <th md-column class="md-table-header-override-whitetext">Status</th>
                            </tr>
                            </thead>
                            <tbody md-body>
                            <tr md-row ng-repeat="record in job.entries" ng-class="[ job.rowStatusMap[ record.status ] ]">
                                <td md-cell>@{{ record.job_name }}</td>
                                <td md-cell>@{{ record.account_name }}</td>
                                <td md-cell>@{{ record.account_number }}</td>
                                <td md-cell>@{{ record.time_started }}</td>
                                <td md-cell>@{{ record.time_finished }}</td>
                                <td md-cell>@{{ record.attempts }}</td>
                                <td md-cell><span class="glyphicon" ng-class="[job.GlythMap[record.status]]"></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </md-table-container>
                </md-content>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/job.js"></script>
@stop
