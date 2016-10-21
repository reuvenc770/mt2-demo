
@extends( 'bootstrap.layout.default' )
@section('title', 'Append EID')

@section('content')

    <div class="panel panel-primary" ng-controller="AppendEidController as deploy">
        <div class="panel-heading">
            <div class="panel-title">Add Mailing Template</div>
        </div>
        <div class="panel-body">
            <fieldset>
                <li flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'appendEID' , '_token' : '{{ csrf_token() }}' } }"
                    flow-files-submitted="$flow.upload()"
                    flow-file-success="deploy.fileUpload($file); $flow.cancel()" flow-btn>
                    <a href="#">Upload Deploy List</a>
                    <input type="file" style="visibility: hidden; position: absolute;"/>
                </li>
            </fieldset>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12">
                    <input class="btn btn-lg btn-primary btn-block" ng-click="append.createFile()"
                           ng-disabled="emailDomain.formSubmitted" type="submit" value="Append Information">

                </div>
            </div>

        </div>
    </div>
    <div id="loading"></div>
@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/appendeid/AppendEidController.js',
                'resources/assets/js/bootstrap/appendeid/AppendEidApiService.js',
                'resources/assets/js/bootstrap/appendeid/Loading.js'], 'js', 'pageLevel') ?>
