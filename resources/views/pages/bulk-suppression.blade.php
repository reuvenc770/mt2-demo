@extends( 'layout.default' )

@section( 'title' , 'Bulk Suppression' )

@section( 'content' )

<div ng-controller="BulkSuppressionController as supp">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="60" flex="100">
            <div layout="column" layout-align="end end">
                <md-button class="md-raised md-accent"
                    ng-disabled="!supp.emailsLoaded"
                    ng-click="supp.uploadSuppressions()">
                        <md-icon md-svg-icon="img/icons/ic_block_white_18px.svg"></md-icon> Suppress
                </md-button>
            </div>

            <md-card>
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <span>Bulk Suppression Options</span>
                    </div>
                </md-toolbar>

                    <md-card-content>
                        <form name="suppressionForm" layout="column" novalidate>
                            <md-input-container class="md-block" ng-cloak>
                                <label>Emails</label>
                                <textarea ng-model="supp.emailString" rows="5" md-select-on-focus ng-change="supp.enableSubmission()"></textarea>
                            </md-input-container>

                            <md-input-container>
                                <label>Suppression Reason</label>
                                <md-select name="reason" ng-model="supp.selectedReason" ng-init="supp.loadReasons()" ng-required="true">
                                    <md-option ng-repeat="reason in supp.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</md-option>
                                </md-select>
                                <div ng-messages="suppressionForm.reason.$error">
                                    <div ng-message="required">Suppression Reason is required.</div>
                                </div>
                            </md-input-container>
                        </form>

                        <div flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'bulksuppression' , '_token' : '{{ csrf_token() }}' } }"
                             flow-files-submitted="$flow.upload()"
                             flow-file-success="supp.startTransfer($file)">
                            <div flow-drop class="dropFile" flow-drag-enter="style={border:'2px solid green'}" flow-drag-leave="style={}" ng-style="style" layout="row" layout-xs="column" layout-align-xs="center center" layout-align-gt-xs="start center">
                                <md-button class="md-raised" flow-btn>
                                    Upload Suppression Files
                                    <input type="file" style="visibility: hidden; position: absolute;" />
                                </md-button>
                                <span>
                                &nbsp;&nbsp;
                                <em>OR</em>
                                &nbsp;&nbsp;
                                </span>

                                <span><strong>Drag & Drop Suppression Files Here</strong></span>
                            </div>

                            <br />
                            <br />

                            <md-card>
                                <md-card-content layout="row" layout-align="center center">
                                    <md-button class="md-raised mt2-button-xs mt2-button-success" ng-click="$flow.resume()">Resume</md-button>
                                    <md-button class="md-raised mt2-button-xs md-warn" ng-click="$flow.pause()">Pause</md-button>
                                    <md-button class="md-raised mt2-button-xs md-warn md-hue-2" ng-click="$flow.cancel()">Cancel</md-button>
                                    <div flex="auto"></div>
                                    <div flex="initial">
                                        <div class="mt2-label mt2-label-info">Total File Size: @{{$flow.getSize() | bytes }}</div>
                                        <div class="mt2-label" ng-class="{ 'mt2-label-default' : !$flow.isUploading() , 'mt2-label-success' : $flow.isUploading() }">Is Uploading: @{{$flow.isUploading() ? 'Yes' : 'No' }}</div>
                                    </div>
                                </md-card-content>
                            </md-card>

                            <md-table-container>
                                <table md-table>
                                    <thead md-head>
                                        <tr md-row>
                                            <th md-column class="md-table-header-override-whitetext">#</th>
                                            <th md-column class="md-table-header-override-whitetext">Name</th>
                                            <th md-column class="md-table-header-override-whitetext" md-numeric>File Size</th>
                                            <th md-column class="md-table-header-override-whitetext">#Chunks</th>
                                            <th md-column class="md-table-header-override-whitetext">Progress</th>
                                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Download Status</th>
                                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Settings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr md-row ng-repeat="file in transfers">
                                            <td md-cell>@{{ $index + 1 }}</td>
                                            <td md-cell>@{{ file.name }}</td>
                                            <td md-cell>@{{ file.size | bytes }}</td>
                                            <td md-cell>@{{ file.chunks.length }}</td>
                                            <td md-cell>
                                                <md-progress-linear class="md-warn" md-mode="determinate" ng-value="file.progress() * 100"></md-progress-linear>
                                            </td>
                                            <td md-cell class="mt2-table-cell-center" ng-class="{ 'bg-info' : file.isUploading() , 'bg-warning' : file.paused , 'bg-danger' : file.error , 'bg-success' : !file.error }">
                                                @{{ file.isUploading() ? 'Downloading' : ( file.paused ? 'Paused': ( file.error ? 'Failed' : 'Successful' ) ) }}
                                            </td>
                                            <td md-cell>
                                                <div layout="row" layout-align="center center">
                                                    <md-button class="md-raised md-warn mt2-button-xs" ng-click="file.pause()" ng-hide="file.paused">Pause</md-button>
                                                    <md-button class="md-raised mt2-button-success mt2-button-xs" ng-click="file.resume()" ng-show="file.paused">Resume</md-button>
                                                    <md-button class="md-raised md-warn md-hue-2 mt2-button-xs" ng-click="file.cancel()">Cancel</md-button>
                                                    <md-button class="md-raised md-accent mt2-button-xs" ng-click="file.retry()" ng-show="file.error">Retry</md-button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </md-table-container>

                        </div>
                    </md-card-content>
            </md-card>

            <div layout="column" layout-align="end end">
                <md-button class="md-raised md-accent"
                    ng-disabled="!supp.emailsLoaded"
                    ng-click="supp.uploadSuppressions()">
                        <md-icon md-svg-icon="img/icons/ic_block_white_18px.svg"></md-icon> Suppress
                </md-button>
            </div>

        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/bulksuppression.js"></script>
@stop
