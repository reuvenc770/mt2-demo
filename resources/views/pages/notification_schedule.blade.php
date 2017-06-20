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
                    <a aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Create Notification" ng-click="notify.showAddDialogForUnscheduled( record.content_key )">
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
            <thead md-head class="mt2-theme-thead" md-order="notify.sort" md-on-reorder="notify.loadSchedules"> <!-- Add sorting when ready -->
                <tr md-row>
                    <th md-column class="mt2-table-btn-column"></th>
                    <th md-column md-order-by="id" class="md-table-header-override-whitetext">ID</th>
                    <th md-column md-order-by="status" class="md-table-header-override-whitetext">Status</th>
                    <th md-column md-order-by="title" class="md-table-header-override-whitetext">Title</th> <!-- Assign sorting field -->
                    <th md-column md-order-by="content_key" class="md-table-header-override-whitetext">Log Name</th>
                    <th md-column md-order-by="level" class="md-table-header-override-whitetext">Level</th>
                    <th md-column class="md-table-header-override-whitetext">Schedule</th>
                    <th md-column md-order-by="content_lookback" class="md-table-header-override-whitetext">Lookback (hours)</th>
                    <th md-column md-order-by="use_email" class="md-table-header-override-whitetext">Email Enabled</th>
                    <th md-column class="md-table-header-override-whitetext">Email Recipients</th>
                    <th md-column md-order-by="email_template_path" class="md-table-header-override-whitetext">Email Template</th>
                    <th md-column md-order-by="use_slack" class="md-table-header-override-whitetext">Slack Enabled</th>
                    <th md-column class="md-table-header-override-whitetext">Slack Recipients</th>
                    <th md-column md-order-by="slack_template_path" class="md-table-header-override-whitetext">Slack Template</th>
                    <th md-column md-order-by="created_at" class="md-table-header-override-whitetext">Created</th>
                    <th md-column md-order-by="updated_at" class="md-table-header-override-whitetext">Updated</th>
                </tr>
            </thead>

            <tbody md-body>
                <tr md-row ng-repeat="record in notify.schedules track by $index"> <!-- repeat schedules -->
                    <td md-cell class="mt2-table-btn-column mt2-cell-left-padding" nowrap>
                        <a aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Edit Notification" ng-click="notify.showEditDialog( $index )">
                            <md-icon md-font-set="material-icons" class="mt2-icon-black">edit</md-icon>
                        </a>
                        <a aria-label="Edit" target="_self" data-toggle="tooltip" data-placement="bottom" title="Toggle Status" ng-click="notify.toggleStatus( record.id , record.status )">
                            <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="!record.status">play_circle_filled</md-icon>
                            <md-icon md-font-set="material-icons" class="mt2-icon-black" ng-show="record.status">pause_circle_filled</md-icon>
                        </a>
                    </td>
                    <td md-cell ng-bind="record.id" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.status ? 'Active' : 'Inactive'" ng-class="{ 'bg-danger' : !record.status , 'bg-success' : record.status }" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.title" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.content_key"></td>
                    <td md-cell ng-bind="record.level | capitalizeFirstChar"></td>
                    <td md-cell>@{{ notify.describeCron( record.cron_expression ) }}</td>
                    <td md-cell ng-bind="record.content_lookback"></td>
                    <td md-cell ng-bind="record.use_email ? 'Enabled' : 'Disabled'" ng-class="{ 'bg-danger' : !record.use_email , 'bg-success' : record.use_email }" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.email_recipients" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.email_template_path"></td>
                    <td md-cell ng-bind="record.use_slack ? 'Enabled' : 'Disabled'" ng-class="{ 'bg-danger' : !record.use_slack , 'bg-success' : record.use_slack }" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.slack_recipients" class="mt2-cell-left-padding"></td>
                    <td md-cell ng-bind="record.slack_template_path"></td>
                    <td md-cell ng-bind="app.formatDate( record.created_at , 'MMMM d, YYYY h:mm a' )"></td>
                    <td md-cell ng-bind="app.formatDate( record.updated_at , 'MMMM d, YYYY h:mm a' )"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="15">
                        <md-content class="md-mt2-zeta-theme">
                            <md-table-pagination md-limit="notify.paginationCount" md-limit-options="notify.paginationOptions" md-page="notify.currentPage" md-total="@{{notify.scheduleTotal}}" md-on-paginate="notify.loadSchedules" md-page-select></md-table-pagination>
                        </md-content>
                    </td>
                </tr>
            </tfoot>
        </table>
    </md-table-container>
</div>

