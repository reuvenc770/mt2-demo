@extends( 'bootstrap.layout.default' )


@section( 'title' , 'Map aWeber Deloys' )


@section( 'angular-controller', 'ng-controller="AWeberController as weber"' )

@section( 'page-menu' )

@stop

@section( 'content' )
    <div class="panel mt2-theme-panel" ng-init="weber.loadReports()" >
        <div class="panel-heading">
            <div class="panel-title">Map Aweber Deploys</div>
        </div>
        <div class="panel-body">
                @foreach($deploys as $deploy)
                <div class="form-group clearfix">
                    <label class="col-sm-12 control-label">{{$deploy->id}} - {{$deploy->deploy_name}} -  {{$deploy->send_date}} - {{$deploy->subject_line}}</label>
                    <div class="col-sm-12">
                        <select ng-model="weber.currentMappings" class="form-control" name="deploy_id">
                            <option value="">Please Select a Possible match</option>
                            <option ng-repeat="record in weber.reports |  filter:{esp_account_id:{{$deploy->esp_account_id}}}">
                                @{{ record.subject }} - @{{ app.formatDate(record.datetime,"MM-DD-YYYY") }}
                                </option>
                        </select>
                    </div>
                </div>
                @endforeach
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/bootstrap/aweber/AWeberController.js',
                'resources/assets/js/bootstrap/aweber/AWeberService.js'],'js','pageLevel') ?>
