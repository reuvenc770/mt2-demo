@extends( 'layout.default' )

@section( 'title' , 'Client Atribution' )

@section( 'navClientClasses' , 'active' )

@section( 'content' )
<div class="row">
    <div class="page-header col-xs-12"><h1 class="text-center">Attribution</h1></div>
</div>

<div ng-controller="ClientAttributionController as clientAttr" ng-init="clientAttr.loadClients()">
    <div class="row">
        <div class="hidden-xs col-md-2"></div>
        <div class="panel panel-default col-xs-12 col-md-8" style="padding-top: 20px;">
            <div flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'attribution' , '_token' : '{{ csrf_token() }}' } }" flow-files-submitted="$flow.upload()">
                <div flow-drop class="dropFile" flow-drag-enter="style={border:'4px solid green'}" flow-drag-leave="style={}" ng-style="style">
                    <span class="btn btn-xs btn-default" flow-btn>
                        Upload Suppression Files
                        <input type="file" style="visibility: hidden; position: absolute;" />
                    </span>

                    &nbsp;&nbsp;
                    <em>OR</em>
                    &nbsp;&nbsp;

                    <strong>Drag & Drop Attribution Files Here</strong>
                </div>

                <br />
                <br />

                <div class="well">
                    <a class="btn btn-xs btn-success" ng-click="$flow.resume()">Resume</a>
                    <a class="btn btn-xs btn-warning" ng-click="$flow.pause()">Pause</a>
                    <a class="btn btn-xs btn-danger" ng-click="$flow.cancel()">Cancel</a>

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
                                    <a class="btn btn-xs btn-warning" ng-click="file.pause()" ng-hide="file.paused">
                                    Pause
                                    </a>

                                    <a class="btn btn-xs btn-warning" ng-click="file.resume()" ng-show="file.paused">
                                    Resume
                                    </a>

                                    <a class="btn btn-xs btn-danger" ng-click="file.cancel()">
                                    Cancel
                                    </a>

                                    <a class="btn btn-xs btn-info" ng-click="file.retry()" ng-show="file.error">
                                    Retry
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="clientAttr.paginationCount" currentpage="clientAttr.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="clientAttr.currentPage" maxpage="clientAttr.pageCount" disableceiling="clientAttr.reachedMaxPage" disablefloor="clientAttr.reachedFirstPage"></pagination>
                </div>
            </div>

            <clientattribution-table records="clientAttr.clients" loadingflag="clientAttr.currentlyLoading" deleteclient="clientAttr.deleteAttribution( id )" setclient="clientAttr.setAttribution( id , level )" ></clientattribution-table>

            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1">
                    <pagination-count recordcount="clientAttr.paginationCount" currentpage="clientAttr.currentPage"></pagination-count>
                </div>

                <div class="col-xs-9 col-sm-10 col-md-10 col-lg-11">
                    <pagination currentpage="clientAttr.currentPage" maxpage="clientAttr.pageCount" disableceiling="clientAttr.reachedMaxPage" disablefloor="clientAttr.reachedFirstPage"></pagination>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section( 'pageIncludes' )
<script src="js/clientAttribution.js"></script>
@stop
