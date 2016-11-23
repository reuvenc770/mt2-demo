@extends( 'bootstrap.layout.default' )

@section( 'title' , 'Bulk Suppression' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-controller="BulkSuppressionController as supp">
    <div class="panel-heading">
        <div class="panel-title">Bulk Suppression Options</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label>Emails</label>
            <textarea class="form-control" rows="5" ng-model="supp.emailString" ng-change="supp.enableSubmission()"></textarea>
        </div>
        <div class="form-group" ng-class="{ 'has-error' : supp.formErrors.suppressionReasonCode }">
            <select name="reason" class="form-control" ng-model="supp.suppressionReasonCode" ng-init="supp.loadReasons()" required>
                <option value="">Suppression Reason</option>
                <option ng-repeat="reason in supp.suppressionReasons" ng-value="reason.value">@{{ reason.name }}</option>
            </select>
            <div class="help-block" ng-show="supp.formErrors.suppressionReasonCode">
                <div ng-repeat="error in supp.formErrors.suppressionReasonCode">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>

        <div flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'bulksuppression' , '_token' : '{{ csrf_token() }}' } }"
             flow-files-submitted="$flow.upload()"
             flow-file-success="supp.startTransfer($file)">
            <div flow-drop class="dropFile" flow-drag-enter="style={border:'2px solid green'}" flow-drag-leave="style={}" ng-style="style">
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
                <table md-table ng-cloak>
                    <thead md-head class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="md-table-header-override-whitetext">#</th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                            <th md-column class="md-table-header-override-whitetext" md-numeric>File Size</th>
                            <th md-column class="md-table-header-override-whitetext">#Chunks</th>
                            <th md-column class="md-table-header-override-whitetext">Progress</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Download Status</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center mt2-table-btn-column">Settings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr md-row ng-repeat="file in transfers">
                            <td md-cell ng-bind="$index + 1"></td>
                            <td md-cell ng-bind="file.name"></td>
                            <td md-cell ng-bind="file.size | bytes"></td>
                            <td md-cell ng-bind="file.chunks.length"></td>
                            <td md-cell>
                                <md-progress-linear class="md-warn" md-mode="determinate" ng-value="file.progress() * 100"></md-progress-linear>
                            </td>
                            <td md-cell class="mt2-table-cell-center"
                                ng-class="{ 'bg-info' : file.isUploading() , 'bg-warning' : file.paused , 'bg-danger' : file.error , 'bg-success' : !file.error }"
                                ng-bind="file.isUploading() ? 'Downloading' : ( file.paused ? 'Paused': ( file.error ? 'Failed' : 'Successful' ) )">
                            </td>
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <md-icon ng-click="file.pause()" ng-hide="file.paused" aria-label="Pause" data-toggle="tooltip" data-placement="bottom" title="Pause" md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                                    <md-icon ng-click="file.resume()" ng-show="file.paused" aria-label="Resume" data-toggle="tooltip" data-placement="bottom" title="Resume" md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                                    <md-icon ng-click="file.cancel()" aria-label="Cancel" data-toggle="tooltip" data-placement="bottom" title="Cancel" md-font-set="material-icons" class="mt2-icon-black">clear</md-icon>
                                    <md-icon ng-click="file.retry()" ng-show="file.error" aria-label="Retry" data-toggle="tooltip" data-placement="bottom" title="Retry" md-font-set="material-icons" class="mt2-icon-black">refresh</md-icon>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>
        </div>
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <input class="btn mt2-theme-btn-primary btn-block" ng-disabled="!supp.emailsLoaded" type="button" ng-click="supp.uploadSuppressions()" value="Suppress">
        </div>
    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/pages/BulkSuppressionController.js',
                'resources/assets/js/bootstrap/pages/BulkSuppressionApiService.js'],'js','pageLevel') ?>
