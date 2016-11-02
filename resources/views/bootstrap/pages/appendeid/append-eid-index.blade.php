
@extends( 'bootstrap.layout.default' )
@section('title', 'Append EID')

@section('content')

    <div class="panel panel-primary" ng-controller="AppendEidController as append">
        <div class="panel-heading">
            <div class="panel-title">Append EID</div>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="form-group" >
                    <input placeholder="Email Address" value="" class="form-control" ng-model="append.email" required="required"
                           name="email" type="email">
                </div>
                <div class="form-group">
                    <md-switch ng-model="append.feed" aria-label="Include Current Feed Name?">
                        Include Current Feed Name?
                    </md-switch>
                    <md-switch ng-model="append.fields" aria-label=" Include Email Information">
                        Append all fields
                    </md-switch>
                    <md-switch ng-model="append.suppress" aria-label=" Include Suppressed Records">
                        Include inactive/suppressed records
                    </md-switch>
                </div>
                <div class="btn-group" flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'appendEID' , '_token' : '{{ csrf_token() }}' } }"
                    flow-files-submitted="$flow.upload()"
                    flow-file-success="append.unlockButtonLoadFile($file); $flow.cancel()" flow-btn>
                    <a  class="btn btn-primary" href="#">Upload EID List</a>
                    <input type="file" style="visibility: hidden; position: absolute;"/>
                </div>
                <div class="btn-group">
                    <button ng-click="append.createFile()" ng-disabled="append.formSubmitted" ng-class="{ 'btn-success' : append.file != '' , 'btn-danger' : append.file == '' }"
                            class="btn">@{{ append.text }}</button>
                </div>
            </fieldset>
        </div>

    </div>

@endsection

<?php Assets::add(
        ['resources/assets/js/bootstrap/appendeid/AppendEidController.js',
                'resources/assets/js/bootstrap/appendeid/AppendEidApiService.js'
        ], 'js', 'pageLevel') ?>
