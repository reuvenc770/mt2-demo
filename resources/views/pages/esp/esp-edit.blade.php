@extends( 'layout.default' )

@section( 'title' , 'Edit ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="espController as esp" ng-init="esp.loadAccount()">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Edit ESP Account :: @{{esp.currentAccount.name}}</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form class="form-horizontal">
                        <input type="hidden" ng-model="esp.currentAccount.id" />
                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.name }">
                            <label for="accountName" class="col-sm-2 control-label">Esp Name</label>

                            <div class="col-sm-10">
                                <input type="text" id="name" class="form-control" required="required" disabled  ng-model="esp.currentAccount.name" />

                                <span class="help-block" ng-bind="esp.formErrors.name" ng-show="esp.formErrors.name"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.email_id_field }">
                            <label for="key1" class="col-sm-2 control-label">Email Id Field</label>

                            <div class="col-sm-10">
                                <input type="text" id="email_id_field" class="form-control" required="required" ng-model="esp.currentAccount.email_id_field" />

                                <span class="help-block" ng-bind="esp.formErrors.email_id_field" ng-show="esp.formErrors.email_id_field"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.email_address_field }">
                            <label for="key2" class="col-sm-2 control-label">Email Address Field</label>

                            <div class="col-sm-10">
                                <input type="text" id="email_address_field" class="form-control"  ng-model="esp.currentAccount.email_address_field" />

                                <span class="help-block" ng-bind="esp.formErrors.email_address_field" ng-show="esp.formErrors.email_address_field"></span>
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
<script src="js/esp.js"></script>
@stop
