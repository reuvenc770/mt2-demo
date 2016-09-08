@extends( 'layout.default' )

@section( 'title' , 'Add ESP' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="espController as esp">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Add ESP API Account</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form class="form-horizontal">
                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.espId }">
                            <label for="espId" class="col-sm-2 control-label">ESP Name</label>

                            <div class="col-sm-10">
                                <select id="espId" class="form-control" required="required"
                                        ng-model="esp.currentAccount.espId">
                                    <option value="">Choose ESP</option>
                                    @foreach( $espList as $espId => $esp )
                                        <option value="{{ $espId }}">{{ $esp }}</option>
                                    @endforeach
                                </select>

                                <span class="help-block" ng-bind="esp.formErrors.espId"
                                      ng-show="esp.formErrors.espId"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.accountName }">
                            <label for="accountName" class="col-sm-2 control-label">Account Name</label>

                            <div class="col-sm-10">
                                <input type="text" id="accountName" class="form-control" required="required"
                                       ng-model="esp.currentAccount.accountName" value=""/>

                                <span class="help-block" ng-bind="esp.formErrors.accountName"
                                      ng-show="esp.formErrors.accountName"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key1 }">
                            <label for="" class="col-sm-2 control-label">Key 1</label>

                            <div class="col-sm-10">
                                <input type="text" id="key1" class="form-control" required="required"
                                       ng-model="esp.currentAccount.key1" value=""/>

                                <span class="help-block" ng-bind="esp.formErrors.key1" ng-show="esp.formErrors.key1"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : esp.formErrors.key2 }">
                            <label for="" class="col-sm-2 control-label">Key 2</label>

                            <div class="col-sm-10">
                                <input type="text" id="key2" class="form-control" ng-model="esp.currentAccount.key2"
                                       value=""/>

                                <span class="help-block" ng-bind="esp.formErrors.key2" ng-show="esp.formErrors.key2"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-lg btn-primary btn-block" ng-click="esp.saveNewAccount()">Save
                                </button>
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
