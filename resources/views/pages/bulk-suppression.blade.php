@extends( 'layout.default' )

@section( 'title' , 'Bulk Suppression' )

@section( 'content' )
<div class="panel mt2-theme-panel" ng-controller="BulkSuppressionController as supp">
    <div class="panel-heading">
        <div class="panel-title">Bulk Suppression Options</div>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs" role="tablist">
            <li role="options" class="active"><a href="#manual-tab" role="tab" data-toggle="tab">Enter Emails</a></li>
            <li role="options"><a href="#upload-file-tab" role="tab" data-toggle="tab">Upload File</a></li>
        </ul>

        <div role="tabpanel" class="tab-content tabpanel-border">
            <div class="tab-pane active" id="manual-tab">

                <div class="form-group">
                    <p>Manually enter emails in - separated by commas or one email per line. Select a suppression reason for all listed emails and hit 'Suppress'.</p>
                    <label>Emails</label>
                    <textarea class="form-control" rows="5" ng-model="supp.emailString" ng-change="supp.enableSubmission()"></textarea>
                </div>
                <div class="form-group" ng-class="{ 'has-error' : supp.formErrors.suppressionReasonCode }">
                    <select name="reason" class="form-control" ng-model="supp.suppressionReasonCode" ng-init="supp.loadReasons()" required ng-options="reason.value.toString() as reason.name for reason in supp.suppressionReasons">
                        <option value="">Suppression Reason</option>

                    </select>
                    <div class="help-block" ng-show="supp.formErrors.suppressionReasonCode">
                        <div ng-repeat="error in supp.formErrors.suppressionReasonCode">
                            <span ng-bind="error"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-offset-4 col-md-4">
                    <input class="btn mt2-theme-btn-primary btn-block " ng-disabled="!supp.emailsLoaded" type="button" ng-click="supp.uploadSuppressions()" value="Suppress">
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="upload-file-tab">
                <p>To suppress emails by uploading a file list of emails, select a suppression reason before uploading the file(s). A file is completed once the progress bar is green and will go through process of suppression. If an individual file is canceled during the upload, progress will not be saved.</p>
                <p><span class="bold-text">Note:</span> 1 or more files can be uploaded at the same time but the suppression reason selected will be applied to all emails that are uploaded together.</p>
                <br/>

                <div class="row">
                <div class="form-group col-md-6" ng-class="{ 'has-error' : supp.formErrors.suppressionReasonCode }">
                    <select name="reason" class="form-control" ng-model="supp.suppressionReasonCode" ng-init="supp.loadReasons()" ng-options="reason.value.toString() as reason.name for reason in supp.suppressionReasons" required>
                        <option value="">Suppression Reason</option>
                    </select>
                    <div class="help-block" ng-show="supp.formErrors.suppressionReasonCode">
                        <div ng-repeat="error in supp.formErrors.suppressionReasonCode">
                            <span ng-bind="error"></span>
                        </div>
                    </div>
                </div>
                </div>

        <div flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'bulksuppression' , '_token' : '{{ csrf_token() }}' } }"
             flow-files-submitted="$flow.upload()"
             flow-file-success="supp.startTransfer($file)"
             flow-complete="supp.flowCompleteCallback()">

        <div class="row">
            <div class="col-md-6">
                <div flow-drop flow-prevent-drop class="dropFile text-center" flow-drag-enter="style={background:'#f1f1f1'}" flow-drag-leave="style={}" ng-style="style" flow-drop-enabled="supp.suppressionReasonCode">

                    <div ng-show="!supp.suppressionReasonCode">
                        <span class="bold-text warning-text">*A suppression reason is required before uploading a file.</span>
                        <br/><br/>
                    </div>
                    <div ng-show="supp.suppressionReasonCode">
                        <span class="bold-text">Drag & Drop Suppression Files Here</span>
                        <br/>
                        <span class="italic">OR</span>
                    </div>
                    <button class="btn btn-default" flow-btn ng-disabled="!supp.suppressionReasonCode">
                        Upload Suppression Files
                        <input type="file" style="visibility: hidden; position: absolute;"/>
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-center" ng-show="$flow.files.length > 0">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p class="bold-text">For remaining incompleted files:</p>
                        <a class="btn btn-small btn-success" ng-click="$flow.resume()">Resume</a>
                        <a class="btn btn-small btn-warning" ng-click="$flow.pause()">Pause</a>
                        <a class="btn btn-small btn-danger" ng-click="$flow.cancel()">Cancel</a>
                    </div>
                    <div class="panel-footer">Total File Size: @{{$flow.getSize() | bytes }}</div>
                </div>
            </div>
        </div>
        <br/>
            <md-table-container>
                <table md-table ng-cloak>
                    <thead md-head class="mt2-theme-thead">
                        <tr md-row>
                            <th md-column class="md-table-header-override-whitetext">#</th>
                            <th md-column class="md-table-header-override-whitetext">Name</th>
                            <th md-column class="md-table-header-override-whitetext" md-numeric>File Size</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center">Progress</th>
                            <th md-column class="md-table-header-override-whitetext mt2-table-header-center mt2-table-btn-column"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-show="$flow.files.length < 1">
                            <td md-cell colspan="5"><h3 class="text-center"><small>No files uploaded.</small></h3></td>
                        </tr>
                        <tr md-row ng-repeat="file in $flow.files">
                            <td md-cell ng-bind="$index + 1"></td>
                            <td md-cell ng-bind="file.name"></td>
                            <td md-cell ng-bind="file.size | bytes"></td>
                            <td md-cell class="mt2-table-cell-center">
                                <md-progress-linear md-mode="determinate" ng-value="file.progress() * 100"
                                    ng-class="{ 'cmp-md-success' : ( file.isComplete() || file.isUploading() ), 'md-warn' : !file.isUploading() }"></md-progress-linear>
                            </td>
                            <td md-cell class="mt2-table-btn-column">
                                <div layout="row" layout-align="center center">
                                    <md-icon ng-click="file.pause()" ng-show="!file.paused && !file.isComplete()" aria-label="Pause" data-toggle="tooltip" data-placement="bottom" title="Pause" md-font-set="material-icons" class="mt2-icon-black">pause</md-icon>
                                    <md-icon ng-click="file.resume()" ng-show="file.paused && !file.isComplete()" aria-label="Resume" data-toggle="tooltip" data-placement="bottom" title="Resume" md-font-set="material-icons" class="mt2-icon-black">play_arrow</md-icon>
                                    <md-icon ng-click="file.cancel()" aria-label="Cancel" data-toggle="tooltip" data-placement="bottom" title="Cancel" md-font-set="material-icons" class="mt2-icon-black" ng-show="!file.isComplete()">clear</md-icon>

                                    <md-icon ng-click="file.retry()" aria-label="Retry" data-toggle="tooltip" data-placement="bottom" title="Retry" md-font-set="material-icons" class="mt2-icon-black" ng-show="file.error">refresh</md-icon>
                                    <md-icon aria-label="Error" data-toggle="tooltip" data-placement="bottom" title="Error" md-font-set="material-icons" class="mt2-icon-error" ng-show="file.error">error</md-icon>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>
        </div>
            </div>
        </div>

    </div>
</div>
@stop

<?php Assets::add(
        ['resources/assets/js/pages/BulkSuppressionController.js',
                'resources/assets/js/pages/BulkSuppressionApiService.js'],'js','pageLevel') ?>
