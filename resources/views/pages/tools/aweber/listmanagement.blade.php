@extends( 'layout.default' )


@section( 'title' , 'AWeber List Management' )


@section( 'angular-controller', 'ng-controller="AWeberController as weber"' )

@section( 'page-menu' )

@stop

@section( 'content' )
    <div class="panel mt2-theme-panel"  >
        <div class="panel-heading">
            <div class="panel-title">Map Lists</div>
        </div>
        <div class="panel-body">
            @foreach($espAccounts as $account)
              <div ng-init="weber.getLists({{$account->id}})"><h3>{{$account->account_name}}</h3>
                  <lite-membership-widget recordlist="weber.lists[{{$account->id}}]['active']" chosenrecordlist="weber.lists[{{$account->id}}]['deactive']"
                                          availablerecordtitle="weber.availableWidgetTitle"
                                          chosenrecordtitle="weber.chosenWidgetTitle" idfield="weber.idField"
                                          namefield="weber.nameField"
                                          height="200"
                                          updatecallback="weber.inactiveToggle({{$account->id}})"></lite-membership-widget></div>
            @endforeach
        </div>
        <div class="panel-footer">
            <div class="row">
            <div class="col-md-offset-4 col-md-4">
              <input class="btn btn-block mt2-theme-btn-primary" ng-click="weber.updateLists()" value="Update Mailing List Status">
            </div>
            </div>
        </div>
    </div>
@stop

<?php Assets::add(
        ['resources/assets/js/aweber/AWeberController.js',
                'resources/assets/js/aweber/AWeberService.js'],'js','pageLevel') ?>
