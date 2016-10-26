
@extends( 'bootstrap.layout.default' )
@section('title', 'Append EID')

@section('content')

    <div class="panel panel-primary" ng-controller="AppendEidController as append">
        <div class="panel-heading">
            <div class="panel-title">Add Mailing Template</div>
        </div>
        <div class="panel-body">
            <fieldset>

                <div class="btn-group" flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'appendEID' , '_token' : '{{ csrf_token() }}' } }"
                    flow-files-submitted="$flow.upload()"
                    flow-file-success="append.unlockButtonLoadFile($file); $flow.cancel()" flow-btn>
                    <a  class="btn btn-primary" href="#">Upload Deploy List</a>
                    <input type="file" style="visibility: hidden; position: absolute;"/>
                </div>
                <div class="btn-group">
                    <button ng-click="append.createFile()" ng-disable="append.formSubmitted" ng-class="{ 'btn-success' : append.file != '' , 'btn-danger' : append.file == '' }"
                            class="btn">@{{ append.text }}</button>
                </div>

            </fieldset>
        </div>

    </div>
    <div id="loading"></div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/appendeid/AppendEidController.js',
                'resources/assets/js/bootstrap/appendeid/AppendEidApiService.js'
        ], 'js', 'pageLevel') ?>
