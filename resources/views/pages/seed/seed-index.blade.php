@extends( 'layout.default' )

@section( 'title' , 'Seed List' )

@section ( 'angular-controller' , 'ng-controller="SeedController as seed"' )

@section( 'content' )
<div class="panel mt2-theme-panel">
    <div class="panel-heading">
        <div class="panel-title">Seed List</div>
    </div>
    <div class="panel-body">
        <div class="form-group" ng-class="{ 'has-error' : seed.formErrors.email_address }">
            <div class="input-group">
                <input type="text" class="form-control" ng-model="seed.email_address" placeholder="Email Address">
              <span class="input-group-btn">
                <button ng-click="seed.create()" class="btn mt2-theme-btn-primary" type="button">Add Seed</button>
              </span>
            </div>
            <div class="help-block" ng-show="seed.formErrors.email_address">
                <div ng-repeat="error in seed.formErrors.email_address">
                    <span ng-bind="error"></span>
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">Active Seeds</div>
            <ul class="list-group">
                @foreach($seeds as $seed)
                <li class="list-group-item cmp-list-item-condensed"><a ng-click="seed.delete({{$seed->id}})" style="padding-right: 20px" href="#">Delete</a>{{$seed->email_address}}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@stop

<?php Assets::add(
    ['resources/assets/js/seed/SeedController.js',
        'resources/assets/js/seed/SeedApiService.js'],'js','pageLevel') ?>
