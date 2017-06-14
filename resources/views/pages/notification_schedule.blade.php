@extends( 'layout.default' )

@section( 'container' , 'container-fluid' )

@section( 'title' , 'Scheduled Notifications' )

@section( 'angular-controller' , 'ng-controller="ScheduledNotificationController as notify"' )

@section( 'content' )
<md-table-container>
    <table md-table class="mt2-table-large"> <!-- Add promise when ready -->
        <thead md-head class="mt2-theme-thead"> <!-- Add sorting when ready -->
            <tr md-row>
                <th md-column class="md-table-header-override-whitetext">Title</th> <!-- Assign sorting field -->
                <th md-column class="md-table-header-override-whitetext">Content Key</th>
                <th md-column class="md-table-header-override-whitetext">Level</th>
                <th md-column class="md-table-header-override-whitetext">Status</th>
                <th md-column class="md-table-header-override-whitetext">Frequency</th>
                <th md-column class="md-table-header-override-whitetext">Content Lookback (hours)</th>
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
            <tr md-row> <!-- repeat schedules -->

            </tr>
        </tbody>
    </table>
</md-table-container>
@stop

<?php Assets::add(
        ['resources/assets/js/pages/ScheduledNotificationController.js',
        'resources/assets/js/pages/ScheduledNotificationApiService.js'],'js','pageLevel') ?>
