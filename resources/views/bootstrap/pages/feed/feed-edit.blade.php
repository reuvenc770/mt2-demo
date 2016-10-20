
@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Edit Feed' )

@section( 'navFeedClasses' , 'active' )

@section( 'angular-controller' , 'ng-controller="FeedController as feed"' )

@section( 'content' )

<div ng-init="feed.loadFeed()">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-md="50" flex="100">
            <div layout="row" layout-align="end end">

                <md-button class="md-raised md-accent" ng-click="feed.resetPassword()">
                    <md-icon class="material-icons" md-font-set="material-icons">settings</md-icon>Reset FTP Password
                </md-button>
                <md-button layout="row" class="md-raised md-accent" ng-disabled="feed.generatingLinks" ng-click="feed.generateLinks()">
                    <md-icon class="material-icons" md-font-set="material-icons" ng-hide="feed.generatingLinks">link</md-icon>
                    <md-progress-circular ng-show="feed.generatingLinks" md-mode="indeterminate" md-diameter="16"></md-progress-circular> <span flex> Generate Links</span>
                </md-button>
                <md-button layout="row" class="md-raised md-accent" ng-disabled="feed.updatingFeed" ng-click="feed.updateFeed( $event , feedForm )">
                    <md-icon class="material-icons" md-font-set="material-icons" ng-hide="feed.updatingFeed">save</md-icon>
                    <md-progress-circular ng-show="feed.updatingFeed" md-mode="indeterminate" md-diameter="16"></md-progress-circular> <span flex>Update</span>
                </md-button>
            </div>

            @include( 'pages.feed.feed-form' )
            <div layout="row" layout-align="end end">
                <md-button layout="row" class="md-raised md-accent" ng-disabled="feed.generatingLinks" ng-click="feed.generateLinks()">
                    <md-icon class="material-icons" md-font-set="material-icons" ng-hide="feed.generatingLinks">settings</md-icon>
                    <md-progress-circular ng-show="feed.generatingLinks" md-mode="indeterminate" md-diameter="16">link</md-progress-circular> <span flex> Generate Links</span>
                </md-button>
                <md-button layout="row" class="md-raised md-accent" ng-disabled="feed.updatingFeed" ng-click="feed.updateFeed( $event , feedForm )">
                    <md-icon class="material-icons" md-font-set="material-icons" ng-hide="feed.updatingFeed">save</md-icon>
                    <md-progress-circular ng-show="feed.updatingFeed" md-mode="indeterminate" md-diameter="16"></md-progress-circular> <span flex> Update</span>
                </md-button>
            </div>
        </div>
    </md-content>

    <div style="visibility: hidden;">
        <div class="md-dialog-container" id="urlModal" ng-cloak>
            <md-dialog layout="column">
                <md-toolbar>
                    <div class="md-toolbar-tools" layout="row">
                        <h4 id="urlModalLabel">Feed URLs</h4>
                        <span flex></span>
                        <md-button class="md-icon-button" ng-click="feed.closeUrlModal()"><md-icon md-svg-src="img/icons/ic_clear_white_24px.svg"></md-icon></md-button>
                    </div>
                </md-toolbar>
                <md-dialog-content>
                    <div class="md-dialog-content" id="urlModalBody">
                        <md-table-container>
                            <table md-table id="urlTable">
                                <thead md-head>
                                    <tr md-row>
                                        <th md-column class="md-table-header-override-whitetext">Feed ID</th>
                                        <th md-column class="md-table-header-override-whitetext">URL</th>
                                    </tr>
                                </thead>

                                <tbody md-body>
                                    <tr md-row ng-repeat="record in feed.urlList track by $index">
                                        <td md-cell ng-bind="::record.offerId"></td>
                                        <td md-cell ng-bind="::record.url"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </md-table-container>
                    </div>
                </md-dialog-content>
                <md-dialog-actions layout="row" layout-align="end end">
                    <md-button class="md-raised" ngclipboard data-clipboard-target="#urlTable">Copy URLs</md-button>
                </md-dialog-actions>
            </md-dialog>
        </div>
    </div>

</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/feed/FeedController.js',
        'resources/assets/js/bootstrap/feed/FeedApiService.js',
        'resources/assets/js/bootstrap/feed/FeedUrlModalDirective.js'],'js','pageLevel') ?>

