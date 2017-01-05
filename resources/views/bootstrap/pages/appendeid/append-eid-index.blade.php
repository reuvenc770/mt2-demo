
@extends( 'layout.default' )
@section('title', 'Append EID')

@section('content')

    <div class="panel mt2-theme-panel" ng-controller="AppendEidController as append">
        <div class="panel-heading">
            <div class="panel-title">Append EID</div>
        </div>
        <div class="panel-body">
            <p>To get information associated to an EID, upload EID list file and select the information you would like returned. If nothing is selected, only email address will be returned. Output file will be saved to FTP.</p>
            <br/>
            <fieldset>
                <div layout="column">
                    <md-checkbox ng-model="append.feed" aria-label="Include current feed name.">
                        Include current feed name.
                    </md-checkbox>
                    <md-checkbox ng-model="append.fields" aria-label="Include email information">
                        Include all data fields.
                        <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="If box is checked, all email information for each EID record will be included (ie: name, address, gender, etc).">help</md-icon>
                    </md-checkbox>
                    <md-checkbox ng-model="append.suppress" aria-label=" Include suppressed records">
                        Include inactive/suppressed records.
                        <md-icon md-font-set="material-icons" class="mt2-icon-black material-icons icon-xs cmp-tooltip-marker" data-toggle="popover" data-placement="right" data-content="If the uploaded EID list has inactive/suppressed records and this is checked, the return file will include these records. If unchecked, inactive/suppressed records will be removed. ">help</md-icon>
                    </md-checkbox>
                </div>
                <div class="btn-group" flow-init="{ target : 'api/attachment/upload' , query : { 'fromPage' : 'appendEID' , '_token' : '{{ csrf_token() }}' } }"
                    flow-files-submitted="$flow.upload()"
                    flow-file-success="append.unlockButtonLoadFile($file); $flow.cancel()" flow-btn>
                    <a  class="btn mt2-theme-btn-primary" href="#">Upload EID List</a>
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
