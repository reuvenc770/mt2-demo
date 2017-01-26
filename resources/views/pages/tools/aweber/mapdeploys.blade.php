@extends( 'layout.default' )


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
                    <label class="col-sm-12 control-label">{{$deploy->id}} - {{$deploy->deploy_name}} -  {{$deploy->send_date}}
                    <br/>
                    <span style="font-weight:normal">{{$deploy->subject_line}}</span>
                    </label>
                    <div class="col-sm-12">
                        <div class="input-group">
                        <select ng-model="weber.currentMappings[{{$deploy->id}}]" class="form-control" name="deploy_id">
                            <option value="">Please Select a Possible match</option>
                            @foreach($rawReports as $rawRecord)
                                @if( $deploy->esp_account_id == $rawRecord['esp_account_id'] )
                                <option ng-value="{{ $rawRecord['internal_id'] }}">
                                {{ $rawRecord['internal_id'] }} {{ $rawRecord['subject'] }} - {{ $rawRecord['datetime'] }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                             <div class="input-group-btn">
                            <span class=" btn btn-primary" ng-click="weber.convertReport(weber.currentMappings[{{$deploy->id}}] , {{$deploy->id}} )" id="basic-addon2">Assign Deploy</span>
                                 </div>
                            </div>
                    </div>
                </div>
                @endforeach
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/aweber/AWeberController.js',
                'resources/assets/js/aweber/AWeberService.js'],'js','pageLevel') ?>
