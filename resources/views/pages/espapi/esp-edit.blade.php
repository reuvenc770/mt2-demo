@extends( 'layout.default' )

@section( 'title' , 'Edit ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="espController as esp" ng-init="esp.loadAccount()">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit ESP API Account :: @{{esp.currentAccount.accountName}}</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form class="form-horizontal">
                        <input type="hidden" ng-model="esp.currentAccount.id" />
                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.accountName }">
                            <label for="accountName" class="col-sm-2 control-label">Account Name</label>

                            <div class="col-sm-10">
                                <input type="text" id="accountName" class="form-control" required="required"  ng-model="esp.currentAccount.accountName" />

                                <span class="help-block" ng-bind="esp.formErrors.accountName" ng-show="esp.formErrors.accountName"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key1 }">
                            <label for="key1" class="col-sm-2 control-label">Key 1</label>

                            <div class="col-sm-10">
                                <input type="text" id="key1" class="form-control" required="required" ng-model="esp.currentAccount.key1" />

                                <span class="help-block" ng-bind="esp.formErrors.key1" ng-show="esp.formErrors.key1"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key2 }">
                            <label for="key2" class="col-sm-2 control-label">Key 2</label>

                            <div class="col-sm-10">
                                <input type="text" id="key2" class="form-control"  ng-model="esp.currentAccount.key2" />

                                <span class="help-block" ng-bind="esp.formErrors.key2" ng-show="esp.formErrors.key2"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-lg btn-primary btn-block" ng-click="esp.editAccount()">Save</button>
                            </div>
                        </div>
                    </form>
                </md-card-content>
            </md-card>
        </div>
    </md-content>
@stop

@section( 'pageIncludes' )
<script src="js/espapi.js"></script>
@stop
