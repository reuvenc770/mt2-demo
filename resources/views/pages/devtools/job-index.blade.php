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
                <div id="mtTableContainer" class="table-responsive">
                    <table class="table table-striped table-bordered table-hover text-center table-condensed">
                        <thead>
                        <tr>
                            <th class="text-center" ng-repeat="header in job.headers">@{{ header }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr ng-repeat="record in job.entries" class="@{{ job.classes[record.status] }}">
                            <td>@{{ record.job_name }}</td>
                            <td>@{{ record.account_name }}</td>
                            <td>@{{ record.account_number }}</td>
                            <td>@{{ record.time_started }}</td>
                            <td>@{{ record.time_finished }}</td>
                            <td>@{{ record.attempts }}</td>
                            <td><span class="glyphicon glyphicon-@{{ job.glyths[record.status] }}"></span></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop

@section( 'pageIncludes' )
    <script src="js/job.js"></script>
@stop
