@extends( 'layout.default' )

@section( 'title' , 'Add YMLP Campaign' )

@section( 'navEspClasses' , 'active' )

@section( 'content' )
    <md-content layout="row" layout-align="center center" class="md-mt2-zeta-theme md-hue-1">
        <div flex-gt-sm="50" flex="100">
            <md-card ng-controller="ymlpCampaignController as ymlp">
                <md-toolbar class="md-hue-3">
                    <div class="md-toolbar-tools">
                        <span>Create YMLP Campaign</span>
                    </div>
                </md-toolbar>
                <md-card-content>
                    <form class="form-horizontal">
                        <div class="form-group" ng-class="{ 'has-error' : ymlp.formErrors.esp_account_id }">
                            <label for="esp_account_id" class="col-sm-2 control-label">ESP Account</label>

                            <div class="col-sm-10">
                                <select id="esp_account_id" name="esp_account_id"class="form-control" required="required"
                                        ng-model="ymlp.currentCampaign.esp_account_id">
                                    <option value="">Choose Esp Account</option>
                                    @foreach ($espAccounts as $esp)
                                        <option value="{{$esp->id}}">{{$esp->account_name}}</option>
                                    @endforeach
                                </select>

                            <span class="help-block" ng-bind="ymlp.formErrors.esp_account_id"
                                  ng-show="ymlp.formErrors.esp_account_id"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : ymlp.formErrors.sub_id }">
                            <label for="name" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-10">
                                <input type="text" id="name" class="form-control" required="required"
                                       ng-model="ymlp.currentCampaign.sub_id" value=""/>

                            <span class="help-block" ng-bind="ymlp.formErrors.sub_id"
                                  ng-show="ymlp.formErrors.sub_id"></span>
                            </div>
                        </div>

                        <div class="form-group" ng-class="{ 'has-error' : ymlp.formErrors.date }">
                            <label for="" class="col-sm-2 control-label">Date</label>

                            <div class="col-sm-10">
                                <input type="text" id="date" class="form-control" required="required"
                                       ng-model="ymlp.currentCampaign.date" value=""/>

                                <span class="help-block" ng-bind="ymlp.formErrors.date"
                                      ng-show="ymlp.formErrors.date"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="button" class="btn btn-lg btn-primary btn-block"
                                        ng-click="ymlp.saveNewCampaign()">Save
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
    <script src="js/ymlpcampaign.js"></script>
@stop
