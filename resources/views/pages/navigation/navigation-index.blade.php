@extends( 'layout.default' )

@section( 'title' , 'Manage Navigation' )

@section( 'angular-controller' , 'ng-controller="NavigationController as navController"')
@section( 'cacheTag' , 'navigation-bootstrap' )


@section( 'content' )
    <div class="panel mt2-theme-panel" ng-init="navController.loadNavigation()">
        <div class="panel-heading">
            <div class="panel-title">Modify Navigation</div>
        </div>
        <div class="panel-body">
            <div class="col-sm-6"><h2>Unused Nav Items</h2>
                <ul class="list-group" dnd-list="navController.orphans"
                    dnd-allowed-types="['itemType']">
                    <li class="list-group-item" ng-repeat="item in navController.orphans"
                        dnd-draggable="item"
                        dnd-type="'itemType'"
                        dnd-effect-allowed="move"
                        dnd-moved="navController.orphans.splice($index, 1)">
                        @{{ item.name }}
                    </li>
                </ul>
            </div>
            <div class="col-sm-6">
            <ul class="list-unstyled" dnd-list="navController.navigation"
                dnd-allowed-types="['containerType']">
                <li ng-repeat="items in navController.navigation"
                    dnd-draggable="items"
                    dnd-type="'containerType'"
                    dnd-effect-allowed="move"
                    dnd-moved="navController.navigation.splice($index, 1)">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">@{{ items.name }}</div>
                        </div>
                        <div class="panel-body">
                            <ul class="list-group"  style="min-height:100px" dnd-list="items.childrenItems"
                                dnd-allowed-types="['itemType']"
                                dnd-horizontal-list="true">
                                <li class="list-group-item" ng-repeat="item in items.childrenItems"
                                    dnd-draggable="item"
                                    dnd-type="'itemType'"
                                    dnd-effect-allowed="move"
                                    dnd-moved="items.childrenItems.splice($index, 1)">
                                    @{{item.name}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
            </div>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input class="btn btn-block mt2-theme-btn-primary" ng-disabled="navController.formSubmitted" ng-click="navController.updateNavigation()" type="submit" value="Update Navigation">
            </div>
        </div>
    </div>




@endsection
<?php
Assets::add([
        'resources/assets/js/navigation/NavigationController.js',
        'resources/assets/js/navigation/NavigationApiService.js',
], 'js', 'pageLevel');
?>