<div style="visibility: hidden;">
    <div class="md-dialog-container" id="addScheduleModal" flex="100" layout-align="center center">
        <md-dialog flex=50>
            <md-toolbar class="mt2-theme-toolbar">
                <div class="mt2-toolbar-tools mt2-theme-toolbar-tools">
                    <h3 class="pull-left" ng-bind="notify.currentDialogTitle"></h3>
                    <span flex></span>
                    <md-button class="md-icon-button pull-right" ng-click="notify.closeDialog()"><md-icon md-font-set="material-icons" class="mt2-icon-white" title="Close" aria-label="Close">clear</md-icon></md-button>
                </div>
            </md-toolbar>

            <md-dialog-content>
                <div class="md-dialog-content">
                    <div class="panel panel-info">
                        <div class="panel-heading">General</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="scheduleTitle">Title</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="scheduleTitle" class="form-control" ng-model="notify.currentSchedule.title" placeholder="Title" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="scheduleLevel">Level</label>
                                        <div class="col-sm-9">
                                            <select id="scheduleLevel" class="form-control" ng-model="notify.currentSchedule.level" required> <!-- Implement NG-OPTIONS -->
                                                <option ng-repeat="level in notify.levelOptions" ng-value="level.value" ng-selected="level.value == notify.currentSchedule.level">@{{ level.label }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="scheduleLogName">Log Name</label>
                                        <div class="col-sm-9">
                                            <select id="scheduleLogName" class="form-control" ng-init="notify.loadContentKeys()" ng-model="notify.currentSchedule.content_key"> <!-- Implement NG-OPTIONS -->
                                                <option ng-repeat="key in notify.contentKeys" ng-value="key">@{{ key }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">Scheduling</div>
                        <div class="panel-body">
                            <blockquote>
                                <p ng-bind="notify.currentSchedule.cron_expression ? notify.describeCron( notify.currentSchedule.cron_expression ) : 'No Schedule Defined'"></p>
                            </blockquote>
                            <cron-selection ng-model="notify.currentSchedule.cron_expression" config="notify.cronSelectorOptions"></cron-selection>
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">Notification Configuration</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="scheduleLogLookback">Lookback (in hours)</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="scheduleLogLookback" class="form-control" ng-model="notify.currentSchedule.content_lookback" placeholder="Lookback" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            <md-checkbox ng-model="notify.currentSchedule.use_email" aria-label="Use Email" ng-true-value="1" ng-false-value="0">Use Email</md-checkbox>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-show="notify.currentSchedule.use_email">
                                        <label class="control-label col-sm-2" for="scheduleEmailTemplate">Email Template</label>
                                        <div class="col-sm-9">
                                            <select id="scheduleEmailTemplate" class="form-control" ng-init="notify.loadEmailTemplates()" ng-model="notify.currentSchedule.email_template_path"> <!-- Implement NG-OPTIONS -->
                                                <option ng-repeat="template in notify.emailTemplates" ng-value="template">@{{ template }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-show="notify.currentSchedule.use_email">
                                        <label class="control-label col-sm-2" for="scheduleEmailRecipients">Email Recipients</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="scheduleEmailRecipients" class="form-control" ng-model="notify.currentSchedule.email_recipients" placeholder="Email Recipients" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            <md-checkbox ng-model="notify.currentSchedule.use_slack" aria-label="Use Slack" ng-true-value="1" ng-false-value="0">Use Slack</md-checkbox>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-show="notify.currentSchedule.use_slack">
                                        <label class="control-label col-sm-2" for="scheduleSlackTemplate">Slack Template</label>
                                        <div class="col-sm-9">
                                            <select id="scheduleEmailTemplate" class="form-control" ng-init="notify.loadSlackTemplates()" ng-model="notify.currentSchedule.slack_template_path"> <!-- Implement NG-OPTIONS -->
                                                <option ng-repeat="template in notify.slackTemplates" ng-value="template">@{{ template }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-show="notify.currentSchedule.use_slack">
                                        <label class="control-label col-sm-2" for="scheduleSlackRecipients">Slack Recipients</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="scheduleSlackRecipients" class="form-control" ng-model="notify.currentSchedule.slack_recipients" placeholder="Slack Recipients" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </md-dialog-content>
            <md-dialog-actions layout="row" layout-align="center center" class="mt2-theme-dialog-footer layout-align-center-center layout-row">
                <div class="col-md-4">
                    <input class="btn mt2-theme-btn-primary btn-block" ng-click="notify.saveSchedule()" ng-disabled="notify.formSubmitted" type="submit" ng-value="notify.currentDialogButton">
                </div>
            </md-dialog-actions>
        </md-dialog>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/pages/ScheduledNotificationController.js',
        'resources/assets/js/pages/ScheduledNotificationApiService.js' ,
        ],'js','pageLevel') ?>
