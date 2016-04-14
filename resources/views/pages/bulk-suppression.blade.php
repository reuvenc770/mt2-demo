@extends( 'layout.default' )

@section( 'title' , 'Bulk Suppression' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Bulk Suppression</h1></div>
</div>

<div ng-controller="BulkSuppressionController as supp">
    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3"></div>

        <div class="col-xs-12 col-md-6">
            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Suppress</button>

            <div class="clearfix"></div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Bulk Suppression Options</h3>
                </div>

                <div class="panel-body">
                    <md-content flex>
                        <md-input-container class="md-block" ng-cloak>
                            <label>Emails</label>
                            <textarea ng-model="emails" rows="5" md-select-on-focus></textarea>
                        </md-input-container>


                        <md-input-container class="md-block" flex-gt-sm>
                            <label>Suppression Reason</label>
                            <md-select ng-model="reason">
                                <md-option value="">Select Reason</md-option>
                                <md-option value="C">Complaint</md-option>
                                <md-option value="B">Bounce</md-option>
                                <md-option value="ADVS">Advertiser Screamer</md-option>
                                <md-option value="I">ImpressionWise</md-option>
                                <md-option value="IPCMP">IP Provider Complaint</md-option>
                                <md-option value="LOS">List Owner Screamer</md-option>
                                <md-option value="SPAMT">Spamtrap</md-option>
                                <md-option value="">Known Seed</md-option> <!-- Need value for this!!! -->
                            </md-select>
                        </md-input-container>

                        <div flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'bulksuppression' , '_token' : '{{ csrf_token() }}' } }" flow-files-submitted="$flow.upload()">
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

                            <table class="table table-hover table-bordered table-striped" flow-transfers ng-cloak>
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">File Size</th>
                                        <th class="text-center">#Chunks</th>
                                        <th class="text-center">Progress</th>
                                        <th class="text-center">Download Status</th>
                                        <th class="text-center">Settings</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="file in transfers">
                                        <td>@{{ $index + 1 }}</td>
                                        <td>@{{ file.name }}</td>
                                        <td class="text-center">@{{ file.size | bytes }}</td>
                                        <td class="text-center">@{{ file.chunks.length }}</td>
                                        <td>
                                            <md-progress-linear class="md-warn" md-mode="determinate" ng-value="file.progress() * 100"></md-progress-linear>
                                        </td>
                                        <td class="text-center" ng-class="{ 'bg-info' : file.isUploading() , 'bg-warning' : file.paused , 'bg-danger' : file.error , 'bg-success' : !file.error }"><strong>@{{ file.isUploading() ? 'Downloading' : ( file.paused ? 'Paused': ( file.error ? 'Failed' : 'Successful' ) ) }}</strong></td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="btn btn-mini btn-warning" ng-click="file.pause()" ng-hide="file.paused">
                                                Pause
                                                </a>

                                                <a class="btn btn-mini btn-warning" ng-click="file.resume()" ng-show="file.paused">
                                                Resume
                                                </a>

                                                <a class="btn btn-mini btn-danger" ng-click="file.cancel()">
                                                Cancel
                                                </a>

                                                <a class="btn btn-mini btn-info" ng-click="file.retry()" ng-show="file.error">
                                                Retry
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </md-content>
                </div>
            </div>

            <button type="button" class="btn btn-success btn-md pull-right" ng-class="{ 'disabled' : listProfile.creatingListProfile }" ng-click="listProfile.calculateListProfile( $event )"><span class="glyphicon glyphicon-save" ng-class="{ 'rotateMe' : listProfile.creatingListProfile }"></span> Suppress</button>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/bulksuppression.js"></script>
@stop
