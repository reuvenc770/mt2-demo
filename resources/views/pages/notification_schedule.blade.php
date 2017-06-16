@extends( 'layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Scheduled Notifications' )

@section( 'angular-controller' , 'ng-controller="ScheduledNotificationController as notify"' )

@section( 'cacheTag' , 'NotificationSchedule' )

@section( 'page-menu' )
    <li ng-click="notify.showAddDialog()" role="button"><a href="#">Add Notification</a></li>
@stop

@section( 'content' )
<div ng-init="notify.loadUnscheduledKeys()">
<div class="navbar navbar-topper navbar-primary" role="navigation">
    <div class="container-fluid">
        <a class="navbar-brand pull-left md-table-header-override-whitetext">Unscheduled Logs</a>
    </div>
</div>
<md-table-container>
    <table md-table class="mt2-table-large" md-progress="notify.logPromise">
        <thead md-head class="mt2-theme-thead">
            <tr md-row>
                <th md-column class="mt2-table-btn-column"></th>
                <th md-column class="md-table-header-override-whitetext">Log Name</th> <!-- Assign sorting field -->
                <th md-column class="md-table-header-override-whitetext">Example Log</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in notify.unscheduled track by $index"> <!-- repeat schedules -->
                <td md-cell class="mt2-table-btn-column" nowrap>
                    <a aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Create Notification">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">alarm_add</md-icon>
                    </a>
                </td>
                <td md-cell ng-bind="record.content_key"></td>
                <td md-cell ng-bind="record.content | prettyJSON"></td>
            </tr>
        </tbody>
    </table>
</md-table-container>
</div>

<br /><br />

<div ng-init="notify.loadSchedules()">
<div class="navbar navbar-topper navbar-primary" role="navigation">
    <div class="container-fluid">
        <a class="navbar-brand pull-left md-table-header-override-whitetext">Notifications</a>
    </div>
</div>
<md-table-container>
    <table md-table class="mt2-table-large" md-progress="notify.schedulePromise">
        <thead md-head class="mt2-theme-thead"> <!-- Add sorting when ready -->
            <tr md-row>
                <th md-column class="mt2-table-btn-column"></th>
                <th md-column class="md-table-header-override-whitetext">Title</th> <!-- Assign sorting field -->
                <th md-column class="md-table-header-override-whitetext">Log Name</th>
                <th md-column class="md-table-header-override-whitetext">Level</th>
                <th md-column class="md-table-header-override-whitetext">Status</th>
                <th md-column class="md-table-header-override-whitetext">Schedule</th>
                <th md-column class="md-table-header-override-whitetext">Lookback (hours)</th>
                <th md-column class="md-table-header-override-whitetext">Email Enabled</th>
                <th md-column class="md-table-header-override-whitetext">Email Recipients</th>
                <th md-column class="md-table-header-override-whitetext">Email Template</th>
                <th md-column class="md-table-header-override-whitetext">Slack Enabled</th>
                <th md-column class="md-table-header-override-whitetext">Slack Recipients</th>
                <th md-column class="md-table-header-override-whitetext">Slack Template</th>
                <th md-column class="md-table-header-override-whitetext">Created</th>
                <th md-column class="md-table-header-override-whitetext">Updated</th>
            </tr>
        </thead>

        <tbody md-body>
            <tr md-row ng-repeat="record in notify.schedules track by $index"> <!-- repeat schedules -->
                <td md-cell class="mt2-table-btn-column" nowrap>
                    <a aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit Notification">
                        <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                    </a>
                </td>
                <td md-cell ng-bind="record.title"></td>
                <td md-cell ng-bind="record.content_key"></td>
                <td md-cell ng-bind="record.level | capitalizeFirstChar"></td>
                <td md-cell ng-bind="record.status ? 'Active' : 'Inactive'"></td>
                <td md-cell>@{{ notify.describeCron( record.cron_expression ) }}</td>
                <td md-cell ng-bind="record.content_lookback"></td>
                <td md-cell ng-bind="record.use_email ? 'Enabled' : 'Disabled'"></td>
                <td md-cell ng-bind="record.email_recipients"></td>
                <td md-cell ng-bind="record.email_template_path"></td>
                <td md-cell ng-bind="record.use_slack ? 'Enabled' : 'Disabled'"></td>
                <td md-cell ng-bind="record.slack_recipients"></td>
                <td md-cell ng-bind="record.slack_template_path"></td>
                <td md-cell ng-bind="app.formatDate( record.created_at , 'MMMM d, YYYY h:mm a' )"></td>
                <td md-cell ng-bind="app.formatDate( record.updated_at , 'MMMM d, YYYY h:mm a' )"></td>
            </tr>
        </tbody>
    </table>
</md-table-container>
</div>

<div style="visibility: hidden;">
    <div class="md-dialog-container" id="addScheduleModal">
        <md-dialog>
            <md-toolbar class="mt2-theme-toolbar">
                <div class="mt2-toolbar-tools mt2-theme-toolbar-tools">
                    <h3>Add New Schedule Notification</h3>
                    <span flex></span>
                    <md-button class="md-icon-button" ng-click=""><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                </div>
            </md-toolbar>

            <md-dialog-content>
                <div class="md-dialog-content">
                    <div class="form-horizontal">

                        <div class="panel panel-info">
                            <div class="panel-heading">General</div>
                            <div class="panel-body">
                                
                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">Scheduling</div>
                            <div class="panel-body">

                            </div>
                        </div>

                        <div class="panel panel-info">
                            <div class="panel-heading">Notification Configuration</div>
                            <div class="panel-body">

                            </div>
                        </div>

                    </div>
                </div>
            </md-dialog-content>
        </md-dialog>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/pages/ScheduledNotificationController.js',
        'resources/assets/js/pages/ScheduledNotificationApiService.js' ,
        ],'js','pageLevel') ?>
