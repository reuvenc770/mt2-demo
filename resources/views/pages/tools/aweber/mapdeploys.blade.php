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
                <div ng-hide="weber.shouldHide({{$deploy['id']}})" class="form-group clearfix">
                    <label class="col-sm-12 control-label">{{$deploy['id']}} - {{$deploy['deploy_name']}} -  {{$deploy['send_date']}}
                    <br/>
                    <span style="font-weight:normal">{{$deploy['subject_line']}}</span>
                    </label>
                    <div class="col-sm-12">
                        <div class="input-group">
                            @if(isset($deploy['raw_reports']))
                        <select ng-model="weber.currentMappings[{{$deploy['id']}}]" class="form-control" name="deploy_id">
                            <option value="">Please Select a Possible match</option>
                            @foreach($deploy['raw_reports'] as $rawRecord)
                                <option ng-value="{{ $rawRecord['internal_id'] }}">
                                {{ $rawRecord['internal_id'] }} {{ $rawRecord['subject'] }} - {{ $rawRecord['datetime'] }}
                                </option>
                            @endforeach
                        </select>

                             <div class="input-group-btn">
                            <span class=" btn btn-primary" ng-click="weber.convertReport(weber.currentMappings[{{$deploy['id']}}] , {{$deploy['id']}} )" id="basic-addon2">Assign Deploy</span>
                                 </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/aweber/AWeberController.js',
                'resources/assets/js/aweber/AWeberService.js'],'js','pageLevel') ?>
