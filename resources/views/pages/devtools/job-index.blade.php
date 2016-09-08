@extends( 'layout.default' )

@section( 'title' , 'Background Jobs' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="column" class="md-mt2-zeta-theme md-hue-1">
        <div flex="grow" layout-padding>
            <div ng-controller="jobController as job" ng-init="job.loadJobs()">
                <div id="mtTableContainer" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover text-center table-condensed">
                        <thead>
                        <tr>
                            <th class="text-center" ng-repeat="header in job.headers">@{{ header }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr ng-repeat="record in job.entries" ng-class="[ job.rowStatusMap[ record.status ] ]">
                            <td>@{{ record.job_name }}</td>
                            <td>@{{ record.account_name }}</td>
                            <td>@{{ record.account_number }}</td>
                            <td>@{{ record.time_started }}</td>
                            <td>@{{ record.time_finished }}</td>
                            <td>@{{ record.attempts }}</td>
                            <td><span class="glyphicon" ng-class="[job.GlythMap[record.status]]"></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
    <script src="js/job.js"></script>
@stop
