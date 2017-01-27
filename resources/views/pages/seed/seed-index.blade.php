@extends( 'layout.default' )

@section( 'title' , 'Seed List' )

@section ( 'angular-controller' , 'ng-controller="SeedController as seed"' )

@section( 'content' )
<div>
    <h2>Active Seeds</h2>
    <div class="form-group">
    <div class="input-group">
        <input type="text" class="form-control" ng-model="seed.email_address" placeholder="Email Address">
      <span class="input-group-btn">
        <button ng-click="seed.create()"  class="btn btn-primary" type="button">Insert Seed</button>
      </span>
    </div>
    </div>
    <ul class="list-group">
        @foreach($seeds as $seed)
        <li class="list-group-item"><a ng-click="seed.delete({{$seed->id}})" style="padding-right: 20px" href="#">Delete Seed</a>{{$seed->email_address}}</li>
        @endforeach
    </ul>
</div>
@stop

<?php Assets::add(
    ['resources/assets/js/seed/SeedController.js',
        'resources/assets/js/seed/SeedApiService.js'],'js','pageLevel') ?>
