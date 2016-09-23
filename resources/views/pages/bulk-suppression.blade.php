@extends( 'layout.default' )

@section( 'title' , 'Bulk Suppression' )

@section( 'content' )

<div ng-controller="BulkSuppressionController as supp">
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">

        <div flex-gt-md="80" flex="100">
            <button type="button" class="btn btn-success btn-md pull-right"
                ng-disabled="!supp.emailsLoaded"
                ng-click="supp.uploadSuppressions()">
                    <span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : supp.emailsLoaded }"></span>
                    Suppress
            </button>

            <div class="clearfix"></div>

            <md-card>
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Bulk Suppression Options</span>
                    </div>
                </md-toolbar>

                    <md-card-content flex>
                        <md-input-container class="md-block" ng-cloak>
                            <label>Emails</label>
                            <textarea ng-model="supp.emailString" rows="5" md-select-on-focus ng-change="supp.enableSubmission()"></textarea>
                        </md-input-container>


                        <select name="reason" class="form-control" ng-model="supp.selectedReason" ng-init="supp.loadReasons()" required>
                            <option value="">Please Choose a Suppression Reason</option>
                            <option ng-repeat="reason in supp.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
                        </select>

                        <div flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'bulksuppression' , '_token' : '{{ csrf_token() }}' } }"
                             flow-files-submitted="$flow.upload()"
                             flow-file-success="supp.startTransfer($file)">
                            <div flow-drop class="dropFile" flow-drag-enter="style={border:'4px solid green'}" flow-drag-leave="style={}" ng-style="style">
                                <span class="btn btn-default" flow-btn>
                                    Upload Suppression Files
                                    <input type="file" style="visibility: hidden; position: absolute;" />
                                </span>

                                &nbsp;&nbsp;
                                <em>OR</em>
                                &nbsp;&nbsp;

                                <strong>Drag & Drop Suppression Files Here</strong>
                            </div>

                            <br />
                            <br />

                            <div class="well">
                                <a class="btn btn-small btn-success" ng-click="$flow.resume()">Resume</a>
                                <a class="btn btn-small btn-warning" ng-click="$flow.pause()">Pause</a>
                                <a class="btn btn-small btn-danger" ng-click="$flow.cancel()">Cancel</a>

                                <h4 class="pull-right">
                                    <span class="label label-md label-info">Total File Size: @{{$flow.getSize() | bytes }}</span>
                                    <span class="label label-md" ng-class="{ 'label-default' : !$flow.isUploading() , 'label-success' : $flow.isUploading() }">Is Uploading: @{{$flow.isUploading() ? 'Yes' : 'No' }}</span>
                                </h4>
                            </div>

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

            <button type="button" class="btn btn-success btn-md pull-right"
            ng-disabled="!supp.emailsLoaded"
            ng-click="supp.uploadSuppressions()">
                <span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : supp.emailsLoaded }"></span>
                Suppress
            </button>
        </div>
    </md-content>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/bulksuppression.js"></script>
@stop
